<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\StudentBalance;
use Inertia\Inertia;

class OutstandingBalancesController extends Controller
{
    public function __invoke()
    {
        $studentBalances = StudentBalance::query()
            ->where('user_id', auth()->id())
            ->select(['id', 'fee_name', 'total_amount','paid_amount', 'status', 'user_id'])
            ->get();

        $totalAssessment = $studentBalances->sum(
            fn (StudentBalance $balance) => (float) $balance->total_amount
        );
        $amountSettled = $studentBalances->sum(
            fn (StudentBalance $balance) => min(
                max((float) $balance->paid_amount, 0),
                (float) $balance->total_amount
            )
        );
        $amountDue = max($totalAssessment - $amountSettled, 0);

        return Inertia::render('kiosk/Account/Balances', [
            'studentBalances' => $studentBalances,
            'totalAssessment' => $totalAssessment,
            'amountSettled' => $amountSettled,
            'amountDue' => $amountDue,
            'overPayment' => auth()->user()->over_payment ?? 0,
        ]);
    }
}
