<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Str;

class InitiatePaymentController extends Controller
{
    public function __invoke()
    {
        $transactionId = Str::uuid7()->toString();

        Payment::query()->create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        session(['transaction_id' => $transactionId]);

        return redirect()->route('kiosk.tuition-fee.cash-insertion', [
            'transaction_id' => $transactionId,
        ]);
    }
}
