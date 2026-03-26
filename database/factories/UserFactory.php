<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'student_id' => $this->faker->unique()->numerify('02########'),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => 'student',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
