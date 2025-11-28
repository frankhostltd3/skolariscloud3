<?php

namespace Skolaris\FeesPay\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGatewayInterface
{
    protected $baseUrl;
    protected $clientId;
    protected $secret;

    public function __construct()
    {
        $mode = config('fees-pay.payment_gateways.paypal.mode');
        $this->baseUrl = $mode === 'sandbox' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $this->clientId = config('fees-pay.payment_gateways.paypal.client_id');
        $this->secret = config('fees-pay.payment_gateways.paypal.secret');
    }

    protected function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json()['access_token'];
    }

    public function initiatePayment(array $data)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->post($this->baseUrl . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $data['tx_ref'],
                'amount' => [
                    'currency_code' => $data['currency'],
                    'value' => $data['amount'],
                ],
            ]],
            'application_context' => [
                'return_url' => $data['redirect_url'],
                'cancel_url' => $data['cancel_url'],
            ],
        ]);

        return $response->json();
    }

    public function verifyPayment(string $reference)
    {
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->get($this->baseUrl . "/v2/checkout/orders/{$reference}");
        return $response->json();
    }
}
