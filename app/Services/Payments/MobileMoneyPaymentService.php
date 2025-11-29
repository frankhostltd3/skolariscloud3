<?php

namespace App\Services\Payments;

use App\Models\MobileMoneyGateway;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MobileMoneyPaymentService
{
    protected ?MobileMoneyGateway $gateway = null;
    protected array $config = [];

    /**
     * Initialize with a specific gateway
     */
    public function __construct(?MobileMoneyGateway $gateway = null)
    {
        if ($gateway) {
            $this->setGateway($gateway);
        }
    }

    /**
     * Set the gateway to use
     */
    public function setGateway(MobileMoneyGateway $gateway): self
    {
        $this->gateway = $gateway;
        $this->config = $gateway->getConfiguration();
        return $this;
    }

    /**
     * Get the current gateway
     */
    public function getGateway(): ?MobileMoneyGateway
    {
        return $this->gateway;
    }

    /**
     * Use the default gateway for the current school
     */
    public function useDefault(): self
    {
        $gateway = MobileMoneyGateway::getDefault();
        
        if (!$gateway) {
            throw new \Exception('No default mobile money gateway configured.');
        }
        
        return $this->setGateway($gateway);
    }

    /**
     * Use a specific gateway by slug
     */
    public function useGateway(string $slug): self
    {
        $school = request()->attributes->get('currentSchool');
        
        $gateway = MobileMoneyGateway::where('school_id', $school->id)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
        
        if (!$gateway) {
            throw new \Exception("Gateway '{$slug}' not found or not active.");
        }
        
        return $this->setGateway($gateway);
    }

    /**
     * Initiate a payment request
     */
    public function initiatePayment(array $data): PaymentResult
    {
        $this->validateGateway();
        
        // Validate required fields
        $this->validatePaymentData($data);
        
        // Route to appropriate provider handler
        return match($this->gateway->provider) {
            'mtn_momo' => $this->initiateMtnMomo($data),
            'mpesa' => $this->initiateMpesa($data),
            'airtel_money' => $this->initiateAirtelMoney($data),
            'flutterwave' => $this->initiateFlutterwave($data),
            'paystack' => $this->initiatePaystack($data),
            'orange_money' => $this->initiateOrangeMoney($data),
            'yo_payments' => $this->initiateYoPayments($data),
            'dpo' => $this->initiateDpo($data),
            'stripe' => $this->initiateStripe($data),
            'paypal' => $this->initiatePaypal($data),
            default => $this->initiateCustom($data),
        };
    }

    /**
     * Check payment status
     */
    public function checkStatus(string $transactionId): PaymentResult
    {
        $this->validateGateway();
        
        return match($this->gateway->provider) {
            'mtn_momo' => $this->checkMtnMomoStatus($transactionId),
            'mpesa' => $this->checkMpesaStatus($transactionId),
            'airtel_money' => $this->checkAirtelMoneyStatus($transactionId),
            'flutterwave' => $this->checkFlutterwaveStatus($transactionId),
            'paystack' => $this->checkPaystackStatus($transactionId),
            default => $this->checkCustomStatus($transactionId),
        };
    }

    /**
     * Process a webhook callback
     */
    public function handleWebhook(array $payload): PaymentResult
    {
        $this->validateGateway();
        
        return match($this->gateway->provider) {
            'mtn_momo' => $this->handleMtnMomoWebhook($payload),
            'mpesa' => $this->handleMpesaWebhook($payload),
            'airtel_money' => $this->handleAirtelMoneyWebhook($payload),
            'flutterwave' => $this->handleFlutterwaveWebhook($payload),
            'paystack' => $this->handlePaystackWebhook($payload),
            default => $this->handleCustomWebhook($payload),
        };
    }

    // =============================================
    // MTN MOBILE MONEY
    // =============================================

    protected function initiateMtnMomo(array $data): PaymentResult
    {
        try {
            $token = $this->getMtnMomoToken();
            
            $referenceId = Str::uuid()->toString();
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => $this->gateway->environment,
                'Ocp-Apim-Subscription-Key' => $this->config['subscription_key'],
                'Content-Type' => 'application/json',
            ])->post($this->gateway->getEndpoint('request_to_pay'), [
                'amount' => (string) $data['amount'],
                'currency' => $data['currency'] ?? $this->gateway->currency_code,
                'externalId' => $data['reference'] ?? $referenceId,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $this->formatPhoneNumber($data['phone'], 'UG'),
                ],
                'payerMessage' => $data['description'] ?? 'Payment',
                'payeeNote' => $data['note'] ?? 'School Fee Payment',
            ]);

            if ($response->status() === 202) {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $referenceId,
                    message: 'Payment request sent. Please approve on your phone.',
                    providerResponse: ['reference_id' => $referenceId]
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: 'Failed to initiate payment: ' . $response->body(),
                providerResponse: $response->json()
            );
        } catch (\Exception $e) {
            Log::error('MTN MoMo payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function getMtnMomoToken(): string
    {
        $cacheKey = 'mtn_momo_token_' . $this->gateway->id;
        
        if ($this->gateway->hasValidToken()) {
            return $this->gateway->access_token;
        }
        
        return Cache::remember($cacheKey, 3500, function () {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(
                    $this->config['api_user'] . ':' . $this->config['api_password']
                ),
                'Ocp-Apim-Subscription-Key' => $this->config['subscription_key'],
            ])->post($this->gateway->getEndpoint('token'));

            if ($response->successful()) {
                $token = $response->json()['access_token'];
                $expiresIn = $response->json()['expires_in'] ?? 3600;
                
                $this->gateway->update([
                    'access_token' => $token,
                    'token_expires_at' => now()->addSeconds($expiresIn),
                ]);
                
                return $token;
            }
            
            throw new \Exception('Failed to get MTN MoMo access token');
        });
    }

    protected function checkMtnMomoStatus(string $referenceId): PaymentResult
    {
        try {
            $token = $this->getMtnMomoToken();
            
            $endpoint = str_replace('{referenceId}', $referenceId, $this->gateway->getEndpoint('request_status'));
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Target-Environment' => $this->gateway->environment,
                'Ocp-Apim-Subscription-Key' => $this->config['subscription_key'],
            ])->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                $status = strtolower($data['status'] ?? 'unknown');
                
                return new PaymentResult(
                    success: $status === 'successful',
                    status: $this->mapMtnStatus($status),
                    transactionId: $referenceId,
                    providerTransactionId: $data['financialTransactionId'] ?? null,
                    amount: $data['amount'] ?? null,
                    message: $data['reason'] ?? 'Status: ' . $status,
                    providerResponse: $data
                );
            }
            
            return new PaymentResult(
                success: false,
                status: 'error',
                message: 'Failed to check status',
                providerResponse: $response->json()
            );
        } catch (\Exception $e) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function mapMtnStatus(string $status): string
    {
        return match($status) {
            'successful' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'rejected' => 'rejected',
            'timeout' => 'expired',
            default => 'unknown',
        };
    }

    protected function handleMtnMomoWebhook(array $payload): PaymentResult
    {
        $status = strtolower($payload['status'] ?? 'unknown');
        
        return new PaymentResult(
            success: $status === 'successful',
            status: $this->mapMtnStatus($status),
            transactionId: $payload['externalId'] ?? null,
            providerTransactionId: $payload['financialTransactionId'] ?? null,
            amount: $payload['amount'] ?? null,
            providerResponse: $payload
        );
    }

    // =============================================
    // M-PESA (SAFARICOM)
    // =============================================

    protected function initiateMpesa(array $data): PaymentResult
    {
        try {
            $token = $this->getMpesaToken();
            
            $timestamp = now()->format('YmdHis');
            $password = base64_encode(
                $this->config['short_code'] . 
                ($this->config['custom_fields']['passkey'] ?? '') . 
                $timestamp
            );
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($this->gateway->getEndpoint('stk_push'), [
                'BusinessShortCode' => $this->config['short_code'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $data['amount'],
                'PartyA' => $this->formatPhoneNumber($data['phone'], 'KE'),
                'PartyB' => $this->config['short_code'],
                'PhoneNumber' => $this->formatPhoneNumber($data['phone'], 'KE'),
                'CallBackURL' => $this->gateway->callback_url,
                'AccountReference' => $data['reference'] ?? 'Payment',
                'TransactionDesc' => $data['description'] ?? 'School Fee Payment',
            ]);

            $result = $response->json();

            if (($result['ResponseCode'] ?? '') === '0') {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $result['CheckoutRequestID'] ?? null,
                    message: 'STK push sent. Please enter your M-Pesa PIN.',
                    providerResponse: $result
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: $result['ResponseDescription'] ?? 'Failed to initiate M-Pesa payment',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            Log::error('M-Pesa payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function getMpesaToken(): string
    {
        $cacheKey = 'mpesa_token_' . $this->gateway->id;
        
        return Cache::remember($cacheKey, 3500, function () {
            $credentials = base64_encode(
                $this->config['client_id'] . ':' . $this->config['client_secret']
            );
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($this->gateway->getEndpoint('token'));

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get M-Pesa access token');
        });
    }

    protected function checkMpesaStatus(string $checkoutRequestId): PaymentResult
    {
        try {
            $token = $this->getMpesaToken();
            
            $timestamp = now()->format('YmdHis');
            $password = base64_encode(
                $this->config['short_code'] . 
                ($this->config['custom_fields']['passkey'] ?? '') . 
                $timestamp
            );
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->post($this->gateway->getEndpoint('stk_query'), [
                'BusinessShortCode' => $this->config['short_code'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ]);

            $result = $response->json();
            $resultCode = $result['ResultCode'] ?? -1;

            return new PaymentResult(
                success: $resultCode === 0,
                status: $resultCode === 0 ? 'completed' : ($resultCode === 1032 ? 'cancelled' : 'failed'),
                transactionId: $checkoutRequestId,
                message: $result['ResultDesc'] ?? 'Unknown status',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function handleMpesaWebhook(array $payload): PaymentResult
    {
        $body = $payload['Body']['stkCallback'] ?? $payload;
        $resultCode = $body['ResultCode'] ?? -1;
        
        $metadata = collect($body['CallbackMetadata']['Item'] ?? [])
            ->pluck('Value', 'Name')
            ->toArray();

        return new PaymentResult(
            success: $resultCode === 0,
            status: $resultCode === 0 ? 'completed' : 'failed',
            transactionId: $body['CheckoutRequestID'] ?? null,
            providerTransactionId: $metadata['MpesaReceiptNumber'] ?? null,
            amount: $metadata['Amount'] ?? null,
            message: $body['ResultDesc'] ?? null,
            providerResponse: $payload
        );
    }

    // =============================================
    // AIRTEL MONEY
    // =============================================

    protected function initiateAirtelMoney(array $data): PaymentResult
    {
        try {
            $token = $this->getAirtelMoneyToken();
            
            $transactionId = 'AM' . time() . rand(1000, 9999);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'X-Country' => $this->gateway->country_code,
                'X-Currency' => $this->gateway->currency_code,
            ])->post($this->gateway->getEndpoint('payments'), [
                'reference' => $data['reference'] ?? $transactionId,
                'subscriber' => [
                    'country' => $this->gateway->country_code,
                    'currency' => $this->gateway->currency_code,
                    'msisdn' => $this->formatPhoneNumber($data['phone'], $this->gateway->country_code),
                ],
                'transaction' => [
                    'amount' => $data['amount'],
                    'country' => $this->gateway->country_code,
                    'currency' => $this->gateway->currency_code,
                    'id' => $transactionId,
                ],
            ]);

            $result = $response->json();

            if (($result['status']['code'] ?? '') === '200') {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $transactionId,
                    message: $result['status']['message'] ?? 'Payment initiated',
                    providerResponse: $result
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: $result['status']['message'] ?? 'Failed to initiate payment',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            Log::error('Airtel Money payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function getAirtelMoneyToken(): string
    {
        $cacheKey = 'airtel_money_token_' . $this->gateway->id;
        
        return Cache::remember($cacheKey, 3500, function () {
            $response = Http::post($this->gateway->getEndpoint('token'), [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get Airtel Money access token');
        });
    }

    protected function checkAirtelMoneyStatus(string $transactionId): PaymentResult
    {
        try {
            $token = $this->getAirtelMoneyToken();
            
            $endpoint = str_replace('{id}', $transactionId, $this->gateway->getEndpoint('status'));
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Country' => $this->gateway->country_code,
                'X-Currency' => $this->gateway->currency_code,
            ])->get($endpoint);

            $result = $response->json();
            $status = strtolower($result['data']['transaction']['status'] ?? 'unknown');

            return new PaymentResult(
                success: $status === 'ts',
                status: $status === 'ts' ? 'completed' : ($status === 'tf' ? 'failed' : 'pending'),
                transactionId: $transactionId,
                providerTransactionId: $result['data']['transaction']['airtel_money_id'] ?? null,
                message: $result['data']['transaction']['message'] ?? null,
                providerResponse: $result
            );
        } catch (\Exception $e) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function handleAirtelMoneyWebhook(array $payload): PaymentResult
    {
        $transaction = $payload['transaction'] ?? [];
        $status = strtolower($transaction['status_code'] ?? 'unknown');

        return new PaymentResult(
            success: $status === 'ts',
            status: $status === 'ts' ? 'completed' : 'failed',
            transactionId: $transaction['id'] ?? null,
            providerTransactionId: $transaction['airtel_money_id'] ?? null,
            amount: $transaction['amount'] ?? null,
            providerResponse: $payload
        );
    }

    // =============================================
    // FLUTTERWAVE
    // =============================================

    protected function initiateFlutterwave(array $data): PaymentResult
    {
        try {
            $txRef = 'FW' . time() . rand(1000, 9999);
            $country = strtolower($this->gateway->country_code);
            
            $endpoint = str_replace('{country}', $country, $this->gateway->getEndpoint('charge'));
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['secret_key'],
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'tx_ref' => $data['reference'] ?? $txRef,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? $this->gateway->currency_code,
                'phone_number' => $this->formatPhoneNumber($data['phone'], $this->gateway->country_code),
                'email' => $data['email'] ?? 'customer@school.com',
                'fullname' => $data['name'] ?? 'Customer',
                'redirect_url' => $this->gateway->return_url,
                'meta' => [
                    'invoice_id' => $data['invoice_id'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                ],
            ]);

            $result = $response->json();

            if (($result['status'] ?? '') === 'success') {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $result['data']['tx_ref'] ?? $txRef,
                    providerTransactionId: $result['data']['id'] ?? null,
                    message: $result['message'] ?? 'Payment initiated',
                    providerResponse: $result,
                    redirectUrl: $result['meta']['authorization']['redirect'] ?? null
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: $result['message'] ?? 'Failed to initiate payment',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            Log::error('Flutterwave payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function checkFlutterwaveStatus(string $transactionId): PaymentResult
    {
        try {
            $endpoint = str_replace('{id}', $transactionId, $this->gateway->getEndpoint('verify'));
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['secret_key'],
            ])->get($endpoint);

            $result = $response->json();
            $status = strtolower($result['data']['status'] ?? 'unknown');

            return new PaymentResult(
                success: $status === 'successful',
                status: $status === 'successful' ? 'completed' : ($status === 'pending' ? 'pending' : 'failed'),
                transactionId: $result['data']['tx_ref'] ?? null,
                providerTransactionId: $result['data']['id'] ?? null,
                amount: $result['data']['amount'] ?? null,
                providerResponse: $result
            );
        } catch (\Exception $e) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function handleFlutterwaveWebhook(array $payload): PaymentResult
    {
        // Verify webhook signature
        $signature = request()->header('verif-hash');
        if ($signature !== $this->config['webhook_secret']) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: 'Invalid webhook signature'
            );
        }

        $data = $payload['data'] ?? [];
        $status = strtolower($data['status'] ?? 'unknown');

        return new PaymentResult(
            success: $status === 'successful',
            status: $status === 'successful' ? 'completed' : 'failed',
            transactionId: $data['tx_ref'] ?? null,
            providerTransactionId: $data['id'] ?? null,
            amount: $data['amount'] ?? null,
            providerResponse: $payload
        );
    }

    // =============================================
    // PAYSTACK
    // =============================================

    protected function initiatePaystack(array $data): PaymentResult
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['secret_key'],
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $data['email'],
                'amount' => (int) ($data['amount'] * 100), // Paystack uses kobo/pesewas
                'currency' => $data['currency'] ?? $this->gateway->currency_code,
                'reference' => $data['reference'] ?? 'PS' . time() . rand(1000, 9999),
                'callback_url' => $this->gateway->callback_url,
                'channels' => ['mobile_money'],
                'metadata' => [
                    'invoice_id' => $data['invoice_id'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                    'phone' => $data['phone'] ?? null,
                ],
            ]);

            $result = $response->json();

            if ($result['status'] ?? false) {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $result['data']['reference'] ?? null,
                    message: 'Payment initialized',
                    providerResponse: $result,
                    redirectUrl: $result['data']['authorization_url'] ?? null
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: $result['message'] ?? 'Failed to initialize payment',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            Log::error('Paystack payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function checkPaystackStatus(string $reference): PaymentResult
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['secret_key'],
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            $result = $response->json();
            $status = strtolower($result['data']['status'] ?? 'unknown');

            return new PaymentResult(
                success: $status === 'success',
                status: $status === 'success' ? 'completed' : ($status === 'pending' ? 'pending' : 'failed'),
                transactionId: $reference,
                providerTransactionId: $result['data']['id'] ?? null,
                amount: ($result['data']['amount'] ?? 0) / 100,
                providerResponse: $result
            );
        } catch (\Exception $e) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function handlePaystackWebhook(array $payload): PaymentResult
    {
        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        if ($event !== 'charge.success') {
            return new PaymentResult(
                success: false,
                status: 'failed',
                message: 'Payment not successful',
                providerResponse: $payload
            );
        }

        return new PaymentResult(
            success: true,
            status: 'completed',
            transactionId: $data['reference'] ?? null,
            providerTransactionId: $data['id'] ?? null,
            amount: ($data['amount'] ?? 0) / 100,
            providerResponse: $payload
        );
    }

    // =============================================
    // YO! PAYMENTS (UGANDA)
    // =============================================

    protected function initiateYoPayments(array $data): PaymentResult
    {
        try {
            $transactionRef = 'YO' . time() . rand(1000, 9999);
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <AutoCreate>
                <Request>
                    <APIUsername>' . $this->config['api_user'] . '</APIUsername>
                    <APIPassword>' . $this->config['api_password'] . '</APIPassword>
                    <Method>acdepositfunds</Method>
                    <Account>' . $this->formatPhoneNumber($data['phone'], 'UG') . '</Account>
                    <Amount>' . $data['amount'] . '</Amount>
                    <Narrative>' . ($data['description'] ?? 'School Fee Payment') . '</Narrative>
                    <ExternalReference>' . ($data['reference'] ?? $transactionRef) . '</ExternalReference>
                    <ProviderReferenceText>' . ($data['description'] ?? 'Payment') . '</ProviderReferenceText>
                </Request>
            </AutoCreate>';

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->send('POST', $this->config['api_base_url'], [
                'body' => $xml,
            ]);

            // Parse XML response
            $xmlResponse = simplexml_load_string($response->body());
            $status = (string) ($xmlResponse->Response->Status ?? 'ERROR');

            if ($status === 'OK') {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: (string) ($xmlResponse->Response->TransactionReference ?? $transactionRef),
                    message: (string) ($xmlResponse->Response->StatusMessage ?? 'Payment initiated'),
                    providerResponse: json_decode(json_encode($xmlResponse), true)
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: (string) ($xmlResponse->Response->StatusMessage ?? 'Failed to initiate payment'),
                providerResponse: json_decode(json_encode($xmlResponse), true)
            );
        } catch (\Exception $e) {
            Log::error('Yo! Payments error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    // =============================================
    // ORANGE MONEY
    // =============================================

    protected function initiateOrangeMoney(array $data): PaymentResult
    {
        try {
            $transactionId = 'OM' . time() . rand(1000, 9999);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getOrangeMoneyToken(),
                'Content-Type' => 'application/json',
            ])->post($this->gateway->api_base_url . '/payment', [
                'merchant_key' => $this->config['custom_fields']['merchant_key'] ?? '',
                'currency' => $this->gateway->currency_code,
                'order_id' => $data['reference'] ?? $transactionId,
                'amount' => $data['amount'],
                'return_url' => $this->gateway->return_url,
                'cancel_url' => $this->gateway->cancel_url,
                'notif_url' => $this->gateway->callback_url,
                'lang' => 'en',
                'reference' => $data['description'] ?? 'Payment',
            ]);

            $result = $response->json();

            if (($result['status'] ?? 0) === 201) {
                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $transactionId,
                    message: 'Redirecting to Orange Money',
                    providerResponse: $result,
                    redirectUrl: $result['payment_url'] ?? null
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: $result['message'] ?? 'Failed to initiate payment',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            Log::error('Orange Money payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function getOrangeMoneyToken(): string
    {
        $cacheKey = 'orange_money_token_' . $this->gateway->id;
        
        return Cache::remember($cacheKey, 3500, function () {
            $response = Http::asForm()->post($this->gateway->api_base_url . '/oauth/token', [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get Orange Money access token');
        });
    }

    // =============================================
    // DPO (DIRECT PAY ONLINE)
    // =============================================

    protected function initiateDpo(array $data): PaymentResult
    {
        try {
            $transactionToken = $this->createDpoToken($data);
            
            if (!$transactionToken) {
                return new PaymentResult(
                    success: false,
                    status: 'failed',
                    message: 'Failed to create DPO transaction token'
                );
            }

            $paymentUrl = $this->config['api_base_url'] . '/payv2.php?ID=' . $transactionToken;

            return new PaymentResult(
                success: true,
                status: 'pending',
                transactionId: $transactionToken,
                message: 'Redirecting to DPO payment page',
                redirectUrl: $paymentUrl
            );
        } catch (\Exception $e) {
            Log::error('DPO payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function createDpoToken(array $data): ?string
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
            <CompanyToken>' . ($this->config['custom_fields']['company_token'] ?? '') . '</CompanyToken>
            <Request>createToken</Request>
            <Transaction>
                <PaymentAmount>' . $data['amount'] . '</PaymentAmount>
                <PaymentCurrency>' . ($data['currency'] ?? $this->gateway->currency_code) . '</PaymentCurrency>
                <CompanyRef>' . ($data['reference'] ?? 'REF' . time()) . '</CompanyRef>
                <RedirectURL>' . $this->gateway->return_url . '</RedirectURL>
                <BackURL>' . $this->gateway->cancel_url . '</BackURL>
                <CompanyRefUnique>0</CompanyRefUnique>
                <PTL>5</PTL>
            </Transaction>
            <Services>
                <Service>
                    <ServiceType>' . ($this->config['custom_fields']['service_type'] ?? '5525') . '</ServiceType>
                    <ServiceDescription>' . ($data['description'] ?? 'School Fee Payment') . '</ServiceDescription>
                    <ServiceDate>' . now()->format('Y/m/d H:i') . '</ServiceDate>
                </Service>
            </Services>
        </API3G>';

        $response = Http::withHeaders([
            'Content-Type' => 'application/xml',
        ])->send('POST', $this->config['api_base_url'] . '/API/v6/', [
            'body' => $xml,
        ]);

        $xmlResponse = simplexml_load_string($response->body());
        
        if ((string) ($xmlResponse->Result ?? '') === '000') {
            return (string) $xmlResponse->TransToken;
        }
        
        return null;
    }

    // =============================================
    // STRIPE
    // =============================================

    protected function initiateStripe(array $data): PaymentResult
    {
        try {
            \Stripe\Stripe::setApiKey($this->config['secret_key']);
            
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($data['currency'] ?? $this->gateway->currency_code ?? 'usd'),
                        'product_data' => [
                            'name' => $data['description'] ?? 'School Fee Payment',
                        ],
                        'unit_amount' => (int) ($data['amount'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->gateway->return_url . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->gateway->cancel_url,
                'metadata' => [
                    'invoice_id' => $data['invoice_id'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                ],
            ]);

            return new PaymentResult(
                success: true,
                status: 'pending',
                transactionId: $session->id,
                message: 'Redirecting to Stripe checkout',
                redirectUrl: $session->url,
                providerResponse: ['session_id' => $session->id]
            );
        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    // =============================================
    // PAYPAL
    // =============================================

    protected function initiatePaypal(array $data): PaymentResult
    {
        try {
            $token = $this->getPaypalToken();
            
            $baseUrl = $this->gateway->environment === 'production' 
                ? 'https://api.paypal.com' 
                : 'https://api.sandbox.paypal.com';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $data['reference'] ?? 'ORDER' . time(),
                    'amount' => [
                        'currency_code' => $data['currency'] ?? $this->gateway->currency_code ?? 'USD',
                        'value' => number_format($data['amount'], 2, '.', ''),
                    ],
                    'description' => $data['description'] ?? 'School Fee Payment',
                ]],
                'application_context' => [
                    'return_url' => $this->gateway->return_url,
                    'cancel_url' => $this->gateway->cancel_url,
                ],
            ]);

            $result = $response->json();

            if (isset($result['id'])) {
                $approvalUrl = collect($result['links'] ?? [])
                    ->firstWhere('rel', 'approve')['href'] ?? null;

                return new PaymentResult(
                    success: true,
                    status: 'pending',
                    transactionId: $result['id'],
                    message: 'Redirecting to PayPal',
                    redirectUrl: $approvalUrl,
                    providerResponse: $result
                );
            }

            return new PaymentResult(
                success: false,
                status: 'failed',
                message: $result['message'] ?? 'Failed to create PayPal order',
                providerResponse: $result
            );
        } catch (\Exception $e) {
            Log::error('PayPal payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function getPaypalToken(): string
    {
        $cacheKey = 'paypal_token_' . $this->gateway->id;
        
        return Cache::remember($cacheKey, 32000, function () {
            $baseUrl = $this->gateway->environment === 'production' 
                ? 'https://api.paypal.com' 
                : 'https://api.sandbox.paypal.com';

            $response = Http::asForm()
                ->withBasicAuth($this->config['client_id'], $this->config['client_secret'])
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get PayPal access token');
        });
    }

    // =============================================
    // CUSTOM GATEWAY
    // =============================================

    protected function initiateCustom(array $data): PaymentResult
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
            ];

            // Add authentication based on available credentials
            if ($this->config['public_key']) {
                $headers['X-API-Key'] = $this->config['public_key'];
            }
            if ($this->config['secret_key']) {
                $headers['Authorization'] = 'Bearer ' . $this->config['secret_key'];
            }

            $payload = array_merge($data, [
                'callback_url' => $this->gateway->callback_url,
                'return_url' => $this->gateway->return_url,
            ]);

            // Add any custom fields
            if (!empty($this->config['custom_fields'])) {
                $payload = array_merge($payload, $this->config['custom_fields']);
            }

            $response = Http::withHeaders($headers)
                ->post($this->config['api_base_url'] . '/payments', $payload);

            $result = $response->json();

            return new PaymentResult(
                success: $response->successful(),
                status: $response->successful() ? 'pending' : 'failed',
                transactionId: $result['transaction_id'] ?? $result['id'] ?? $result['reference'] ?? null,
                message: $result['message'] ?? ($response->successful() ? 'Payment initiated' : 'Failed'),
                providerResponse: $result,
                redirectUrl: $result['redirect_url'] ?? $result['payment_url'] ?? null
            );
        } catch (\Exception $e) {
            Log::error('Custom gateway payment error: ' . $e->getMessage());
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function checkCustomStatus(string $transactionId): PaymentResult
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
            ];

            if ($this->config['public_key']) {
                $headers['X-API-Key'] = $this->config['public_key'];
            }
            if ($this->config['secret_key']) {
                $headers['Authorization'] = 'Bearer ' . $this->config['secret_key'];
            }

            $response = Http::withHeaders($headers)
                ->get($this->config['api_base_url'] . '/payments/' . $transactionId);

            $result = $response->json();

            return new PaymentResult(
                success: $response->successful() && ($result['status'] ?? '') === 'completed',
                status: $result['status'] ?? 'unknown',
                transactionId: $transactionId,
                amount: $result['amount'] ?? null,
                providerResponse: $result
            );
        } catch (\Exception $e) {
            return new PaymentResult(
                success: false,
                status: 'error',
                message: $e->getMessage()
            );
        }
    }

    protected function handleCustomWebhook(array $payload): PaymentResult
    {
        return new PaymentResult(
            success: ($payload['status'] ?? '') === 'completed',
            status: $payload['status'] ?? 'unknown',
            transactionId: $payload['transaction_id'] ?? $payload['reference'] ?? null,
            amount: $payload['amount'] ?? null,
            providerResponse: $payload
        );
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    protected function validateGateway(): void
    {
        if (!$this->gateway) {
            throw new \Exception('No payment gateway configured. Call useDefault() or useGateway() first.');
        }
        
        if (!$this->gateway->is_active) {
            throw new \Exception('The selected payment gateway is not active.');
        }
    }

    protected function validatePaymentData(array $data): void
    {
        if (empty($data['amount']) || $data['amount'] <= 0) {
            throw new \InvalidArgumentException('Valid payment amount is required.');
        }
        
        if (empty($data['phone']) && empty($data['email'])) {
            throw new \InvalidArgumentException('Phone number or email is required.');
        }
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber(string $phone, string $countryCode): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Country-specific formatting
        $prefixes = [
            'UG' => '256',
            'KE' => '254',
            'TZ' => '255',
            'RW' => '250',
            'GH' => '233',
            'NG' => '234',
            'ZA' => '27',
            'ZM' => '260',
        ];
        
        $prefix = $prefixes[$countryCode] ?? '';
        
        // If starts with 0, replace with country code
        if (str_starts_with($phone, '0')) {
            $phone = $prefix . substr($phone, 1);
        }
        // If doesn't start with country code, add it
        elseif ($prefix && !str_starts_with($phone, $prefix)) {
            $phone = $prefix . $phone;
        }
        
        return $phone;
    }

    /**
     * Record a completed payment in the database
     */
    public function recordPayment(PaymentResult $result, ?Invoice $invoice = null, array $additionalData = []): ?Payment
    {
        if (!$result->success || $result->status !== 'completed') {
            return null;
        }

        $school = request()->attributes->get('currentSchool');

        $payment = Payment::create([
            'school_id' => $school->id,
            'invoice_id' => $invoice?->id ?? $additionalData['invoice_id'] ?? null,
            'student_id' => $invoice?->student_id ?? $additionalData['student_id'] ?? null,
            'receipt_number' => 'RCP-' . now()->format('Ym') . '-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT),
            'amount' => $result->amount ?? $additionalData['amount'] ?? 0,
            'payment_method' => 'mobile_money',
            'payment_date' => now(),
            'reference_number' => $result->providerTransactionId ?? $result->transactionId,
            'gateway_slug' => $this->gateway->slug,
            'gateway_response' => json_encode($result->providerResponse),
            'status' => 'completed',
            'notes' => "Paid via {$this->gateway->name}",
        ]);

        // Update invoice if provided
        if ($invoice) {
            $invoice->paid_amount += $payment->amount;
            $invoice->balance = $invoice->total_amount - $invoice->paid_amount;
            $invoice->status = $invoice->balance <= 0 ? 'paid' : 'partial';
            $invoice->save();
        }

        return $payment;
    }

    /**
     * Get all active gateways for the current school
     */
    public static function getAvailableGateways(): \Illuminate\Database\Eloquent\Collection
    {
        $school = request()->attributes->get('currentSchool');
        
        return MobileMoneyGateway::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('sort_order')
            ->get();
    }
}
