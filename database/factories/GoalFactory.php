<?php

namespace Database\Factories;

use App\Enums\GoalStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'target_amount' => fake()->randomFloat(2, 100, 5000),
            'current_amount' => 0.00,
            'deadline' => fake()->dateTimeBetween('+1 month', '+2 years'),
            'icon' => '🎯',
            'status' => GoalStatus::Active,
        ];
    }
}
