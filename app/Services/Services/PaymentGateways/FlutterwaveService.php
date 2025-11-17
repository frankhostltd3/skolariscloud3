<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService implements PaymentGatewayInterface
{
    protected $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct(?string $context = 'landlord_billing', bool $isTenant = false)
    {
        $configModel = $isTenant ? TenantPaymentGatewayConfig::class : PaymentGatewayConfig::class;
        
        $query = $configModel::where('gateway', 'flutterwave')
            ->where('is_active', true);
            
        if (!$isTenant) {
            $query->where('context', $context);
        }
        
        $this->config = $query->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $reference = $paymentData['reference'] ?? 'FLW_' . uniqid();

            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->credentials['secret_key'],
                ])
                ->post("{$this->baseUrl}/payments", [
                    'tx_ref' => $reference,
                    'amount' => $paymentData['amount'],
                    'currency' => $paymentData['currency'],
                    'redirect_url' => $paymentData['return_url'] ?? route('landlord.payment.success'),
                    'payment_options' => 'card,mobilemoneyghana,ussd',
                    'customer' => [
                        'email' => $paymentData['payer_email'],
                        'name' => $paymentData['payer_name'] ?? 'Customer',
                        'phonenumber' => $paymentData['payer_phone'] ?? '',
                    ],
                    'customizations' => [
                        'title' => config('app.name'),
                        'description' => $paymentData['description'] ?? 'Payment',
                        'logo' => asset('logo.png'),
                    ],
                ]);

            if (!$response->successful()) {
                throw new \Exception('Flutterwave API Error: ' . $response->body());
            }

            $data = $response->json();

            if ($data['status'] !== 'success') {
                throw new \Exception($data['message'] ?? 'Flutterwave request failed');
            }

            return [
                'success' => true,
                'transaction_id' => $reference,
                'payment_url' => $data['data']['link'] ?? null,
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Flutterwave initiation failed', [
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
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->credentials['secret_key'],
                ])
                ->get("{$this->baseUrl}/transactions/verify_by_reference?tx_ref={$transactionId}");

            if (!$response->successful()) {
                throw new \Exception('Flutterwave verification failed: ' . $response->body());
            }

            $data = $response->json();

            if ($data['status'] !== 'success') {
                throw new \Exception($data['message'] ?? 'Verification failed');
            }

            $txData = $data['data'] ?? [];
            $status = $txData['status'] ?? 'unknown';

            return [
                'success' => true,
                'status' => $status,
                'is_completed' => $status === 'successful',
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Flutterwave verification failed', [
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

            $data = $request->all();
            $eventType = $data['event'] ?? '';
            $txData = $data['data'] ?? [];

            $transactionId = $txData['tx_ref'] ?? null;
            $status = $txData['status'] ?? 'unknown';

            return [
                'success' => true,
                'event_type' => $eventType,
                'transaction_id' => $transactionId,
                'status' => $status,
                'is_completed' => $status === 'successful',
                'amount' => $txData['amount'] ?? null,
                'currency' => $txData['currency'] ?? null,
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('Flutterwave webhook processing failed', [
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
        return $response['data']['link'] ?? null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        $secretHash = $this->credentials['secret_key'];
        $signature = $request->header('verif-hash');

        return $signature === $secretHash;
    }

    public function getGatewayName(): string
    {
        return 'flutterwave';
    }
}
