<?php

namespace Skolaris\FeesPay\Services\PaymentGateways;

use Stripe\StripeClient;

class StripeGateway implements PaymentGatewayInterface
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('fees-pay.payment_gateways.stripe.api_secret'));
    }

    public function initiatePayment(array $data)
    {
        // Create a Checkout Session
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $data['currency'],
                    'product_data' => [
                        'name' => 'School Fees',
                    ],
                    'unit_amount' => $data['amount'] * 100, // Stripe expects cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $data['redirect_url'] . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $data['cancel_url'],
            'client_reference_id' => $data['tx_ref'],
        ]);

        return ['link' => $session->url];
    }

    public function verifyPayment(string $reference)
    {
        // Reference here is the session ID
        $session = $this->stripe->checkout->sessions->retrieve($reference);
        return $session;
    }
}
