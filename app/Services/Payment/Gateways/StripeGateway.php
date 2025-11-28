<?php

namespace App\Services\Payment\Gateways;

use App\Models\BookstoreOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    protected $secretKey;
    protected $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
    }

    public function initiatePayment(BookstoreOrder $order): array
    {
        if (!$this->secretKey) {
            return ['success' => false, 'message' => 'Stripe is not configured.'];
        }

        try {
            $lineItems = [];
            foreach ($order->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd', // Assuming USD for now, should be dynamic
                        'product_data' => [
                            'name' => $item->book_title,
                        ],
                        'unit_amount' => (int) ($item->unit_price * 100), // Cents
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            $response = Http::withToken($this->secretKey)
                ->asForm()
                ->post("{$this->baseUrl}/checkout/sessions", [
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('bookstore.payment.callback', ['gateway' => 'stripe', 'order_id' => $order->id, 'status' => 'success', 'session_id' => '{CHECKOUT_SESSION_ID}']),
                    'cancel_url' => route('bookstore.payment.callback', ['gateway' => 'stripe', 'order_id' => $order->id, 'status' => 'cancel']),
                    'client_reference_id' => $order->id,
                    'customer_email' => $order->customer_email,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'redirect_url' => $data['url'],
                    'transaction_id' => $data['id'],
                ];
            }

            Log::error('Stripe Payment Initiation Failed', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Failed to initiate Stripe payment.'];

        } catch (\Exception $e) {
            Log::error('Stripe Payment Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred while processing payment.'];
        }
    }

    public function verifyPayment(string $sessionId): array
    {
        if (!$this->secretKey) {
            return ['success' => false, 'message' => 'Stripe is not configured.'];
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/checkout/sessions/{$sessionId}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['payment_status'] === 'paid') {
                    return ['success' => true, 'status' => 'completed'];
                }
                return ['success' => false, 'status' => $data['payment_status'], 'message' => 'Payment not completed.'];
            }

            return ['success' => false, 'message' => 'Failed to verify payment.'];

        } catch (\Exception $e) {
            Log::error('Stripe Verification Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred while verifying payment.'];
        }
    }
}
