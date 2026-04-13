<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\StudentBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function __invoke(Request $request)
    {
        $balanceId = $request->query('balance_id');
        $payAll = filter_var($request->query('pay_all', false), FILTER_VALIDATE_BOOLEAN);
        $userId = Auth::id();

        $student = User::query()
            ->with(['information:id,first_name,last_name,user_id'])
            ->findOrFail($userId);

        if ($payAll) {
            $balances = StudentBalance::query()
                ->where('user_id', $userId)
                ->whereRaw('total_amount > paid_amount')
                ->get();

            $totalDue = $balances->sum(fn($b) => $b->total_amount - $b->paid_amount);

            return Inertia::render('kiosk/PaymentMethod', [
                'student' => [
                    'student_name' => $student->information->first_name . ' ' . $student->information->last_name ?? 'Unknown',
                    'student_id' => $student->student_id,
                    'description' => 'All Outstanding Balances',
                    'current_balance' => $totalDue,
                    'over_payment' => $student->over_payment ?? 0,
                ],
                'balance_id' => null,
                'pay_all' => true,
            ]);
        }

        $balance = StudentBalance::query()
            ->where('id', $balanceId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $currentBalance = $balance->total_amount - $balance->paid_amount;

        return Inertia::render('kiosk/PaymentMethod', [
            'student' => [
                'student_name' => $student->information->first_name . ' ' . $student->information->last_name ?? 'Unknown',
                'student_id' => $student->student_id,
                'description' => $balance->fee_name,
                'current_balance' => $currentBalance,
                'over_payment' => $student->over_payment ?? 0,
            ],
            'balance_id' => (int) $balanceId,
            'pay_all' => false,
        ]);
    }
}
