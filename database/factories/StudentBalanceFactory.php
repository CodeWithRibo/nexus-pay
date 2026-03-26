<?php

namespace Database\Factories;

use App\Models\StudentBalance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StudentBalanceFactory extends Factory
{
    protected $model = StudentBalance::class;

    public function definition(): array
    {
        return [
            'fee_name' => $this->faker->randomElement(['Tuition Fee', 'Laboratory Fee', 'Library Fee']),
            'total_amount' => $this->faker->randomFloat(2, 1000, 5000),
            'paid_amount' => $this->faker->randomFloat(2, 0, 1000),
            'status' => $this->faker->randomElement(['pending', 'partial', 'paid']),
            'user_id' => User::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
