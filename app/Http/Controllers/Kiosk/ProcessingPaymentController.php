<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;

use App\Models\Payment;
use App\Models\StudentBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProcessingPaymentController extends Controller
{
    public function index()
    {
        return Inertia::render('kiosk/ProcessingPayment');
    }

    public function process(Request $request, $transaction_id)
    {
        $balance = StudentBalance::query()
            ->where('user_id', auth()->id())
            ->where('fee_name', 'Tuition Fee')
            ->first();

        $payment = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('kiosk.landing-screen');
        }

        $payment->update([
            'status' => 'completed',
            'amount_paid' => $request->amount_paid,
            'reference_no' => 'K-' . strtoupper(Str::random(8)),
            'student_balance_id' => $balance->id,
        ]);

        if ($balance) {
            $newTotal = $balance->total_amount - $request->amount_paid;

            $balance->update([
                'paid_amount' => $request->amount_paid,
                'total_amount' => $newTotal < 0 ? 0 : $newTotal,
                'status' => $newTotal <= 0 ? 'completed' : 'pending',
            ]);
        }

        User::query()
        ->where('id', auth()->id())
        ->update(['credit_balance' => $request->credit_balance]);

        return redirect()->route('kiosk.tuition-fee.receipt', ['transaction_id' => $transaction_id]);
    }

}
