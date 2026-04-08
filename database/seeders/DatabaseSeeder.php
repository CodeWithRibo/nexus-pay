<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInformation;
use App\Models\StudentBalance;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()
            ->has(
                UserInformation::factory()->state([
                    'first_name' => 'Ribo',
                    'last_name' => 'Luna',
                ]),
                'information'
            )
            ->create([
                'email' => 'student@example.com',
                'student_id' => '02000411496',
                'password' => 'stotomas20060628',
                'over_payment' => 0,
            ]);

        StudentBalance::factory()->for($user)->create([
            'fee_name' => 'Tuition Fee',
            'total_amount' => 42000.00,
            'paid_amount' => 0,
            'status' => 'pending',
        ]);

        StudentBalance::factory()->for($user)->create([
            'fee_name' => 'Uniform Fee',
            'total_amount' => 1932.00,
            'paid_amount' => 0,
            'status' => 'pending',
        ]);

        StudentBalance::factory()->for($user)->create([
            'fee_name' => 'Field Trip Fee',
            'total_amount' => 1600.00,
            'paid_amount' => 0,
            'status' => 'pending',
        ]);

        StudentBalance::factory()->for($user)->create([
            'fee_name' => 'Special Exam Fee',
            'total_amount' => 200,
            'paid_amount' => 0,
            'status' => 'pending',
        ]);

        StudentBalance::factory()->for($user)->create([
            'fee_name' => 'Documents Fee',
            'total_amount' => 100.00,
            'paid_amount' => 0,
            'status' => 'pending',
        ]);

//        Payment::factory()
//            ->for($user)
//            ->for($balance)
//            ->create([
//                'transaction_id' => fake()->uuid(),
//                'status' =>   'pending',
//                'amount_paid' => 1500.00,
//                'reference_no' => 'RF-2026-000123',
//            ]);
    }
}
