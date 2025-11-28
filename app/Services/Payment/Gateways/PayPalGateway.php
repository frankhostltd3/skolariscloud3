<?php

namespace App\Services\Payment\Gateways;

use App\Models\BookstoreOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalGateway implements PaymentGatewayInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $mode = config('services.paypal.mode', 'sandbox');
        $this->baseUrl = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    protected function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('PayPal Access Token Failed', ['response' => $response->body()]);
        return null;
    }

    public function initiatePayment(BookstoreOrder $order): array
    {
        if (!$this->clientId || !$this->clientSecret) {
            return ['success' => false, 'message' => 'PayPal is not configured.'];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with PayPal.'];
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => $order->order_number,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($order->total, 2, '.', ''),
                        ],
                    ]],
                    'application_context' => [
                        'return_url' => route('bookstore.payment.callback', ['gateway' => 'paypal', 'order_id' => $order->id, 'status' => 'success']),
                        'cancel_url' => route('bookstore.payment.callback', ['gateway' => 'paypal', 'order_id' => $order->id, 'status' => 'cancel']),
                        'brand_name' => config('app.name'),
                        'user_action' => 'PAY_NOW',
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $approveLink = collect($data['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                return [
                    'success' => true,
                    'redirect_url' => $approveLink,
                    'transaction_id' => $data['id'],
                ];
            }

            Log::error('PayPal Payment Initiation Failed', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Failed to initiate PayPal payment.'];

        } catch (\Exception $e) {
            Log::error('PayPal Payment Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred while processing payment.'];
        }
    }

    public function verifyPayment(string $orderId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with PayPal.'];
        }

        try {
            // Capture the order
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", [
                    'headers' => ['Content-Type' => 'application/json']
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'COMPLETED') {
                    return ['success' => true, 'status' => 'completed'];
                }
                return ['success' => false, 'status' => $data['status'], 'message' => 'Payment not completed.'];
            }

            // If already captured, check details
            if ($response->status() === 422) { // UNPROCESSABLE_ENTITY often means already captured
                 $detailsResponse = Http::withToken($token)
                    ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

                 if ($detailsResponse->successful()) {
                     $data = $detailsResponse->json();
                     if ($data['status'] === 'COMPLETED') {
                         return ['success' => true, 'status' => 'completed'];
                     }
                 }
            }

            Log::error('PayPal Capture Failed', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Failed to capture payment.'];

        } catch (\Exception $e) {
            Log::error('PayPal Verification Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred while verifying payment.'];
        }
    }
}
