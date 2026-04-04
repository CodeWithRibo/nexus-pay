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

class ProcessingPaymentController extends Controller
{
    public function start(Request $request, $transaction_id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'credit_balance' => 'required|numeric',
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
            'credit_balance' => $request->credit_balance,
        ]);

        return redirect()->route('kiosk.tuition-fee.processing.index', [
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
            'credit_balance' => (float) $paymentData['credit_balance'],
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function process(Request $request, $transaction_id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'credit_balance' => 'required|numeric',
        ]);

        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('kiosk.landing-screen');
        }

        DB::transaction(function () use ($request, $transaction_id, $payment) {
            $balance = StudentBalance::query()
                ->where('user_id', auth()->id())
                ->where('fee_name', 'Tuition Fee')
                ->lockForUpdate()
                ->firstOrFail();

            $payment->update([
                'status' => 'completed',
                'amount_paid' => $request->amount_paid,
                'reference_no' => 'K-' . strtoupper(Str::random(8)),
                'student_balance_id' => $balance->id,
            ]);

            $newTotal = $balance->total_amount - $request->amount_paid;

            $balance->update([
                'paid_amount' => $balance->paid_amount + $request->amount_paid,
                'total_amount' => max($newTotal, 0),
                'status' => $newTotal <= 0 ? 'completed' : 'pending',
            ]);

            User::query()
                ->where('id', auth()->id())
                ->update(['credit_balance' => $request->credit_balance]);
        });

        session()->forget("payment_data_{$transaction_id}");

        $request->session()->flash('success', 'Your transaction is complete. Please wait for your printed receipt below.');
        return redirect()->route('kiosk.tuition-fee.receipt', ['transaction_id' => $transaction_id]);
    }
}
