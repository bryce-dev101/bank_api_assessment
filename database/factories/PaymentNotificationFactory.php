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
        $amountGross = fake()->randomFloat(2, 1, 9999);
        $amountFee = fake()->randomFloat(2, 1, 100);

        $data = [
            'pf_payment_id' => fake()->numerify('##########'),
            'payment_status' => 'complete',
            'item_name' => fake()->words(2, true),
            'item_description' => fake()->sentence(),
            'amount_gross' => $amountGross,
            'amount_fee' => $amountFee,
            'amount_net' => $amountGross - $amountFee,
            'merchant_id' => '10000100',
            "name_first" => fake()->firstName(),
            "name_last" => fake()->lastName(),
            "email_address" => fake()->email(),
            'payment_id' => Payment::factory()
        ];

        $data['signature'] = http_build_query($data, '', '&') . '&passphrase=' . urlencode(config('payfast.merchant_passphrase'));

        return $data;
    }
}
