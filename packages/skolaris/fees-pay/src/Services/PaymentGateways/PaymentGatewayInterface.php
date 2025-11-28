<?php

namespace Skolaris\FeesPay\Services\PaymentGateways;

interface PaymentGatewayInterface
{
    public function initiatePayment(array $data);
    public function verifyPayment(string $reference);
}
