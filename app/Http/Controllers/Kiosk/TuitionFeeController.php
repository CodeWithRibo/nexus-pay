<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class TuitionFeeController extends Controller
{
    public function __invoke()
    {
        $student = User::query()
            ->where('id', auth()->id())
            ->with([
                'information:id,first_name,last_name,user_id',
                'studentBalances' => function ($query) {
                    $query->where('fee_name', 'Tuition Fee')
                        ->latest()
                        ->select(['id', 'fee_name', 'total_amount', 'user_id', 'paid_amount']);
                },
            ])->findOrFail(auth()->id());

        $tuitionBalance = $student->studentBalances->first();
        $totalAmount = $tuitionBalance->total_amount ?? 0;
        $paidAmount =  $tuitionBalance->paid_amount ?? 0;
        $currentBalance = max($totalAmount - $paidAmount, 0);

        return Inertia::render('kiosk/TuitionFee', [
            'student' => [
                'student_name' => trim(($student->information->first_name ?? '').' '.($student->information->last_name ?? '')) ?: 'Unknown',
                'student_id' => $student->student_id,
                'description' => $tuitionBalance->fee_name ?? 'Tuition Fee',
                'current_balance' => $currentBalance,
            ]
        ]);
    }
}
