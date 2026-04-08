<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function __invoke($transaction_id)
    {
        $student = User::query()
            ->where('id', auth()->id())
            ->with([
                'information:id,first_name,last_name,user_id',
                'studentBalances' => function ($query) {
                    $query->where('fee_name', 'Tuition Fee')
                        ->latest()
                        ->select(['id', 'fee_name', 'total_amount', 'paid_amount', 'user_id']);
                },
                'payments:id,amount_paid,user_id'
            ])->findOrFail(auth()->id());

        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($payment->status !== 'completed') {
            return redirect()->route('kiosk.landing-screen');
        }

        $sessionKey = "receipt_viewed_{$transaction_id}";
        if (session()->has($sessionKey)) {
            return redirect()->route('kiosk.landing-screen');
        }

        session()->put($sessionKey, false);
        request()->session()->forget('transaction_id');

        $studentBalance = $student->studentBalances->first();
        $totalPaidToDate = optional($studentBalance)->paid_amount ?? 0;
        $totalAmount = optional($studentBalance)->total_amount ?? 0;
        $currentBalance = max($totalAmount - $totalPaidToDate, 0);
        
        $currentOverpayment = session("current_overpayment_{$transaction_id}", 0);
        $overpaymentUsed = session("overpayment_used_{$transaction_id}", 0);
        $totalOverpayment = $student->over_payment ?? 0;

        return Inertia::render('kiosk/Receipt', [
            'student_name' => $student->information->first_name.' '.$student->information->last_name ?? 'Unknown',
            'student_email' => $student->email,
            'student_id' => $student->student_id,
            'reference_number' => $payment->reference_no,
            'fee_category' => optional($studentBalance)->fee_name,
            'amount_paid' => $payment->amount_paid,
            'total_paid_to_date' => $totalPaidToDate,
            'outstanding_balance' => $currentBalance,
            'current_overpayment' => $currentOverpayment,
            'overpayment_used' => $overpaymentUsed,
            'total_overpayment' => $totalOverpayment,
            'transaction_date' => $payment->created_at->format('F d, Y \a\t h:i A'),
        ]);
    }
}
