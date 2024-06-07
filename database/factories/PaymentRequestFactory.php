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
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $email = fake()->email();
        $cellNumber = fake()->numerify('0#########');
        $itemName = fake()->words(2, true);
        $itemDescription = fake()->sentence();
        $amount = fake()->randomFloat(2, 1, 99999);
        $merchantId = config('payfast.merchant_id');
        $merchantKey = config('payfast.merchant_key');
        $paymentID = Payment::factory();

        // Prepare the data array for hashing
        $data = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'name_first' => $firstName,
            'name_last' => $lastName,
            'email_address' => $email,
            'cell_number' => $cellNumber,
            'm_payment_id' => $paymentID,
            'amount' => $amount,
            'item_name' => $itemName,
            'item_description' => $itemDescription
        ];

        $queryString = http_build_query($data, '', '&') . '&passphrase=' . urlencode(config('payfast.merchant_passphrase'));


        return [
            'customer_first_name' => $firstName,
            'customer_last_name' => $lastName,
            'customer_email' => $email,
            'customer_cell_number' => $cellNumber,
            'item_name' => $itemName,
            'item_description' => $itemDescription,
            'amount' => $amount,
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'payment_id' => $paymentID,
            'signature' => md5($queryString)
        ];
    }
}
