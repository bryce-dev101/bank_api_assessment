<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Generate Signature to be authenticated by PayFast
     * Returns MD5 hashed string
     */
    private function generateSignature($data, $passPhrase = null)
    {
        // Create parameter string
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }
        // Remove last ampersand
        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    /**
     * Generate request data for payload to PayFast
     */
    private function generateRequest($user, $requestData, $passPhrase)
    {
        $data = [
            'merchant_id' => env('PAYFAST_MERCHANT_ID', '10000100'),
            'merchant_key' => env('PAYFAST_MERCHANT_KEY', '46f0cd694581a'),
            'name_first' => $user->first_name,
            'name_last' => $user->last_name,
            'email_address' => $user->email_address,
            'cell_number' => $user->cell_number,
            'm_payment_id' => 1,
            'amount' => $requestData->amount,
            'item_name' => $requestData->item_name,
            'item_description' => $requestData->item_description
        ];

        $signature = $this->generateSignature($data, $passPhrase);

        $data['signature'] = $signature;
    }

    /**
     * Show a single payments details
     */
    public function show()
    {
        
    }

    /**
     * Initialize payment to PayFast
     */
    public function initialize(Request $request)
    {
        $data = $this->generateRequest($request->user(), $request, $request->passPhrase);
        $response = Http::post(env('PAYFAST_API', 'https://sandbox.payfast.co.za​/eng/process'), $data);

        return $response;
    }

    /**
     * Payfast will send us a notification that the payment has been made or cancelled
     */
    public function notify()
    {
        
    }

    /**
     * Payment page to make a payment
     */
    public function payment()
    {
        return view('welcome');
    }
}
