<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentRequest>
 */
class PaymentRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_first_name' => fake()->firstName(),
            'customer_last_name' => fake()->lastName(),
            'customer_email' => fake()->unique()->email(),
            'customer_cell_number' => fake()->phoneNumber(),
            'item_name' => fake()->words(2, true),
            'item_description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1, 999),
            'merchant_id' => '10000100',
            'merchant_key' => '46f0cd694581a',
            'payment_id' => Payment::factory()
        ];
    }
}
