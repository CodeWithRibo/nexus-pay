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
                    ->select(['id', 'fee_name', 'total_amount', 'user_id']);
            },
            'payments:id,amount_paid,user_id'
        ])->findOrFail(auth()->id());

        return Inertia::render('kiosk/TuitionFee', [
            'student' => [
                'student_name' => $student->information->first_name.' '.$student->information->last_name ?? 'Unknown',
                'student_id' => $student->student_id,
                'description' => $student->studentBalances->first()->fee_name,
                'current_balance' => optional($student->studentBalances->first())->total_amount,
            ]
        ]);
    }
}
