<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'm_payment_id' => 'required|string|max:100',
            'pf_payment_id' => 'required|string|max:100',
            'payment_status' => 'required|in:COMPLETE,PENDING,FAILED,CANCELLED',
            'item_name' => 'required|string|max:255',
            'item_description' => 'nullable|string|max:1000',
            'amount_gross' => 'required|numeric|min:0.01',
            'amount_fee' => 'required|numeric|min:0.01',
            'amount_net' => 'required|numeric|min:0.01',
            'name_first' => 'nullable|string|max:100',
            'name_last' => 'nullable|string|max:100',
            'email_address' => 'nullable|email|max:255',
            'merchant_id' => 'required|numeric',
            'signature' => 'required|string',
        ];
    }
}
