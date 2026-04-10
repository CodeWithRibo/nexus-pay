<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\StudentBalance;
use Inertia\Inertia;

class CheckBalanceController extends Controller
{
    public function __invoke()
    {
        $studentBalances = StudentBalance::query()
            ->where('user_id', auth()->id())
            ->select(['id', 'fee_name', 'total_amount','paid_amount', 'status', 'user_id'])
            ->get();

        $amountDue = $studentBalances->sum(
            fn (StudentBalance $balance) => max(
                (float) $balance->total_amount - (float) $balance->paid_amount,
                0
            )
        );

        return Inertia::render('kiosk/CheckBalance', [
            'studentBalances' => $studentBalances,
            'totalAssessment' => $studentBalances->sum('total_amount'),
            'amountSettled' => $studentBalances->sum('paid_amount'),
            'amountDue' => $amountDue,
            'overPayment' => auth()->user()->over_payment ?? 0,
        ]);
    }
}
