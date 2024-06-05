<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_name' => fake()->words(2, true),
            'item_description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1, 999),
            'status' => 'created',
            'user_id' => User::factory()
        ];
    }
}
