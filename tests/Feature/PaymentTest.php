<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test making a payment as a logged-in user.
     */
    public function test_making_a_payment_as_a_logged_in_user(): void
    {
        // Create and log in the user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Payment data
        $data = [
            'amount' => fake()->randomFloat(2, 1, 999),
            'item_name' => fake()->words(2, true),
            'item_description' => fake()->sentence()
        ];

        // Make a POST request to the payment initialization route
        $response = $this->post(route('payment.initialize', $data));

        // Assert the response was OK
        $response->assertOk();

        // Assert that a payment record was created and associated with the user
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'item_name' => $data['item_name']
        ]);

        // Assert that a payment request was created and associated with the created payment\
        $payment = $user->payments()->first();
        $this->assertDatabaseHas('payment_requests', [
            'payment_id' => $payment->id
        ]);

        // Ensure that the payment request data matches expected values
        $paymentRequest = $payment->paymentRequests()->first();
        $this->assertEquals($paymentRequest->item_name, $data['item_name']);
    }

    /**
     * Test recieveing a payment notification from payfast.
     */
    public function test_payfast_making_a_request_notifying_of_successful_payment(): void
    {
        $amountFee = fake()->randomFloat(2, 1, 100);
        
        $user = User::factory()->has(
            Payment::factory()->hasPaymentRequests(1)
        )->create();

        $payment = $user->payments()->first();
        $paymentRequest = $payment->paymentRequests->first();

        // Payment data
        $data = [
            'm_payment_id' => $payment->id,
            'pf_payment_id' => fake()->numerify('#######'),
            'payment_status' => 'COMPLETE',
            'item_name' => $payment->item_name,
            'item_description' => $payment->item_description,
            'amount_gross' => $payment->amount,
            'amount_fee' => $amountFee,
            'amount_net' => strval($payment->amount - $amountFee),
            'name_first' => $user->first_name,
            'name_last' => $user->last_name,
            'email_address' => $user->email,
            'merchant_id' => strval($paymentRequest->merchant_id)
        ];

        // Build query string and generate signature
        $data['signature'] = md5(http_build_query($data, '', '&') . '&passphrase=' . urlencode(config('payfast.merchant_passphrase')));

        // Make a POST request to the payment notification route
        $response = $this->withHeaders([
            'Referer' => 'http://localhost/'
        ])->post(route('payment.notify', $data));

        // Assert the response was OK
        $response->assertOk();
    }
}
