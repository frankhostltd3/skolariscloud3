<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MtnMomoService implements PaymentGatewayInterface
{
    protected $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl;

    public function __construct(?string $context = 'landlord_billing', bool $isTenant = false)
    {
        $configModel = $isTenant ? TenantPaymentGatewayConfig::class : PaymentGatewayConfig::class;
        
        $query = $configModel::where('gateway', 'mtn_momo')
            ->where('is_active', true);
            
        if (!$isTenant) {
            $query->where('context', $context);
        }
        
        $this->config = $query->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
        $this->baseUrl = $this->isTestMode 
            ? 'https://sandbox.momodeveloper.mtn.com'
            : 'https://proxy.momoapi.mtn.com';
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $referenceId = $paymentData['reference'] ?? uniqid('txn_');
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => $this->isTestMode ? 'sandbox' : 'production',
                'Ocp-Apim-Subscription-Key' => $this->credentials['subscription_key'],
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/collection/v1_0/requesttopay", [
                'amount' => (string) $paymentData['amount'],
                'currency' => $paymentData['currency'],
                'externalId' => $referenceId,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $this->formatPhoneNumber($paymentData['payer_phone']),
                ],
                'payerMessage' => $paymentData['description'] ?? 'Payment',
                'payeeNote' => $paymentData['description'] ?? 'Payment',
            ]);

            if (!$response->successful()) {
                throw new \Exception('MTN MoMo API Error: ' . $response->body());
            }

            // MTN MoMo returns 202 Accepted with transaction ID in header
            return [
                'success' => true,
                'transaction_id' => $referenceId,
                'status' => 'PENDING',
                'raw_response' => [
                    'reference_id' => $referenceId,
                    'status_code' => $response->status(),
                ],
            ];

        } catch (\Exception $e) {
            Log::error('MTN MoMo initiation failed', [
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
                'X-Target-Environment' => $this->isTestMode ? 'sandbox' : 'production',
                'Ocp-Apim-Subscription-Key' => $this->credentials['subscription_key'],
            ])->get("{$this->baseUrl}/collection/v1_0/requesttopay/{$transactionId}");

            if (!$response->successful()) {
                throw new \Exception('MTN MoMo verification failed: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['status'] ?? 'UNKNOWN';

            return [
                'success' => true,
                'status' => $status,
                'is_completed' => $status === 'SUCCESSFUL',
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('MTN MoMo verification failed', [
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
            $referenceId = $request->input('referenceId');
            
            if (!$referenceId) {
                throw new \Exception('No referenceId in webhook');
            }

            // Verify transaction with MTN API
            $verification = $this->verifyPayment($referenceId);

            return [
                'success' => true,
                'transaction_id' => $referenceId,
                'is_completed' => $verification['is_completed'] ?? false,
                'status' => $verification['status'] ?? 'Unknown',
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('MTN MoMo webhook processing failed', [
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
        // MTN MoMo uses STK Push, no redirect URL
        return null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        // MTN MoMo recommends fetching transaction status for validation
        return true;
    }

    public function getGatewayName(): string
    {
        return 'mtn_momo';
    }

    /**
     * Get OAuth access token
     */
    protected function getAccessToken(): string
    {
        $apiUser = $this->credentials['api_user'];
        $apiKey = $this->credentials['api_key'];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("{$apiUser}:{$apiKey}"),
            'Ocp-Apim-Subscription-Key' => $this->credentials['subscription_key'],
        ])->post("{$this->baseUrl}/collection/token/");

        if (!$response->successful()) {
            throw new \Exception('Failed to get MTN MoMo access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    /**
     * Format phone number for MTN (remove + and spaces)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
