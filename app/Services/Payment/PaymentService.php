<?php

namespace App\Services\Payment;

use App\Models\BookstoreOrder;
use App\Services\Payment\Gateways\PaymentGatewayInterface;
use App\Services\Payment\Gateways\StripeGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use InvalidArgumentException;

class PaymentService
{
    public function getGateway(string $gatewayName): PaymentGatewayInterface
    {
        return match ($gatewayName) {
            'card' => new StripeGateway(), // Mapping 'card' to Stripe
            'stripe' => new StripeGateway(),
            'paypal' => new PayPalGateway(),
            // 'flutterwave' => new FlutterwaveGateway(),
            // 'mtn_momo' => new MtnMomoGateway(),
            // 'airtel_money' => new AirtelMoneyGateway(),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gatewayName}"),
        };
    }

    public function initiatePayment(string $gatewayName, BookstoreOrder $order): array
    {
        try {
            $gateway = $this->getGateway($gatewayName);
            return $gateway->initiatePayment($order);
        } catch (InvalidArgumentException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyPayment(string $gatewayName, string $transactionId): array
    {
        try {
            $gateway = $this->getGateway($gatewayName);
            return $gateway->verifyPayment($transactionId);
        } catch (InvalidArgumentException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
