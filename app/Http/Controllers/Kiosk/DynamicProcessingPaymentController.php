<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessingRequest;
use App\Models\Payment;
use App\Models\StudentBalance;
use App\Models\User;
use App\Services\Kiosk\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DynamicProcessingPaymentController extends Controller
{
    public function start(ProcessingRequest $request, $transaction_id)
    {
        $request->validated();

        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('kiosk.landing-screen');
        }

        session()->put("payment_data_{$transaction_id}", [
            'amount_paid' => $request->amount_paid,
            'over_payment' => $request->over_payment,
        ]);

        return redirect()->route('kiosk.processing.index', [
            'transaction_id' => $transaction_id,
        ]);
    }

    public function index(Request $request, $transaction_id)
    {
        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('kiosk.landing-screen');
        }

        $sessionKey = "payment_data_{$transaction_id}";
        $paymentData = session($sessionKey);

        if (!$paymentData) {
            return redirect()->route('kiosk.landing-screen');
        }

        return Inertia::render('kiosk/Payments/Processing', [
            'transaction_id' => $transaction_id,
            'amount_paid' => (float) $paymentData['amount_paid'],
            'over_payment' => (float) $paymentData['over_payment'],
            'use_dynamic_route' => true,
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function process(Request $request, $transaction_id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'over_payment' => 'required|numeric',
        ]);

        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('kiosk.landing-screen');
        }

        $balanceId = session('payment_balance_id');
        $payAll = (bool) session('payment_pay_all', false);
        $useOverpayment = session('use_overpayment', false);

        $currentOverpayment = 0.0;
        $previousOverpayment = (float) (User::find(auth()->id())->over_payment ?? 0);

        DB::transaction(function () use ($request, $transaction_id, $payment, $balanceId, $payAll, &$currentOverpayment, $previousOverpayment, $useOverpayment) {
            $cashPaid = (float) $request->amount_paid;
            $overpaymentUsed = 0;

            if ($payAll) {
                $balances = StudentBalance::query()
                    ->where('user_id', auth()->id())
                    ->whereRaw('total_amount > paid_amount')
                    ->lockForUpdate()
                    ->get();

                $firstBalance = $balances->first();
                $amountToApply = $cashPaid;

                if ($useOverpayment && $previousOverpayment > 0) {
                    $totalDue = $balances->sum(fn($b) => max((float) $b->total_amount - (float) $b->paid_amount, 0));
                    $overpaymentUsed = min($previousOverpayment, $totalDue);
                    $amountToApply += $overpaymentUsed;
                }

                foreach ($balances as $balance) {
                    if ($amountToApply <= 0) break;

                    $balanceDue = max((float) $balance->total_amount - (float) $balance->paid_amount, 0);
                    $paymentForThis = min($amountToApply, $balanceDue);

                    $newPaidAmount = min(
                        (float) $balance->paid_amount + $paymentForThis,
                        (float) $balance->total_amount
                    );

                    $balance->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newPaidAmount >= $balance->total_amount ? 'completed' : 'pending',
                    ]);

                    $amountToApply -= $paymentForThis;
                }

                $payment->update([
                    'status' => 'completed',
                    'amount_paid' => $cashPaid,
                    'reference_no' => ReceiptService::generateRefNo(),
                    'student_balance_id' => $firstBalance?->id,
                ]);

                $currentOverpayment = max($amountToApply, 0);
            } else {
                $balance = StudentBalance::query()
                    ->where('id', $balanceId)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $balanceDue = max((float) $balance->total_amount - (float) $balance->paid_amount, 0);

                if ($useOverpayment && $previousOverpayment > 0) {
                    $overpaymentUsed = min($previousOverpayment, $balanceDue);
                }

                $totalAmountApplied = min($cashPaid + $overpaymentUsed, $balanceDue);
                $currentOverpayment = max($cashPaid + $overpaymentUsed - $balanceDue, 0);

                $payment->update([
                    'status' => 'completed',
                    'amount_paid' => $cashPaid,
                    'reference_no' => ReceiptService::generateRefNo(),
                    'student_balance_id' => $balance->id,
                ]);

                $newPaidAmount = min(
                    (float) $balance->paid_amount + $totalAmountApplied,
                    (float) $balance->total_amount
                );

                $balance->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $newPaidAmount >= $balance->total_amount ? 'completed' : 'pending',
                ]);
            }

            $newOverpaymentBalance = max($previousOverpayment - $overpaymentUsed + $currentOverpayment, 0);

            User::query()
                ->where('id', auth()->id())
                ->update(['over_payment' => $newOverpaymentBalance]);

            session()->put("overpayment_used_{$transaction_id}", $overpaymentUsed);
        });

        session()->put("current_overpayment_{$transaction_id}", $currentOverpayment);

        session()->forget("payment_data_{$transaction_id}");
        session()->forget('payment_balance_id');
        session()->forget('payment_pay_all');
        session()->forget('use_overpayment');

        $request->session()->flash('success', 'Your transaction is complete. Please wait for your printed receipt below.');
        return redirect()->route('kiosk.receipt', ['transaction_id' => $transaction_id]);
    }
}
