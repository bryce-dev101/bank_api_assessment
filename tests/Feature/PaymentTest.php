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
     * A basic feature test example.
     */
    public function test_making_a_payment_as_a_logged_in_user(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $data = [
            'passPhrase' => 'jt7NOE43FZPn',
            'amount' => '100.50',
            'item_name' => 'Test Item',
            'item_description' => 'Testing Item description'
        ];

        $response = $this->post(route('payment.initialize', $data));

        $response->assertStatus(200);
    }
}
