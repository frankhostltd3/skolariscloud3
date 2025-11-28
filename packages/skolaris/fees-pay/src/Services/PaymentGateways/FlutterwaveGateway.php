<?php

namespace Skolaris\FeesPay\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;

class FlutterwaveGateway implements PaymentGatewayInterface
{
    protected $baseUrl = 'https://api.flutterwave.com/v3';
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('fees-pay.payment_gateways.flutterwave.secret_key');
    }

    public function initiatePayment(array $data)
    {
        $response = Http::withToken($this->secretKey)->post($this->baseUrl . '/payments', [
            'tx_ref' => $data['tx_ref'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'redirect_url' => $data['redirect_url'],
            'customer' => [
                'email' => $data['email'],
                'phonenumber' => $data['phone'] ?? null,
                'name' => $data['name'],
            ],
            'customizations' => [
                'title' => 'School Fees Payment',
            ],
        ]);

        return $response->json();
    }

    public function verifyPayment(string $reference)
    {
        $response = Http::withToken($this->secretKey)->get($this->baseUrl . "/transactions/{$reference}/verify");
        return $response->json();
    }
}
