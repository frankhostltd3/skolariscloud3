<?php

namespace App\Services\Payments;

use JsonSerializable;

class PaymentResult implements JsonSerializable
{
    public function __construct(
        public bool $success = false,
        public string $status = 'unknown',
        public ?string $transactionId = null,
        public ?string $providerTransactionId = null,
        public ?string $externalId = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $message = null,
        public ?string $errorCode = null,
        public ?string $paymentUrl = null,
        public array $providerResponse = [],
        public array $metadata = []
    ) {}

    /**
     * Create a successful payment result
     */
    public static function success(
        string $transactionId,
        float $amount = null,
        string $message = 'Payment successful',
        array $providerResponse = []
    ): self {
        return new self(
            success: true,
            status: 'completed',
            transactionId: $transactionId,
            amount: $amount,
            message: $message,
            providerResponse: $providerResponse
        );
    }

    /**
     * Create a pending payment result
     */
    public static function pending(
        string $transactionId,
        string $message = 'Payment pending',
        string $paymentUrl = null,
        array $providerResponse = []
    ): self {
        return new self(
            success: true,
            status: 'pending',
            transactionId: $transactionId,
            message: $message,
            paymentUrl: $paymentUrl,
            providerResponse: $providerResponse
        );
    }

    /**
     * Create a failed payment result
     */
    public static function failed(
        string $message,
        string $errorCode = null,
        array $providerResponse = []
    ): self {
        return new self(
            success: false,
            status: 'failed',
            message: $message,
            errorCode: $errorCode,
            providerResponse: $providerResponse
        );
    }

    /**
     * Create an error result
     */
    public static function error(string $message, string $errorCode = null): self
    {
        return new self(
            success: false,
            status: 'error',
            message: $message,
            errorCode: $errorCode
        );
    }

    /**
     * Check if payment was successful
     */
    public function isSuccessful(): bool
    {
        return $this->success && $this->status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->success && $this->status === 'pending';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return !$this->success || in_array($this->status, ['failed', 'error', 'rejected', 'cancelled']);
    }

    /**
     * Check if needs redirect (for redirect-based payment flows)
     */
    public function needsRedirect(): bool
    {
        return !empty($this->paymentUrl);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'completed', 'successful' => 'bg-success',
            'pending', 'processing' => 'bg-warning text-dark',
            'failed', 'error' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            'expired' => 'bg-dark',
            'refunded' => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'completed' => 'Completed',
            'successful' => 'Successful',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'failed' => 'Failed',
            'error' => 'Error',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'refunded' => 'Refunded',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status' => $this->status,
            'transaction_id' => $this->transactionId,
            'provider_transaction_id' => $this->providerTransactionId,
            'external_id' => $this->externalId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'message' => $this->message,
            'error_code' => $this->errorCode,
            'payment_url' => $this->paymentUrl,
            'provider_response' => $this->providerResponse,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * JSON serialization
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            status: $data['status'] ?? 'unknown',
            transactionId: $data['transaction_id'] ?? $data['transactionId'] ?? null,
            providerTransactionId: $data['provider_transaction_id'] ?? $data['providerTransactionId'] ?? null,
            externalId: $data['external_id'] ?? $data['externalId'] ?? null,
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            message: $data['message'] ?? null,
            errorCode: $data['error_code'] ?? $data['errorCode'] ?? null,
            paymentUrl: $data['payment_url'] ?? $data['paymentUrl'] ?? null,
            providerResponse: $data['provider_response'] ?? $data['providerResponse'] ?? [],
            metadata: $data['metadata'] ?? []
        );
    }
}
