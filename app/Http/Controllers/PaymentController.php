<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentInitiationRequest;
use App\Http\Requests\PaymentNotificationRequest;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Generates an MD5 signature for PayFast API authentication from the given data array.
     *
     * @param array $data Key-value pairs for signature.
     * @return string MD5 hash of the constructed signature string.
     */
    private function generateSignature($data): string
    {
        // Filter out empty values
        $filteredData = array_filter($data, function ($value) {
            return ($value !== '' && $value !== null);
        });

        // Build query string from filtered data
        $pfOutput = http_build_query($filteredData, '', '&');

        // Append passphrase
        $pfOutput .= '&passphrase=' . urlencode(config('payfast.merchant_passphrase'));

        // Return MD5 hash of the final string
        return md5($pfOutput);
    }

    /**
     * Generate request data for payload to PayFast
     * 
     * @param User $user User model instance
     * @param Request $requestData Data from the request
     * @return array Array of data to be sent to PayFast.
     */
    private function generateRequest($user, $requestData): array
    {
        // Prepare payload in correct order for PayFast
        $data = [
            'merchant_id' => config('payfast.merchant_id'),
            'merchant_key' => config('payfast.merchant_key'),
            'name_first' => $user->first_name,
            'name_last' => $user->last_name,
            'email_address' => $user->email_address,
            'cell_number' => $user->cell_number,
            'm_payment_id' => $requestData->payment_id ?? 1,
            'amount' => $requestData->amount,
            'item_name' => $requestData->item_name,
            'item_description' => $requestData->item_description
        ];

        // Generate and append signature to payload
        $data['signature'] = $this->generateSignature($data);

        // Return the fully prepared data array
        return $data;
    }

    /**
     * Show all of my payments
     */
    public function index()
    {
    }

    /**
     * Show a payments details
     */
    public function show()
    {
    }

    /**
     * Initialize payment to PayFast with user and payment details
     * 
     * @param PaymentInitiationRequest $request Validated request data for payment initiation.
     * @return \Illuminate\Http\Client\Response Returns the HTTP response from the PayFast API.
     */
    public function initialize(PaymentInitiationRequest $request): Response
    {
        // Generate the data array for the PayFast API request using validated request data.
        $data = $this->generateRequest($request->user(), $request);

        // Prepare the API endpoint URL.
        $apiUrl = config('payfast.api_url') . 'eng/process';

        // Send the data to PayFast as a form-encoded POST request and store the response.
        $response = Http::asForm()->post($apiUrl, $data);

        // Error logging.
        if ($response->failed()) {
            Log::error('PayFast API request failed', [
                'url' => $apiUrl,
                'data' => $data,
                'response' => $response->body()
            ]);
        }

        // Return the HTTP response to the caller
        return $response;
    }

    /**
     * Payfast will send us a notification that the payment has been made or cancelled
     */
    public function handleNotification(PaymentNotificationRequest $request)
    {
        
    }

    /**
     * Payment page to make a payment
     */
    public function create()
    {
        return view('welcome');
    }
}
