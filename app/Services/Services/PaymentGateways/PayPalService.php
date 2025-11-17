<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService implements PaymentGatewayInterface
{
    protected PaymentGatewayConfig $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl;

    public function __construct(?string $context = 'landlord_billing')
    {
        $this->config = PaymentGatewayConfig::where('gateway', 'paypal')
            ->where('context', $context)
            ->where('is_active', true)
            ->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
        $this->baseUrl = $this->isTestMode 
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => $paymentData['reference'] ?? uniqid('pay_'),
                        'amount' => [
                            'currency_code' => $paymentData['currency'],
                            'value' => number_format($paymentData['amount'], 2, '.', '')
                        ],
                        'description' => $paymentData['description'] ?? 'Payment',
                    ]],
                    'application_context' => [
                        'brand_name' => config('app.name'),
                        'return_url' => $paymentData['return_url'] ?? route('landlord.payment.success'),
                        'cancel_url' => $paymentData['cancel_url'] ?? route('landlord.payment.cancel'),
                        'user_action' => 'PAY_NOW',
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception('PayPal API Error: ' . $response->body());
            }

            $data = $response->json();

            return [
                'success' => true,
                'transaction_id' => $data['id'],
                'payment_url' => $this->getPaymentUrl($data),
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('PayPal initiation failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyPayment(string $transactionId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$transactionId}");

            if (!$response->successful()) {
                throw new \Exception('PayPal verification failed: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['status'] ?? 'UNKNOWN';

            return [
                'success' => true,
                'status' => $status,
                'is_completed' => in_array($status, ['APPROVED', 'COMPLETED']),
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('PayPal verification failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function processWebhook(Request $request): array
    {
        try {
            if (!$this->validateWebhookSignature($request)) {
                throw new \Exception('Invalid webhook signature');
            }

            $eventType = $request->input('event_type');
            $resource = $request->input('resource', []);

            $orderId = $resource['id'] ?? null;
            $status = $resource['status'] ?? 'UNKNOWN';

            return [
                'success' => true,
                'event_type' => $eventType,
                'transaction_id' => $orderId,
                'status' => $status,
                'is_completed' => in_array($eventType, [
                    'CHECKOUT.ORDER.APPROVED',
                    'PAYMENT.CAPTURE.COMPLETED'
                ]),
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('PayPal webhook processing failed', [
                'error' => $e->getMessage(),
                'webhook_data' => $request->all()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPaymentUrl(array $response): ?string
    {
        foreach ($response['links'] ?? [] as $link) {
            if ($link['rel'] === 'approve') {
                return $link['href'];
            }
        }
        return null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        // PayPal webhook signature validation
        // In production, implement proper signature validation
        // https://developer.paypal.com/api/rest/webhooks/rest/
        
        $webhookId = $this->credentials['webhook_id'] ?? null;
        
        if (!$webhookId) {
            // If no webhook ID configured, skip validation (development only)
            return true;
        }

        // Implement PayPal webhook verification here
        // This requires the webhook ID and signature headers
        
        return true; // Simplified for now
    }

    public function getGatewayName(): string
    {
        return 'paypal';
    }

    /**
     * Get OAuth access token
     */
    protected function getAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(
                $this->credentials['client_id'],
                $this->credentials['client_secret']
            )
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayPal access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }
}
