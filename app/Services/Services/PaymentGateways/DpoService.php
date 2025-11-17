<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DpoService implements PaymentGatewayInterface
{
    protected $config;
    protected array $credentials;
    protected bool $isTestMode;
    protected string $baseUrl;

    public function __construct(?string $context = 'landlord_billing', bool $isTenant = false)
    {
        $configModel = $isTenant ? TenantPaymentGatewayConfig::class : PaymentGatewayConfig::class;
        
        $query = $configModel::where('gateway', 'dpo')
            ->where('is_active', true);
            
        if (!$isTenant) {
            $query->where('context', $context);
        }
        
        $this->config = $query->firstOrFail();

        $this->credentials = $this->config->getDecryptedCredentials();
        $this->isTestMode = $this->config->settings['is_test_mode'] ?? false;
        $this->baseUrl = $this->isTestMode 
            ? 'https://secure.3gdirectpay.com'
            : 'https://secure.3gdirectpay.com';
    }

    public function initiatePayment(array $paymentData): array
    {
        try {
            $xml = $this->buildPaymentXml($paymentData);

            $response = Http::withBody($xml, 'text/xml')
                ->post("{$this->baseUrl}/API/v6/");

            if (!$response->successful()) {
                throw new \Exception('DPO API Error: ' . $response->body());
            }

            $result = simplexml_load_string($response->body());

            if ((string)$result->Result !== '000') {
                throw new \Exception($result->ResultExplanation ?? 'DPO payment creation failed');
            }

            $transToken = (string)$result->TransToken;

            return [
                'success' => true,
                'transaction_id' => $transToken,
                'payment_url' => "{$this->baseUrl}/payv2.php?ID={$transToken}",
                'raw_response' => json_decode(json_encode($result), true),
            ];

        } catch (\Exception $e) {
            Log::error('DPO initiation failed', [
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
            $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<API3G>
    <CompanyToken>{$this->credentials['company_token']}</CompanyToken>
    <Request>verifyToken</Request>
    <TransactionToken>{$transactionId}</TransactionToken>
</API3G>
XML;

            $response = Http::withBody($xml, 'text/xml')
                ->post("{$this->baseUrl}/API/v6/");

            if (!$response->successful()) {
                throw new \Exception('DPO verification failed: ' . $response->body());
            }

            $result = simplexml_load_string($response->body());

            if ((string)$result->Result !== '000') {
                throw new \Exception($result->ResultExplanation ?? 'Verification failed');
            }

            $approved = (string)$result->TransactionApproved === '1';

            return [
                'success' => true,
                'status' => $approved ? 'Completed' : 'Pending',
                'is_completed' => $approved,
                'raw_response' => json_decode(json_encode($result), true),
            ];

        } catch (\Exception $e) {
            Log::error('DPO verification failed', [
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
            $transToken = $request->input('TransactionToken');
            
            if (!$transToken) {
                throw new \Exception('No TransactionToken in webhook');
            }

            // Verify transaction with DPO API
            $verification = $this->verifyPayment($transToken);

            return [
                'success' => true,
                'transaction_id' => $transToken,
                'is_completed' => $verification['is_completed'] ?? false,
                'status' => $verification['status'] ?? 'Unknown',
                'raw_data' => $request->all(),
            ];

        } catch (\Exception $e) {
            Log::error('DPO webhook processing failed', [
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
        return $response['payment_url'] ?? null;
    }

    public function validateWebhookSignature(Request $request): bool
    {
        // DPO recommends verifying via API call
        return true;
    }

    public function getGatewayName(): string
    {
        return 'dpo';
    }

    /**
     * Build XML for payment creation
     */
    protected function buildPaymentXml(array $paymentData): string
    {
        $reference = $paymentData['reference'] ?? uniqid('txn_');
        $amount = number_format($paymentData['amount'], 2, '.', '');
        $currency = $paymentData['currency'];
        $description = $paymentData['description'] ?? 'Payment';
        $returnUrl = $paymentData['return_url'] ?? route('landlord.payment.success');
        $backUrl = $paymentData['cancel_url'] ?? route('landlord.payment.cancel');
        $serviceDate = now()->format('Y/m/d H:i');
        
        $email = $paymentData['payer_email'] ?? 'customer@example.com';
        $phone = $paymentData['payer_phone'] ?? '';

        return <<<XML
<?xml version="1.0" encoding="utf-8"?>
<API3G>
    <CompanyToken>{$this->credentials['company_token']}</CompanyToken>
    <Request>createToken</Request>
    <Transaction>
        <PaymentAmount>{$amount}</PaymentAmount>
        <PaymentCurrency>{$currency}</PaymentCurrency>
        <CompanyRef>{$reference}</CompanyRef>
        <RedirectURL>{$returnUrl}</RedirectURL>
        <BackURL>{$backUrl}</BackURL>
        <CompanyRefUnique>0</CompanyRefUnique>
        <PTL>5</PTL>
    </Transaction>
    <Services>
        <Service>
            <ServiceType>{$this->credentials['service_type']}</ServiceType>
            <ServiceDescription>{$description}</ServiceDescription>
            <ServiceDate>{$serviceDate}</ServiceDate>
        </Service>
    </Services>
</API3G>
XML;
    }
}
