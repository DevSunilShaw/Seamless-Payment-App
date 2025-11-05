<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $merchantKey;
    private $merchantKeyValidate;
    private $apiUrls;

    public function __construct()
    {
        $this->merchantKey         = config('constants.merchant.primary');
        $this->merchantKeyValidate = config('constants.merchant.validate');
        $this->apiUrls             = config('constants.api');
    }

    public function createTransaction(Request $request)
    {
        try {
            $payload = [
                'merchant_key' => $this->merchantKey,
                'invoice' => [
                    'items' => [
                        [
                            'name'        => 'Deposit',
                            'price'       => config('constants.invoice.price'),
                            'description' => config('constants.invoice.description'),
                            'qty'         => 1
                        ]
                    ],
                    'invoice_id'          => (string) now()->timestamp . rand(1000,9999),
                    'invoice_description' => config('constants.invoice.description'),
                    'total'               => config('constants.invoice.price')
                ],
                'currency_code' => config('constants.invoice.currency'),
                'ip'            => $request->ip() ?: '127.0.0.1',
                'domain'        => config('constants.invoice.domain'),
                'user_id'       => config('constants.invoice.user_id'),
                'last_three_transactions' => [
                    ['amount' => 100, 'utr' => 'UTR1234567890'],
                    ['amount' => 99,  'utr' => 'UTR1234567891'],
                    ['amount' => 98,  'utr' => 'UTR1234567892'],
                ]
            ];

            Log::info('Create Transaction payload: ' . json_encode($payload));

            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrls['create_transaction'], $payload);

            Log::info('Create Transaction response status: ' . $response->status());
            Log::info('Create Transaction response body: ' . $response->body());

            $json = $response->json();
            if (is_array($json) && isset($json[0])) $json = $json[0];

            return response()->json($json);
        } catch (\Exception $e) {
            Log::error('Create Transaction Exception: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'error_message' => 'Server error while creating transaction: ' . $e->getMessage(),
            ]);
        }
    }

    public function getDepositDetails(Request $request)
    {
        $token = $request->input('token');
        if (!$token) return response()->json(['status' => false, 'error_message' => 'Missing token']);

        try {
            $payload = [
                'merchant_key' => $this->merchantKey,
                'token'        => $token,
                'type'         => 'upi',
            ];

            Log::info('Get Deposit Details payload: ' . json_encode($payload));

            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrls['get_deposit_details'], $payload);

            Log::info('Get Deposit Details body: ' . $response->body());

            $json = $response->json();
            if (is_array($json) && isset($json[0])) $json = $json[0];

            return response()->json($json);
        } catch (\Exception $e) {
            Log::error('Get Deposit Details Exception: ' . $e->getMessage());
            return response()->json(['status' => false, 'error_message' => $e->getMessage()]);
        }
    }

    public function validateTransaction(Request $request)
    {
        $token = $request->input('token');
        if (!$token) return response()->json(['status' => false, 'error_message' => 'Missing token']);

        try {
            $payload = [
                'token'        => $token,
                'merchant_key' => $this->merchantKeyValidate,
            ];

            Log::info('Validate Transaction payload: ' . json_encode($payload));

            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrls['validate_transaction'], $payload);

            Log::info('Validate Transaction body: ' . $response->body());

            $json = $response->json();
            if (is_array($json) && isset($json[0])) $json = $json[0];

            return response()->json($json);
        } catch (\Exception $e) {
            Log::error('Validate Transaction Exception: ' . $e->getMessage());
            return response()->json(['status' => false, 'error_message' => $e->getMessage()]);
        }
    }
}
