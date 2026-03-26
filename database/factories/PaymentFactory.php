<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\StudentBalance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'reference_no' => 'RF-' . $this->faker->unique()->numerify('####-######'),
            'amount_paid' => $this->faker->randomFloat(2, 500, 5000),
            'user_id' => User::factory(),
            'student_balance_id' => StudentBalance::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
