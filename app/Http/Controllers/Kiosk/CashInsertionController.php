<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentBalance;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CashInsertionController extends Controller
{
    public function __invoke($transaction_id)
    {
        $payment = Payment::query()
        ->where('transaction_id', $transaction_id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('kiosk.landing-screen');
        }

        $useOverpayment = session('use_overpayment', false);
        $user = auth()->user();
        $userOverpayment = $user->over_payment ?? 0;

        $studAmountDue = StudentBalance::query()
        ->where('user_id', auth()->id())
        ->where('fee_name', 'Tuition Fee')
        ->sum('total_amount');

        if ($useOverpayment && $userOverpayment > 0) {
            $studAmountDue = max($studAmountDue - $userOverpayment, 0);
        }

        return Inertia::render('kiosk/Payments/CashInsertion', [
            'studAmountDue' => $studAmountDue,
            'transaction_id' => $transaction_id,
        ]);
    }
}
