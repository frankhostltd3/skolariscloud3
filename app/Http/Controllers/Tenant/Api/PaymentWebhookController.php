<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileMoneyGateway;
use App\Models\PaymentTransaction;
use App\Services\Payments\MobileMoneyPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected MobileMoneyPaymentService $paymentService;

    public function __construct(MobileMoneyPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming payment webhook/callback
     * POST /api/payments/webhook/{provider}
     */
    public function handle(Request $request, string $provider): JsonResponse
    {
        Log::info("Payment webhook received for provider: {$provider}", [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            // Verify webhook signature if applicable
            if (!$this->verifyWebhookSignature($request, $provider)) {
                Log::warning("Webhook signature verification failed for {$provider}");
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Find the appropriate gateway
            $gateway = $this->findGatewayByProvider($provider, $request);
            
            if (!$gateway) {
                Log::warning("No gateway found for provider: {$provider}");
                return response()->json(['error' => 'Gateway not found'], 404);
            }

            // Process the webhook
            $this->paymentService->setGateway($gateway);
            $result = $this->paymentService->handleWebhook($request->all());

            // Find and update the transaction
            $transactionId = $result->transactionId ?? $result->externalId;
            
            if ($transactionId) {
                $transaction = PaymentTransaction::findByIdentifier($transactionId);
                
                if ($transaction) {
                    $this->updateTransaction($transaction, $result, $request->all());
                }
            }

            Log::info("Webhook processed successfully", [
                'provider' => $provider,
                'status' => $result->status,
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'success' => true,
                'status' => $result->status,
                'message' => 'Webhook processed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error("Webhook processing error: {$e->getMessage()}", [
                'provider' => $provider,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle MTN MoMo callback
     */
    public function mtnMomo(Request $request): JsonResponse
    {
        return $this->handle($request, 'mtn_momo');
    }

    /**
     * Handle M-Pesa callback
     */
    public function mpesa(Request $request): JsonResponse
    {
        return $this->handle($request, 'mpesa');
    }

    /**
     * Handle Airtel Money callback
     */
    public function airtelMoney(Request $request): JsonResponse
    {
        return $this->handle($request, 'airtel_money');
    }

    /**
     * Handle Flutterwave callback
     */
    public function flutterwave(Request $request): JsonResponse
    {
        return $this->handle($request, 'flutterwave');
    }

    /**
     * Handle Paystack callback
     */
    public function paystack(Request $request): JsonResponse
    {
        return $this->handle($request, 'paystack');
    }

    /**
     * Handle Orange Money callback
     */
    public function orangeMoney(Request $request): JsonResponse
    {
        return $this->handle($request, 'orange_money');
    }

    /**
     * Handle Yo Payments callback
     */
    public function yoPayments(Request $request): JsonResponse
    {
        return $this->handle($request, 'yo_payments');
    }

    /**
     * Handle DPO callback
     */
    public function dpo(Request $request): JsonResponse
    {
        return $this->handle($request, 'dpo');
    }

    /**
     * Verify webhook signature based on provider
     */
    protected function verifyWebhookSignature(Request $request, string $provider): bool
    {
        // Skip verification for testing/sandbox environments
        if (app()->environment('local', 'testing')) {
            return true;
        }

        return match($provider) {
            'flutterwave' => $this->verifyFlutterwaveSignature($request),
            'paystack' => $this->verifyPaystackSignature($request),
            'stripe' => $this->verifyStripeSignature($request),
            default => true, // Other providers may not have signature verification
        };
    }

    /**
     * Verify Flutterwave webhook signature
     */
    protected function verifyFlutterwaveSignature(Request $request): bool
    {
        $signature = $request->header('verif-hash');
        $gateway = $this->findGatewayByProvider('flutterwave', $request);
        
        if (!$gateway || !$signature) {
            return true; // Allow if no signature configured
        }

        $secretHash = $gateway->webhook_secret;
        
        return $signature === $secretHash;
    }

    /**
     * Verify Paystack webhook signature
     */
    protected function verifyPaystackSignature(Request $request): bool
    {
        $signature = $request->header('x-paystack-signature');
        $gateway = $this->findGatewayByProvider('paystack', $request);
        
        if (!$gateway || !$signature) {
            return true;
        }

        $payload = $request->getContent();
        $computed = hash_hmac('sha512', $payload, $gateway->secret_key);
        
        return hash_equals($computed, $signature);
    }

    /**
     * Verify Stripe webhook signature
     */
    protected function verifyStripeSignature(Request $request): bool
    {
        $signature = $request->header('stripe-signature');
        $gateway = $this->findGatewayByProvider('stripe', $request);
        
        if (!$gateway || !$signature) {
            return true;
        }

        try {
            \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $gateway->webhook_secret
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Find gateway by provider, extracting school from webhook data if possible
     */
    protected function findGatewayByProvider(string $provider, Request $request): ?MobileMoneyGateway
    {
        // Try to extract transaction reference to find the gateway
        $reference = $this->extractReference($request, $provider);
        
        if ($reference) {
            $transaction = PaymentTransaction::findByIdentifier($reference);
            
            if ($transaction && $transaction->gateway_id) {
                return MobileMoneyGateway::find($transaction->gateway_id);
            }
        }

        // Try from metadata
        $schoolId = $request->input('metadata.school_id') 
                 ?? $request->input('customFields.school_id')
                 ?? $request->input('school_id');

        if ($schoolId) {
            return MobileMoneyGateway::where('school_id', $schoolId)
                ->where('provider', $provider)
                ->where('is_active', true)
                ->first();
        }

        // Fallback: find any active gateway with this provider
        return MobileMoneyGateway::where('provider', $provider)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Extract transaction reference from webhook payload
     */
    protected function extractReference(Request $request, string $provider): ?string
    {
        return match($provider) {
            'mtn_momo' => $request->input('externalId') ?? $request->input('referenceId'),
            'mpesa' => $request->input('TransactionID') ?? $request->input('TransID'),
            'airtel_money' => $request->input('id') ?? $request->input('transaction.id'),
            'flutterwave' => $request->input('data.tx_ref') ?? $request->input('txRef'),
            'paystack' => $request->input('data.reference'),
            'orange_money' => $request->input('txnId') ?? $request->input('transactionId'),
            'yo_payments' => $request->input('internal_reference') ?? $request->input('InternalReference'),
            'dpo' => $request->input('TransactionRef'),
            default => $request->input('reference') ?? $request->input('transaction_id'),
        };
    }

    /**
     * Update transaction based on webhook result
     */
    protected function updateTransaction(PaymentTransaction $transaction, $result, array $webhookData): void
    {
        $transaction->update([
            'callback_data' => $webhookData,
            'callback_received_at' => now(),
            'provider_response' => array_merge(
                $transaction->provider_response ?? [],
                ['webhook' => $webhookData]
            ),
        ]);

        switch ($result->status) {
            case 'completed':
            case 'successful':
                $transaction->markAsCompleted($webhookData);
                $this->notifyPaymentSuccess($transaction);
                break;

            case 'failed':
            case 'rejected':
            case 'error':
                $transaction->markAsFailed(
                    $result->message ?? 'Payment failed',
                    $result->errorCode,
                    $webhookData
                );
                $this->notifyPaymentFailure($transaction);
                break;

            case 'cancelled':
                $transaction->markAsCancelled($result->message ?? 'Payment cancelled by user');
                break;

            case 'expired':
                $transaction->markAsExpired();
                break;

            case 'refunded':
                $transaction->markAsRefunded($webhookData);
                break;
        }
    }

    /**
     * Send notification on payment success
     */
    protected function notifyPaymentSuccess(PaymentTransaction $transaction): void
    {
        // Update related model if exists
        if ($transaction->payable && method_exists($transaction->payable, 'onPaymentReceived')) {
            $transaction->payable->onPaymentReceived($transaction);
        }

        // You could dispatch a notification here
        // event(new PaymentReceived($transaction));
    }

    /**
     * Send notification on payment failure
     */
    protected function notifyPaymentFailure(PaymentTransaction $transaction): void
    {
        // You could dispatch a notification here
        // event(new PaymentFailed($transaction));
    }
}
