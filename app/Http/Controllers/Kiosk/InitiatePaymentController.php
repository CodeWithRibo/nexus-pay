<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InitiatePaymentController extends Controller
{
    public function __invoke(Request $request)
    {
        $transactionId = Str::uuid7()->toString();
        $useOverpayment = $request->boolean('use_overpayment', false);

        Payment::query()->create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        session([
            'transaction_id' => $transactionId,
            'use_overpayment' => $useOverpayment,
        ]);

        return redirect()->route('kiosk.tuition-fee.cash-insertion', [
            'transaction_id' => $transactionId,
        ]);
    }

    public function initiate(Request $request)
    {
        $transactionId = Str::uuid7()->toString();

        $balanceId = $request->input('balance_id');
        $payAll = $request->boolean('pay_all', false);
        $useOverpayment = $request->boolean('use_overpayment', false);

        Payment::query()->create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        session([
            'transaction_id' => $transactionId,
            'payment_balance_id' => $balanceId ? (int) $balanceId : null,
            'payment_pay_all' => (bool) $payAll,
            'use_overpayment' => $useOverpayment,
        ]);

        return redirect()->route('kiosk.cash-insertion', [
            'transaction_id' => $transactionId,
        ]);
    }
}
