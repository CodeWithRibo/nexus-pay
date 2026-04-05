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
        

        return Inertia::render('kiosk/CheckBalance', [
            'studentBalances' => $studentBalances,
            'totalAssessment' => $studentBalances->sum('total_amount'),
            'amountSettled' => $studentBalances->sum('paid_amount'),
            'amountDue' => $studentBalances->sum('total_amount') - $studentBalances->sum('paid_amount'),
        ]);
    }
}
