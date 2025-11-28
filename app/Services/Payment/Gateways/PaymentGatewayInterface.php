<?php

namespace App\Services\Payment\Gateways;

use App\Models\BookstoreOrder;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment request.
     *
     * @param BookstoreOrder $order
     * @return array ['success' => bool, 'redirect_url' => ?string, 'transaction_id' => ?string, 'message' => ?string]
     */
    public function initiatePayment(BookstoreOrder $order): array;

    /**
     * Verify a payment transaction.
     *
     * @param string $transactionId
     * @return array ['success' => bool, 'status' => string, 'message' => ?string]
     */
    public function verifyPayment(string $transactionId): array;
}
