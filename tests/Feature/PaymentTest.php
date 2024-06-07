<?php

namespace Tests\Feature;

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
        $paymentRequest = $payment->paymentRequest()->first();
        $this->assertEquals($paymentRequest->item_name, $data['item_name']);
    }
}
