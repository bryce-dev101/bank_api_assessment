<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentNotification>
 */
class PaymentNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pf_payment_id' => fake()->numerify('##########'),
            'payment_status' => 'complete',
            'item_name' => fake()->words(2, true),
            'item_description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1, 999),
            'merchant_id' => '10000100',
            'payment_id' => Payment::factory(),
            'signature' => 'ad8e7685c9522c24365d7ccea8cb3db7'
        ];
    }
}
