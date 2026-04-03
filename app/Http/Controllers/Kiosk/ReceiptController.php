<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function __invoke($transaction_id)
    {
        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

//        if ($payment->status !== 'pending') {
//            return redirect()->route('kiosk.landing-screen');
//        }

        return Inertia::render('kiosk/Receipt');
    }
}
