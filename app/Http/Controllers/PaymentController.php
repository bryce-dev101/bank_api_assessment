<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentInitiationRequest;
use App\Http\Requests\PaymentNotificationRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            'email_address' => $user->email,
            'cell_number' => $user->cell_number,
            'm_payment_id' => strval($requestData['payment_id']),
            'amount' => $requestData['amount'],
            'item_name' => $requestData['item_name'],
            'item_description' => $requestData['item_description']
        ];

        // Generate and append signature to payload
        $data['signature'] = $this->generateSignature($data);

        // Return the fully prepared data array
        return $data;
    }

    /**
     * Creates and saves a payment record associated with a user.
     *
     * @param User $user The user model instance to associate the payment with.
     * @param array $requestData Data used to create the payment.
     * @return Payment|null The newly created payment model instance or null if failed.
     */
    private function savePayment(User $user, array $requestData): ?Payment
    {
        try {
            // Create a new payment record associated with the user
            $payment = $user->payments()->create($requestData);
            return $payment;
        } catch (\Exception $e) {
            // log the error
            Log::error('Failed to save payment: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Saves a new payment request linked to the specified payment object.
     *
     * @param Payment $payment The payment object to which the request will be linked.
     * @param array $data The data array containing all necessary details for the payment request.
     */
    private function savePaymentRequest(Payment $payment, array $data): void
    {
        // Prepare the data array for the payment request with key renaming.
        $parsedData = [
            'customer_first_name' => $data['name_first'],
            'customer_last_name' => $data['name_last'],
            'customer_email' => $data['email_address'],
            'customer_cell_number' => $data['cell_number'],
            'item_name' => $data['item_name'],
            'item_description' => $data['item_description'],
            'amount' => $data['amount'],
            'merchant_id' => $data['merchant_id'],
            'merchant_key' => $data['merchant_key'],
            'payment_id' => $data['m_payment_id'],
            'signature' => $data['signature']
        ];


        // Create a payment request using the parsed data.
        try {
            $payment->paymentRequests()->create($parsedData);
        } catch (\Exception $e) {
            // Handle potential errors that could occur during the creation process.
            Log::error("Failed to save payment request: {$e->getMessage()}");
        }
    }

    /**
     * Match the generated signature with the request signature.
     *
     * @param string $generatedSignature
     * @param string $requestSignature
     * @return bool
     */
    private function matchSignature(string $generatedSignature, string $requestSignature): bool
    {
        return $generatedSignature === $requestSignature;
    }

    /**
     * Match the payment amount with the gross amount, allowing a small tolerance.
     *
     * @param float $paymentAmount
     * @param float $grossAmount
     * @return bool
     */
    private function matchAmount(float $paymentAmount, float $grossAmount): bool
    {
        return !(abs($paymentAmount - $grossAmount) > 0.01);
    }

    /**
     * Confirm the server response with PayFast.
     * Fake this for the assessment as there is no direct communication with Payfast at the moment
     *
     * @param array $data
     * @return bool
     */
    private function confirmWithPayFast(array $data): bool
    {
        // Build the parameter string for the PayFast server confirmation
        $pfParamString = http_build_query($data, '', '&') . '&passphrase=' . urlencode(config('payfast.merchant_passphrase'));

        // URL for PayFast validation
        $url = config('payfast.api_url') . 'eng/query/validate/';

        // Mocking the HTTP request
        Http::fake([
            $url => Http::response('VALID', 200)
        ]);

        // Make the HTTP request to PayFast
        $response = Http::withOptions(['verify' => true])->asForm()->post($url, [
            'pfParamString' => $pfParamString // Sending the string as a parameter
        ]);

        // Check if the response from PayFast is 'VALID'
        return $response->body() === 'VALID';
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
        // Save payment and retrieve the Payment ID
        $payment = $this->savePayment($request->user(), $request->all());

        // Prepare data including the payment ID for the PayFast API request
        $data = $this->generateRequest($request->user(), array_merge($request->all(), ['payment_id' => $payment->id]));

        // Prepare the API endpoint URL.
        $apiUrl = config('payfast.api_url') . 'eng/process';

        // Send the data to PayFast as a form-encoded POST request and store the response.
        $response = Http::asForm()->post($apiUrl, $data);

        if ($response->failed()) {
            Log::error('PayFast API request failed', [
                'url' => $apiUrl,
                'data' => $data,
                'response' => $response->body()
            ]);

            // if there is an error abort operateion 
            abort(502, "Error communicating with payment gateway.");
        }

        // Save the payment request if it passes
        $this->savePaymentRequest($payment, $data);

        // Return the HTTP response to the caller
        return response(['status' => 'success'], 200);
    }

    /**
     * Payfast will send us a notification that the payment has been made or cancelled.
     *
     * @param PaymentNotificationRequest $request
     * @return Response
     */
    public function handleNotification(PaymentNotificationRequest $request): Response
    {
        // Extract the data from the request, excluding the signature
        $data = $request->except(['signature']);

        // Generate the signature using the extracted data
        $generatedSignature = $this->generateSignature($data);

        // Find the payment using the provided payment ID
        $payment = Payment::find($request->m_payment_id);

        // If the payment is not found, return a 404 response
        if (!$payment) {
            return response(['error' => 'Payment not found.'], 404);
        }

        $matchSignature = $this->matchSignature($generatedSignature, $request->signature); // Check if the generated signature matches the request signature
        $matchAmount = $this->matchAmount($payment->amount, $request->amount_gross); // Check if the amount matches, allowing a small tolerance
        $pfServerConfirmation = $this->confirmWithPayFast($data); // Confirm the server response with PayFast

        // If all conditions are met, create a payment notification and return a success response
        if ($matchSignature && $matchAmount && $pfServerConfirmation) {
            $payment->paymentNotifications()->create($request->except(['m_payment_id']));

            return response(['status' => 'success'], 200);
        }

        // If any condition fails, return a validation error response
        return response(['error' => 'Validation failed.'], 400);
    }

    /**
     * Payment page to make a payment
     */
    public function create()
    {
        return view('welcome');
    }
}
