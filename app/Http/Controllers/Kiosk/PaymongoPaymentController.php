<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymongoRequest;
use App\Models\Payment;
use App\Models\StudentBalance;
use App\Services\Kiosk\PaymentSettlementService;
use App\Services\Kiosk\ReceiptService;
use App\Services\Paymongo\PaymongoClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymongoPaymentController extends Controller
{
    public function initiate(
        PaymongoRequest $request,
        PaymongoClient $paymongoClient,
        PaymentSettlementService $paymentSettlementService
    ) {
        $validated = $request->validated();

        $context = $validated['context'];
        $payAll = $context === 'dynamic' && (bool) ($validated['pay_all'] ?? false);
        $balanceId = $context === 'dynamic' && isset($validated['balance_id'])
            ? (int) $validated['balance_id']
            : null;
        $useOverpayment = (bool) ($validated['use_overpayment'] ?? false);
        $paymongoMethod = (string) ($validated['paymongo_method'] ?? 'qrph');

        if ($context === 'dynamic' && !$payAll && !$balanceId) {
            return back()->with('error', 'Please select a balance before using PayMongo.');
        }

        $initiationData = $this->resolveInitiationData($context, $balanceId, $payAll);
        $balances = $initiationData['balances'];

        if ($balances->isEmpty()) {
            return back()->with('error', 'No outstanding balance found for PayMongo payment.');
        }

        $totalDue = $balances->sum(
            fn(StudentBalance $balance) => max((float) $balance->total_amount - (float) $balance->paid_amount, 0)
        );

        if ($totalDue <= 0) {
            return back()->with('error', 'All selected balances are already settled.');
        }

        $user = $request->user()->loadMissing('information:id,first_name,last_name,user_id');
        $transactionId = Str::uuid7()->toString();

        $overpaymentUsed = 0;
        if ($useOverpayment) {
            $overpaymentUsed = min((float) ($user->over_payment ?? 0), $totalDue);
        }

        $gatewayAmount = round(max($totalDue - $overpaymentUsed, 0), 2);

        $payment = Payment::query()->create([
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'status' => 'awaiting_payment',
            'amount_paid' => $gatewayAmount,
            'student_balance_id' => $initiationData['primary_balance_id'],
            'payment_channel' => 'paymongo',
            'payment_context' => $context,
            'pay_all' => $payAll,
            'use_overpayment' => $useOverpayment,
            'gateway_method' => $paymongoMethod,
        ]);

        session([
            'transaction_id' => $transactionId,
        ]);

        if ($gatewayAmount <= 0) {
            $settlement = $paymentSettlementService->completePayment($payment, 0, [
                'reference_no' => 'PM-' . strtoupper(Str::random(10)),
                'gateway_status' => 'succeeded',
            ]);

            session()->put("current_overpayment_{$transactionId}", $settlement['current_overpayment']);
            session()->put("overpayment_used_{$transactionId}", $settlement['overpayment_used']);

            return redirect()->route($this->receiptRouteName($payment), [
                'transaction_id' => $transactionId,
            ])->with('success', 'Payment completed using your available overpayment.');
        }

        try {
            $description = $initiationData['description'];
            $intent = $paymongoClient->createPaymentIntent(
                (int) round($gatewayAmount * 100),
                $description,
                [
                    'local_transaction_id' => $transactionId,
                    'user_id' => (string) $user->id,
                ]
            );

            $intentId = (string) data_get($intent, 'data.id');
            if ($intentId === '') {
                throw new \RuntimeException('PayMongo payment intent ID was not returned.');
            }

            $billingName = trim(
                ($user->information?->first_name ?? '') . ' ' . ($user->information?->last_name ?? '')
            );
            $billingName = $billingName !== '' ? $billingName : "Student {$user->student_id}";

            $paymentMethod = $paymongoClient->createPaymentMethod(
                $paymongoMethod,
                $billingName,
                $user->email,
                [
                    'local_transaction_id' => $transactionId,
                ],
                route('kiosk.paymongo.return', [
                    'transaction_id' => $transactionId,
                    'result' => 'success',
                ]),
                route('kiosk.paymongo.return', [
                    'transaction_id' => $transactionId,
                    'result' => 'failed',
                ])
            );

            $paymentMethodId = (string) data_get($paymentMethod, 'data.id');
            if ($paymentMethodId === '') {
                throw new \RuntimeException('PayMongo payment method ID was not returned.');
            }

            $attachedIntent = $paymongoClient->attachPaymentIntent(
                $intentId,
                $paymentMethodId,
                route('kiosk.paymongo.return', ['transaction_id' => $transactionId])
            );

            $intentData = data_get($attachedIntent, 'data', []);
            $payment->update([
                'gateway_reference_id' => (string) data_get($intentData, 'id', $intentId),
                'gateway_status' => (string) data_get($intentData, 'attributes.status', 'awaiting_next_action'),
                'gateway_qr_code' => data_get($intentData, 'attributes.next_action.code.image_url'),
                'gateway_response' => $intentData,
            ]);

            return redirect()->route('kiosk.paymongo.checkout', [
                'transaction_id' => $transactionId,
            ]);
        } catch (RequestException $exception) {
            $reason = $this->extractPaymongoError($exception);
            $payment->update([
                'status' => 'failed',
                'gateway_status' => 'failed',
                'failed_reason' => $reason,
            ]);

            return back()->with('error', "Unable to initialize PayMongo payment: {$reason}");
        } catch (\RuntimeException $exception) {
            $reason = $exception->getMessage();
            $payment->update([
                'status' => 'failed',
                'gateway_status' => 'failed',
                'failed_reason' => $reason,
            ]);

            return back()->with('error', "Unable to initialize PayMongo payment: {$reason}");
        }
    }

    public function checkout(
        string $transaction_id,
        PaymongoClient $paymongoClient,
        PaymentSettlementService $paymentSettlementService
    ) {
        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->where('payment_channel', 'paymongo')
            ->firstOrFail();

        try {
            $this->syncPaymentIntent($payment, $paymongoClient, $paymentSettlementService);
        } catch (RequestException|\RuntimeException $exception) {
            return back()->with('error', 'Unable to verify PayMongo payment status right now.');
        }

        $payment->refresh();

        if ($payment->status === 'completed') {
            return redirect()->route($this->receiptRouteName($payment), [
                'transaction_id' => $transaction_id,
            ]);
        }

        return Inertia::render('kiosk/Payments/PaymongoCheckout', [
            'transaction_id' => $transaction_id,
            'amount_due' => (float) $payment->amount_paid,
            'payment_method' => $payment->gateway_method,
            'status' => $payment->status,
            'failed_reason' => $payment->failed_reason,
            'qr_code' => $payment->gateway_qr_code,
            'checkout_url' => data_get($payment->gateway_response, 'attributes.next_action.redirect.url'),
            'expires_at' => optional($payment->created_at)->addMinutes(30)?->toIso8601String(),
        ]);
    }

    public function status(
        string $transaction_id,
        PaymongoClient $paymongoClient,
        PaymentSettlementService $paymentSettlementService
    ) {
        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->where('payment_channel', 'paymongo')
            ->firstOrFail();

        try {
            $this->syncPaymentIntent($payment, $paymongoClient, $paymentSettlementService);
        } catch (RequestException|\RuntimeException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify PayMongo status at the moment.',
            ], 422);
        }

        $payment->refresh();

        if ($payment->status === 'completed') {
            return response()->json([
                'status' => 'completed',
                'receipt_url' => route($this->receiptRouteName($payment), [
                    'transaction_id' => $transaction_id,
                ]),
            ]);
        }

        if ($payment->status === 'failed') {
            return response()->json([
                'status' => 'failed',
                'message' => $payment->failed_reason ?? 'Payment failed. Please try again.',
            ]);
        }

        return response()->json([
            'status' => 'awaiting_payment',
            'gateway_status' => $payment->gateway_status,
        ]);
    }

    public function handleReturn(
        string $transaction_id,
        PaymongoClient $paymongoClient,
        PaymentSettlementService $paymentSettlementService
    ) {
        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->where('payment_channel', 'paymongo')
            ->firstOrFail();

        try {
            $this->syncPaymentIntent($payment, $paymongoClient, $paymentSettlementService);
        } catch (RequestException|\RuntimeException $exception) {
            return redirect()->route('kiosk.paymongo.checkout', [
                'transaction_id' => $transaction_id,
            ])->with('error', 'Unable to verify PayMongo payment status right now.');
        }

        $payment->refresh();

        if ($payment->status === 'completed') {
            return redirect()->route($this->receiptRouteName($payment), [
                'transaction_id' => $transaction_id,
            ])->with('success', 'PayMongo transaction completed successfully.');
        }

        if ($payment->status === 'failed') {
            return redirect()->route('kiosk.paymongo.checkout', [
                'transaction_id' => $transaction_id,
            ])->with('error', $payment->failed_reason ?? 'PayMongo transaction failed.');
        }

        return redirect()->route('kiosk.paymongo.checkout', [
            'transaction_id' => $transaction_id,
        ])->with('success', 'Payment is still processing. Please wait for confirmation.');
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     * @throws ConnectionException
     */
    protected function syncPaymentIntent(
        Payment $payment,
        PaymongoClient $paymongoClient,
        PaymentSettlementService $paymentSettlementService
    ): void {
        if (in_array($payment->status, ['completed', 'failed'], true)) {
            return;
        }

        if (!$payment->gateway_reference_id) {
            $payment->update([
                'status' => 'failed',
                'gateway_status' => 'missing_reference',
                'failed_reason' => 'Missing PayMongo payment intent reference.',
            ]);

            return;
        }

        $intent = $paymongoClient->retrievePaymentIntent($payment->gateway_reference_id);
        $intentData = data_get($intent, 'data', []);
        $intentStatus = (string) data_get($intentData, 'attributes.status');
        $gatewayPaymentId = data_get($intentData, 'attributes.payments.0.id');
        $gatewayMethod = (string) data_get(
            $intentData,
            'attributes.payments.0.attributes.source.type',
            $payment->gateway_method
        );
        $qrCode = data_get($intentData, 'attributes.next_action.code.image_url', $payment->gateway_qr_code);

        if ($intentStatus === 'succeeded') {
            $paidAmount = ((float) data_get(
                $intentData,
                'attributes.payments.0.attributes.amount',
                data_get($intentData, 'attributes.amount', 0)
            )) / 100;

            $settlement = $paymentSettlementService->completePayment($payment, $paidAmount, [
                'reference_no' => ReceiptService::generateRefNo(),
                'gateway_payment_id' => $gatewayPaymentId,
                'gateway_status' => 'succeeded',
                'gateway_method' => $gatewayMethod,
                'gateway_qr_code' => $qrCode,
                'gateway_response' => $intentData,
            ]);

            session()->put("current_overpayment_{$payment->transaction_id}", $settlement['current_overpayment']);
            session()->put("overpayment_used_{$payment->transaction_id}", $settlement['overpayment_used']);

            return;
        }

        if ($intentStatus === 'awaiting_payment_method') {
            $payment->update([
                'status' => 'failed',
                'gateway_status' => $intentStatus,
                'failed_reason' => (string) data_get(
                    $intentData,
                    'attributes.last_payment_error.failed_message',
                    'PayMongo payment failed or expired. Please try again.'
                ),
                'gateway_response' => $intentData,
            ]);

            return;
        }

        $payment->update([
            'status' => 'awaiting_payment',
            'gateway_status' => $intentStatus,
            'gateway_qr_code' => $qrCode,
            'gateway_response' => $intentData,
        ]);
    }

    protected function resolveInitiationData(string $context, ?int $balanceId, bool $payAll): array
    {
        if ($context === 'tuition') {
            $balance = StudentBalance::query()
                ->where('user_id', auth()->id())
                ->where('fee_name', 'Tuition Fee')
                ->firstOrFail();

            return [
                'balances' => collect([$balance]),
                'description' => 'Tuition Fee',
                'primary_balance_id' => $balance->id,
            ];
        }

        if ($payAll) {
            $balances = StudentBalance::query()
                ->where('user_id', auth()->id())
                ->whereRaw('total_amount > paid_amount')
                ->get();

            return [
                'balances' => $balances,
                'description' => 'All Outstanding Balances',
                'primary_balance_id' => $balances->first()?->id,
            ];
        }

        $balance = StudentBalance::query()
            ->where('id', $balanceId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return [
            'balances' => collect([$balance]),
            'description' => $balance->fee_name,
            'primary_balance_id' => $balance->id,
        ];
    }

    protected function receiptRouteName(Payment $payment): string
    {
        return $payment->payment_context === 'tuition'
            ? 'kiosk.tuition-fee.receipt'
            : 'kiosk.receipt';
    }

    protected function extractPaymongoError(RequestException $exception): string
    {
        $rawMessage = (string) data_get(
            $exception->response?->json(),
            'errors.0.detail',
            $exception->getMessage()
        );

        if (preg_match('/amount cannot be less than (\d+)/i', $rawMessage, $matches)) {
            $minimumCentavos = (int) $matches[1];
            $minimumPesos = number_format($minimumCentavos / 100, 2);

            return "Minimum PayMongo amount is ₱{$minimumPesos}.";
        }

        return $rawMessage;
    }
}
