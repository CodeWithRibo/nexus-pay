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

        $studAmountDue = StudentBalance::query()
        ->where('user_id', auth()->id())
        ->where('fee_name', 'Tuition Fee')
        ->sum('total_amount');

        return Inertia::render('kiosk/CashInsertion', [
            'studAmountDue' => $studAmountDue,
            'transaction_id' => $transaction_id,
        ]);
    }
}
