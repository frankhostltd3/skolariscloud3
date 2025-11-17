<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesaPalService implements PaymentGatewayInterface
{
    protected $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl;

    public function __construct(?string $context = 'landlord_billing', bool $isTenant = false)
    {
        $configModel = $isTenant ? TenantPaymentGatewayConfig::class : PaymentGatewayConfig::class;
        
        $query = $configModel::where('gateway', 'pesapal')
            ->where('is_active', true);
            
        if (!$isTenant) {
            $query->where('context', $context);
        }
        
        $this->config = $query->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
        $this->baseUrl = $this->isTestMode 
            ? 'https://cybqa.pesapal.com/pesapalv3'
            : 'https://pay.pesapal.com/v3';
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/api/Transactions/SubmitOrderRequest", [
                    'id' => $paymentData['reference'] ?? uniqid('txn_'),
                    'currency' => $paymentData['currency'],
                    'amount' => $paymentData['amount'],
                    'description' => $paymentData['description'] ?? 'Payment',
                    'callback_url' => $paymentData['return_url'] ?? route('landlord.payment.success'),
                    'notification_id' => $this->credentials['ipn_id'] ?? null,
                    'billing_address' => [
                        'email_address' => $paymentData['payer_email'],
                        'phone_number' => $paymentData['payer_phone'] ?? '',
                        'country_code' => 'KE',
                        'first_name' => $paymentData['payer_name'] ?? 'Customer',
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception('PesaPal API Error: ' . $response->body());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                throw new \Exception($data['error']['message'] ?? 'PesaPal request failed');
            }

            return [
                'success' => true,
                'transaction_id' => $data['order_tracking_id'] ?? $data['merchant_reference'],
                'payment_url' => $data['redirect_url'] ?? null,
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('PesaPal initiation failed', [
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
                ->get("{$this->baseUrl}/api/Transactions/GetTransactionStatus", [
                    'orderTrackingId' => $transactionId
                ]);

            if (!$response->successful()) {
                throw new \Exception('PesaPal verification failed: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['payment_status_description'] ?? 'Unknown';

            return [
                'success' => true,
                'status' => $status,
                'is_completed' => in_array($status, ['Completed', 'Success']),
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('PesaPal verification failed', [
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
            $orderTrackingId = $request->input('OrderTrackingId');
            
            if (!$orderTrackingId) {
                throw new \Exception('No OrderTrackingId in webhook');
            }

            // Verify transaction with PesaPal API
            $verification = $this->verifyPayment($orderTrackingId);

            return [
                'success' => true,
                'transaction_id' => $orderTrackingId,
                'is_completed' => $verification['is_completed'] ?? false,
                'status' => $verification['status'] ?? 'Unknown',
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('PesaPal webhook processing failed', [
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
        return $response['redirect_url'] ?? null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        // PesaPal recommends fetching transaction status for validation
        // rather than trusting webhook directly
        return true;
    }

    public function getGatewayName(): string
    {
        return 'pesapal';
    }

    /**
     * Get OAuth access token
     */
    protected function getAccessToken(): string
    {
        $response = Http::post("{$this->baseUrl}/api/Auth/RequestToken", [
            'consumer_key' => $this->credentials['consumer_key'],
            'consumer_secret' => $this->credentials['consumer_secret'],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PesaPal access token: ' . $response->body());
        }

        return $response->json()['token'];
    }
}
