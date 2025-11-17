<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService implements PaymentGatewayInterface
{
    protected $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl;

    public function __construct(?string $context = 'student_fees', bool $isTenant = true)
    {
        $configModel = $isTenant ? TenantPaymentGatewayConfig::class : PaymentGatewayConfig::class;
        
        $query = $configModel::where('gateway', 'mpesa')
            ->where('is_active', true);
            
        if (!$isTenant) {
            $query->where('context', $context);
        }
        
        $this->config = $query->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
        $this->baseUrl = $this->isTestMode 
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $shortcode = $this->credentials['shortcode'];
            $passkey = $this->credentials['passkey'];
            $timestamp = date('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);

            // Clean phone number
            $phone = $this->formatPhoneNumber($paymentData['phone']);

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => (int) $paymentData['amount'],
                    'PartyA' => $phone,
                    'PartyB' => $shortcode,
                    'PhoneNumber' => $phone,
                    'CallBackURL' => $paymentData['callback_url'] ?? route('tenant.webhooks.mpesa'),
                    'AccountReference' => $paymentData['reference'] ?? 'Payment',
                    'TransactionDesc' => $paymentData['description'] ?? 'Payment'
                ]);

            if (!$response->successful()) {
                throw new \Exception('M-PESA API Error: ' . $response->body());
            }

            $data = $response->json();

            if (($data['ResponseCode'] ?? '') !== '0') {
                throw new \Exception($data['ResponseDescription'] ?? 'M-PESA request failed');
            }

            return [
                'success' => true,
                'transaction_id' => $data['CheckoutRequestID'],
                'merchant_request_id' => $data['MerchantRequestID'],
                'payment_url' => null, // M-PESA uses STK Push, no redirect URL
                'message' => 'STK Push sent. Please enter M-PESA PIN on your phone.',
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('M-PESA initiation failed', [
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
            
            $shortcode = $this->credentials['shortcode'];
            $passkey = $this->credentials['passkey'];
            $timestamp = date('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
                    'BusinessShortCode' => $shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'CheckoutRequestID' => $transactionId,
                ]);

            if (!$response->successful()) {
                throw new \Exception('M-PESA verification failed: ' . $response->body());
            }

            $data = $response->json();
            $resultCode = $data['ResultCode'] ?? '';

            return [
                'success' => true,
                'status' => $resultCode,
                'is_completed' => $resultCode === '0',
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('M-PESA verification failed', [
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
            $data = $request->all();
            
            $body = $data['Body'] ?? [];
            $stkCallback = $body['stkCallback'] ?? [];
            
            $resultCode = $stkCallback['ResultCode'] ?? '';
            $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? '';
            
            $callbackMetadata = $stkCallback['CallbackMetadata'] ?? [];
            $items = $callbackMetadata['Item'] ?? [];
            
            $mpesaReceiptNumber = null;
            $phoneNumber = null;
            $amount = null;
            
            foreach ($items as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $mpesaReceiptNumber = $item['Value'];
                } elseif ($item['Name'] === 'PhoneNumber') {
                    $phoneNumber = $item['Value'];
                } elseif ($item['Name'] === 'Amount') {
                    $amount = $item['Value'];
                }
            }

            return [
                'success' => true,
                'transaction_id' => $checkoutRequestId,
                'mpesa_receipt' => $mpesaReceiptNumber,
                'phone' => $phoneNumber,
                'amount' => $amount,
                'is_completed' => $resultCode === 0,
                'result_description' => $stkCallback['ResultDesc'] ?? '',
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('M-PESA webhook processing failed', [
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
        // M-PESA uses STK Push, no redirect URL needed
        return null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        // M-PESA doesn't use signature validation
        // Validation is done through IP whitelisting and response structure
        return true;
    }

    public function getGatewayName(): string
    {
        return 'mpesa';
    }

    /**
     * Get OAuth access token
     */
    protected function getAccessToken(): string
    {
        $response = Http::withBasicAuth(
                $this->credentials['consumer_key'],
                $this->credentials['consumer_secret']
            )
            ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

        if (!$response->successful()) {
            throw new \Exception('Failed to get M-PESA access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    /**
     * Format phone number to M-PESA format (254...)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, and plus signs
        $phone = preg_replace('/[\s\-\+]/', '', $phone);
        
        // If starts with 0, replace with 254
        if (substr($phone, 0, 1) === '0') {
            return '254' . substr($phone, 1);
        }
        
        // If doesn't start with 254, prepend it
        if (substr($phone, 0, 3) !== '254') {
            return '254' . $phone;
        }
        
        return $phone;
    }
}
