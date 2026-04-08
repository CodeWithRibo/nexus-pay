<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DynamicProcessingPaymentController extends Controller
{
    public function start(Request $request, $transaction_id)
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

        return Inertia::render('kiosk/ProcessingPayment', [
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

        $currentOverpayment = $request->over_payment;
        $previousOverpayment = User::find(auth()->id())->over_payment ?? 0;

        DB::transaction(function () use ($request, $transaction_id, $payment, $balanceId, $payAll, $currentOverpayment, $previousOverpayment) {
            $amountToPay = $request->amount_paid;

            if ($payAll) {
                $balances = StudentBalance::query()
                    ->where('user_id', auth()->id())
                    ->whereRaw('total_amount > paid_amount')
                    ->lockForUpdate()
                    ->get();

                $firstBalance = $balances->first();

                foreach ($balances as $balance) {
                    if ($amountToPay <= 0) break;

                    $balanceDue = $balance->total_amount - $balance->paid_amount;
                    $paymentForThis = min($amountToPay, $balanceDue);

                    $newPaidAmount = $balance->paid_amount + $paymentForThis;

                    $balance->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newPaidAmount >= $balance->total_amount ? 'completed' : 'pending',
                    ]);

                    $amountToPay -= $paymentForThis;
                }

                $payment->update([
                    'status' => 'completed',
                    'amount_paid' => $request->amount_paid,
                    'reference_no' => 'K-' . strtoupper(Str::random(8)),
                    'student_balance_id' => $firstBalance?->id,
                ]);
            } else {
                $balance = StudentBalance::query()
                    ->where('id', $balanceId)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $payment->update([
                    'status' => 'completed',
                    'amount_paid' => $request->amount_paid,
                    'reference_no' => 'K-' . strtoupper(Str::random(8)),
                    'student_balance_id' => $balance->id,
                ]);

                $newPaidAmount = $balance->paid_amount + $request->amount_paid;

                $balance->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $newPaidAmount >= $balance->total_amount ? 'completed' : 'pending',
                ]);
            }

            User::query()
                ->where('id', auth()->id())
                ->update(['over_payment' => $previousOverpayment + $currentOverpayment]);
        });

        session()->put("current_overpayment_{$transaction_id}", $currentOverpayment);

        session()->forget("payment_data_{$transaction_id}");
        session()->forget('payment_balance_id');
        session()->forget('payment_pay_all');

        $request->session()->flash('success', 'Your transaction is complete. Please wait for your printed receipt below.');
        return redirect()->route('kiosk.receipt', ['transaction_id' => $transaction_id]);
    }
}
