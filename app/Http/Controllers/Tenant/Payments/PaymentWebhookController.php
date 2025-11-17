<?php

namespace App\Http\Controllers\Tenant\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentEvent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class PaymentWebhookController extends Controller
{
    /**
     * Minimal shared-secret verification.
     */
    protected function verify(Request $request, string $provider): bool
    {
        $header = $request->header('X-Payment-Signature');
        $secret = Config::get("services.payments.secrets.$provider");
        if (!$secret || !$header) {
            return false;
        }
        $payload = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $header);
    }

    /**
     * Common handler to mark an order paid after basic validation.
     */
    protected function handlePaid(Request $request, string $provider)
    {
        if (!$this->verify($request, $provider)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }
        $data = $request->all();
        // Expecting at least order_id and status='success'
        $orderId = $data['order_id'] ?? null;
        $status  = strtolower((string)($data['status'] ?? ''));
        if (!$orderId || ($status !== 'success' && $status !== 'paid')) {
            return response()->json(['message' => 'Bad payload'], 422);
        }

        /** @var Order|null $order */
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        // Persist the event first
        $event = PaymentEvent::create([
            'order_id' => $order->id,
            'provider' => $provider,
            'event_id' => (string)($data['event_id'] ?? ''),
            'status' => $status,
            'payload' => $data,
        ]);

        // Idempotency: if already paid/cancelled, just acknowledge
        if ($order->status === 'paid') {
            return response()->json(['message' => 'Already paid'], 200);
        }
        if ($order->status === 'cancelled') {
            return response()->json(['message' => 'Order cancelled'], 200);
        }

        // Simple retry/backoff for transient failures when saving order
        $attempts = 0;
        $maxAttempts = 3;
        $delayMs = 150; // base delay
        while (true) {
            try {
                $order->forceFill([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => $order->payment_method ?: $provider,
                ])->save();
                break;
            } catch (\Throwable $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    Log::error('Failed to mark order paid after retries', [
                        'provider' => $provider,
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                    return response()->json(['message' => 'Temporary error'], 503);
                }
                usleep($delayMs * 1000);
                $delayMs *= 2; // exponential backoff
            }
        }

        Log::info('Order marked paid via webhook', [
            'provider' => $provider,
            'order_id' => $order->id,
            'event_id' => $event->event_id,
        ]);

        // Audit log entry
        try {
            AuditLog::create([
                'tenant_id' => tenant('id') ?? null,
                'user_id' => null, // system action via webhook
                'action' => 'order_paid_webhook',
                'description' => 'Order marked as paid via webhook',
                'ip' => request()->ip(),
                'context' => json_encode([
                    'order_id' => $order->id,
                    'provider' => $provider,
                    'event_id' => $event->event_id,
                ]),
            ]);
        } catch (\Throwable $e) {
            // Non-fatal
        }

        return response()->json(['message' => 'ok']);
    }

    public function stripe(Request $request)
    {
        return $this->handlePaid($request, 'stripe');
    }

    public function paypal(Request $request)
    {
        return $this->handlePaid($request, 'paypal');
    }

    public function flutterwave(Request $request)
    {
        return $this->handlePaid($request, 'flutterwave');
    }

    public function mobileMoney(Request $request)
    {
        return $this->handlePaid($request, 'mobile_money');
    }
}
