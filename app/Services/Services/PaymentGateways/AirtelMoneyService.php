<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirtelMoneyService implements PaymentGatewayInterface
{
    protected $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl;

    public function __construct(?string $context = 'landlord_billing', bool $isTenant = false)
    {
        $configModel = $isTenant ? TenantPaymentGatewayConfig::class : PaymentGatewayConfig::class;
        
        $query = $configModel::where('gateway', 'airtel_money')
            ->where('is_active', true);
            
        if (!$isTenant) {
            $query->where('context', $context);
        }
        
        $this->config = $query->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
        $this->baseUrl = $this->isTestMode 
            ? 'https://openapiuat.airtel.africa'
            : 'https://openapi.airtel.africa';
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $reference = $paymentData['reference'] ?? uniqid('txn_');
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'X-Country' => $this->credentials['country'] ?? 'UG',
                'X-Currency' => $paymentData['currency'],
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/merchant/v1/payments/", [
                'reference' => $reference,
                'subscriber' => [
                    'country' => $this->credentials['country'] ?? 'UG',
                    'currency' => $paymentData['currency'],
                    'msisdn' => $this->formatPhoneNumber($paymentData['payer_phone']),
                ],
                'transaction' => [
                    'amount' => $paymentData['amount'],
                    'country' => $this->credentials['country'] ?? 'UG',
                    'currency' => $paymentData['currency'],
                    'id' => $reference,
                ],
            ]);

            if (!$response->successful()) {
                throw new \Exception('Airtel Money API Error: ' . $response->body());
            }

            $data = $response->json();

            if (isset($data['status']['code']) && $data['status']['code'] !== 200) {
                throw new \Exception($data['status']['message'] ?? 'Airtel Money request failed');
            }

            $transactionId = $data['data']['transaction']['id'] ?? $reference;

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => $data['data']['transaction']['status'] ?? 'PENDING',
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Airtel Money initiation failed', [
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

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'X-Country' => $this->credentials['country'] ?? 'UG',
                'X-Currency' => 'UGX', // Default, should be from config
            ])->get("{$this->baseUrl}/standard/v1/payments/{$transactionId}");

            if (!$response->successful()) {
                throw new \Exception('Airtel Money verification failed: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['data']['transaction']['status'] ?? 'UNKNOWN';

            return [
                'success' => true,
                'status' => $status,
                'is_completed' => in_array(strtoupper($status), ['SUCCESS', 'TS']),
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Airtel Money verification failed', [
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
            $transactionId = $request->input('transaction', [])['id'] ?? null;
            
            if (!$transactionId) {
                throw new \Exception('No transaction ID in webhook');
            }

            $status = $request->input('transaction', [])['status'] ?? 'UNKNOWN';

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'is_completed' => in_array(strtoupper($status), ['SUCCESS', 'TS']),
                'status' => $status,
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('Airtel Money webhook processing failed', [
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
        // Airtel Money uses STK Push, no redirect URL
        return null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        // Implement signature validation if required by Airtel
        return true;
    }

    public function getGatewayName(): string
    {
        return 'airtel_money';
    }

    /**
     * Get OAuth access token
     */
    protected function getAccessToken(): string
    {
        $clientId = $this->credentials['client_id'];
        $clientSecret = $this->credentials['client_secret'];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/auth/oauth2/token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Airtel Money access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    /**
     * Format phone number for Airtel (country code + number without +)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure it starts with country code
        if (!str_starts_with($phone, '256') && strlen($phone) === 9) {
            $phone = '256' . $phone; // Uganda default
        }
        
        return $phone;
    }
}
