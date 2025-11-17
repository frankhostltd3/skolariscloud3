<?php

namespace App\Http\Controllers\Landlord\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\LandlordInvoice;
use App\Services\PaymentGateways\PayPalService;
use App\Services\PaymentGateways\FlutterwaveService;
use App\Services\PaymentGateways\MpesaService;
use App\Services\PaymentGateways\PesaPalService;
use App\Services\PaymentGateways\DpoService;
use App\Services\PaymentGateways\MtnMomoService;
use App\Services\PaymentGateways\AirtelMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle payment webhook
     */
    public function handle(Request $request, string $gateway)
    {
        Log::info("Webhook received for {$gateway}", $request->all());

        try {
            // Get payment service
            $service = $this->getPaymentService($gateway);

            // Process webhook
            $webhookData = $service->processWebhook($request);

            if (!$webhookData['success']) {
                Log::error("Webhook processing failed for {$gateway}", [
                    'error' => $webhookData['error'] ?? 'Unknown error'
                ]);
                return response()->json(['status' => 'error'], 400);
            }

            // Find transaction
            $transaction = PaymentTransaction::where('transaction_id', $webhookData['transaction_id'])
                ->first();

            if (!$transaction) {
                Log::warning("Transaction not found for webhook", [
                    'gateway' => $gateway,
                    'transaction_id' => $webhookData['transaction_id']
                ]);
                return response()->json(['status' => 'not_found'], 404);
            }

            // Check if already processed
            if ($transaction->isCompleted()) {
                Log::info("Transaction already completed", [
                    'transaction_id' => $transaction->transaction_id
                ]);
                return response()->json(['status' => 'already_processed'], 200);
            }

            // Process payment completion
            if ($webhookData['is_completed']) {
                DB::transaction(function () use ($transaction, $webhookData) {
                    // Mark transaction as completed
                    $transaction->markAsCompleted($webhookData['raw_data'] ?? []);

                    // Update related invoice
                    if ($transaction->transaction_type === 'invoice') {
                        $invoice = LandlordInvoice::find($transaction->related_id);
                        if ($invoice) {
                            $invoice->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                            ]);
                            
                            Log::info("Invoice marked as paid", [
                                'invoice_id' => $invoice->id,
                                'transaction_id' => $transaction->transaction_id
                            ]);
                        }
                    }
                });

                return response()->json(['status' => 'success'], 200);
            }

            // Payment not completed (failed, cancelled, etc.)
            if (isset($webhookData['result_description'])) {
                $transaction->markAsFailed($webhookData['result_description']);
            }

            return response()->json(['status' => 'acknowledged'], 200);

        } catch (\Exception $e) {
            Log::error("Webhook handler exception for {$gateway}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get payment service instance
     */
    protected function getPaymentService(string $gateway)
    {
        return match($gateway) {
            'paypal' => new PayPalService('landlord_billing'),
            'flutterwave' => new FlutterwaveService('landlord_billing', false),
            'mpesa' => new MpesaService('landlord_billing', false),
            'pesapal' => new PesaPalService('landlord_billing'),
            'dpo' => new DpoService('landlord_billing'),
            'mtn_momo' => new MtnMomoService('landlord_billing'),
            'airtel_money' => new AirtelMoneyService('landlord_billing'),
            default => throw new \Exception("Unsupported gateway: {$gateway}"),
        };
    }
}
