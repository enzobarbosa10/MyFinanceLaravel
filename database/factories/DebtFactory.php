<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'total_amount' => fake()->randomFloat(2, 500, 50000),
            'paid_amount' => 0.00,
            'monthly_interest_rate' => 0.02,
            'min_payment' => 50.00,
            'due_day' => fake()->numberBetween(1, 28),
            'status' => 'active',
        ];
    }
}
