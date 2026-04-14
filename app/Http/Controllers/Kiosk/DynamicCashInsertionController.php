<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentBalance;
use Inertia\Inertia;

class DynamicCashInsertionController extends Controller
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

        $balanceId = session('payment_balance_id');
        $payAll = (bool) session('payment_pay_all', false);
        $useOverpayment = session('use_overpayment', false);

        $user = auth()->user();
        $userOverpayment = $user->over_payment ?? 0;


        if ($payAll) {
            $balances = StudentBalance::query()
                ->where('user_id', auth()->id())
                ->whereRaw('total_amount > paid_amount')
                ->get();

            $amountDue = $balances->sum(fn($b) => $b->total_amount - $b->paid_amount);
            $description = 'All Outstanding Balances';
        } else {
            if (!$balanceId) {
                return redirect()->route('kiosk.outstanding-balance')
                    ->with('error', 'No balance selected');
            }

            $balance = StudentBalance::query()
                ->where('id', $balanceId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $amountDue = $balance->total_amount - $balance->paid_amount;
            $description = $balance->fee_name;
        }

        if ($useOverpayment && $userOverpayment > 0) {
            $amountDue = max($amountDue - $userOverpayment, 0);
        }

        return Inertia::render('kiosk/Payments/CashInsertion', [
            'studAmountDue' => $amountDue,
            'transaction_id' => $transaction_id,
            'description' => $description,
            'balance_id' => $balanceId,
            'pay_all' => $payAll,
        ]);
    }
}
