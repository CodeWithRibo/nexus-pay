<?php

namespace App\Services\Kiosk;

use App\Models\Payment;
use App\Models\StudentBalance;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentSettlementService
{
    /**
     * @throws \Throwable
     */
    public function completePayment(Payment $payment, float $cashPaid, array $paymentAttributes = []): array
    {
        return DB::transaction(function () use ($payment, $cashPaid, $paymentAttributes) {
            $lockedPayment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status === 'completed') {
                return [
                    'already_completed' => true,
                    'current_overpayment' => 0,
                    'overpayment_used' => 0,
                ];
            }

            $user = User::query()
                ->whereKey($lockedPayment->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            $balances = $this->resolveBalances(
                $lockedPayment->user_id,
                (string) $lockedPayment->payment_context,
                (bool) $lockedPayment->pay_all,
                $lockedPayment->student_balance_id
            );

            $previousOverpayment = (float) ($user->over_payment ?? 0);
            $overpaymentUsed = 0;
            $amountToApply = $cashPaid;

            if ($lockedPayment->use_overpayment && $previousOverpayment > 0) {
                $totalDue = $balances->sum(
                    fn(StudentBalance $balance) => max(
                        (float) $balance->total_amount - (float) $balance->paid_amount,
                        0
                    )
                );
                $overpaymentUsed = min($previousOverpayment, $totalDue);
                $amountToApply += $overpaymentUsed;
            }

            foreach ($balances as $balance) {
                if ($amountToApply <= 0) {
                    break;
                }

                $balanceDue = max((float) $balance->total_amount - (float) $balance->paid_amount, 0);
                $paymentForBalance = min($amountToApply, $balanceDue);

                if ($paymentForBalance <= 0) {
                    continue;
                }

                $newPaidAmount = (float) $balance->paid_amount + $paymentForBalance;
                $balance->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $newPaidAmount >= (float) $balance->total_amount ? 'completed' : 'pending',
                ]);

                $amountToApply -= $paymentForBalance;
            }

            $currentOverpayment = max($amountToApply, 0);
            $newOverpaymentBalance = $previousOverpayment - $overpaymentUsed + $currentOverpayment;

            $user->update([
                'over_payment' => $newOverpaymentBalance,
            ]);

            $primaryBalanceId = $balances->first()?->id;

            $lockedPayment->update(array_merge([
                'status' => 'completed',
                'amount_paid' => $cashPaid,
                'student_balance_id' => $primaryBalanceId,
                'failed_reason' => null,
            ], $paymentAttributes));

            return [
                'already_completed' => false,
                'current_overpayment' => $currentOverpayment,
                'overpayment_used' => $overpaymentUsed,
            ];
        });
    }

    protected function resolveBalances(
        int $userId,
        string $paymentContext,
        bool $payAll,
        ?int $studentBalanceId
    ): Collection {
        if ($paymentContext === 'tuition') {
            $balance = StudentBalance::query()
                ->where('user_id', $userId)
                ->where('fee_name', 'Tuition Fee')
                ->lockForUpdate()
                ->first();

            return collect($balance ? [$balance] : []);
        }

        if ($payAll) {
            return StudentBalance::query()
                ->where('user_id', $userId)
                ->whereRaw('total_amount > paid_amount')
                ->lockForUpdate()
                ->get();
        }

        if (!$studentBalanceId) {
            return collect();
        }

        $balance = StudentBalance::query()
            ->where('id', $studentBalanceId)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();

        return collect($balance ? [$balance] : []);
    }
}
