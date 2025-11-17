<?php

namespace App\Http\Controllers\Landlord\Billing;

use App\Http\Controllers\Controller;
use App\Models\LandlordInvoice;
use App\Models\PaymentTransaction;
use App\Services\PaymentGateways\PayPalService;
use App\Services\PaymentGateways\FlutterwaveService;
use App\Services\PaymentGateways\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    /**
     * Initiate payment for an invoice
     */
    public function initiate(Request $request, LandlordInvoice $invoice)
    {
        $request->validate([
            'gateway' => 'required|in:paypal,flutterwave,mpesa,pesapal,dpo,mtn_momo,airtel_money',
            'payer_email' => 'required_if:gateway,paypal,flutterwave|email',
            'payer_phone' => [
                'required_if:gateway,mpesa,mtn_momo,airtel_money',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->gateway, ['mpesa','mtn_momo','airtel_money'])) {
                        $digits = preg_replace('/[^0-9]/', '', (string) $value);
                        // Basic E.164-like check (country code + local number)
                        if (strlen($digits) < 10) {
                            return $fail(__('Please enter a valid phone number with country code, e.g., 2547XXXXXXXX for Kenya or 2567XXXXXXXX for Uganda.'));
                        }
                        if ($request->gateway === 'mpesa' && !str_starts_with($digits, '254')) {
                            return $fail(__('For M-PESA, use Kenyan format like 2547XXXXXXXX.'));
                        }
                    }
                }
            ],
            'payer_name' => 'nullable|string|max:255',
        ], [
            'payer_email.required_if' => __('Email is required for this gateway.'),
            'payer_phone.required_if' => __('Phone number is required for this gateway.'),
        ]);

        // Check if invoice is already paid
        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice has already been paid.');
        }

        try {
            DB::beginTransaction();

            // Get payment service
            $service = $this->getPaymentService($request->gateway);

            $paymentData = [
                'amount' => $invoice->amount,
                'currency' => $invoice->currency ?? 'USD',
                'description' => "Invoice #{$invoice->invoice_number} - {$invoice->description}",
                'reference' => "INV-{$invoice->id}-" . uniqid(),
                'payer_email' => $request->payer_email,
                'payer_name' => $request->payer_name ?? $invoice->tenant->name ?? 'Customer',
                'payer_phone' => $request->payer_phone,
                'return_url' => route('landlord.payment.success', ['invoice' => $invoice->id]),
                'cancel_url' => route('landlord.payment.cancel', ['invoice' => $invoice->id]),
                'callback_url' => route('landlord.webhooks.handle', ['gateway' => $request->gateway]),
            ];

            // Initiate payment
            $response = $service->initiatePayment($paymentData);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Payment initiation failed');
            }

            // Store transaction
            $transaction = PaymentTransaction::create([
                'transaction_type' => 'invoice',
                'related_id' => $invoice->id,
                'gateway' => $request->gateway,
                'transaction_id' => $response['transaction_id'],
                'reference' => $paymentData['reference'],
                'amount' => $invoice->amount,
                'currency' => $paymentData['currency'],
                'status' => 'pending',
                'payer_email' => $request->payer_email,
                'payer_name' => $request->payer_name,
                'payer_phone' => $request->payer_phone,
                'description' => $paymentData['description'],
                'payment_url' => $response['payment_url'] ?? null,
                'raw_request' => $paymentData,
                'raw_response' => $response['raw_response'] ?? [],
                'initiated_at' => now(),
            ]);

            DB::commit();

            // If payment URL exists, redirect to gateway
            if ($response['payment_url']) {
                return redirect($response['payment_url']);
            }

            // For M-PESA STK Push, show waiting page
            if ($request->gateway === 'mpesa') {
                return redirect()
                    ->route('landlord.payment.waiting', ['transaction' => $transaction->id])
                    ->with('success', $response['message'] ?? 'Payment initiated. Please enter your M-PESA PIN.');
            }

            return back()->with('success', 'Payment initiated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Payment initiation failed', [
                'invoice_id' => $invoice->id,
                'gateway' => $request->gateway,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Payment initiation failed: ' . $e->getMessage());
        }
    }

    /**
     * Payment success callback
     */
    public function success(Request $request, LandlordInvoice $invoice)
    {
        $transactionId = $request->query('transaction_id') ?? $request->query('tx_ref') ?? $request->query('token');

        if (!$transactionId) {
            return redirect()
                ->route('landlord.billing.invoices.show', $invoice)
                ->with('error', 'Payment verification failed: No transaction ID');
        }

        // Find transaction
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('related_id', $invoice->id)
            ->first();

        if (!$transaction) {
            return redirect()
                ->route('landlord.billing.invoices.show', $invoice)
                ->with('error', 'Transaction not found');
        }

        // Verify payment with gateway
        try {
            $service = $this->getPaymentService($transaction->gateway);
            $verification = $service->verifyPayment($transactionId);

            if ($verification['is_completed']) {
                DB::transaction(function () use ($transaction, $invoice, $verification) {
                    $transaction->markAsCompleted($verification['raw_response'] ?? []);
                    
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                });

                return redirect()
                    ->route('landlord.billing.invoices.show', $invoice)
                    ->with('success', 'Payment completed successfully!');
            }

            return redirect()
                ->route('landlord.billing.invoices.show', $invoice)
                ->with('warning', 'Payment verification pending. Status: ' . ($verification['status'] ?? 'Unknown'));

        } catch (\Exception $e) {
            \Log::error('Payment verification failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('landlord.billing.invoices.show', $invoice)
                ->with('error', 'Payment verification failed');
        }
    }

    /**
     * Payment cancelled callback
     */
    public function cancel(Request $request, LandlordInvoice $invoice)
    {
        return redirect()
            ->route('landlord.billing.invoices.show', $invoice)
            ->with('warning', 'Payment was cancelled');
    }

    /**
     * Payment waiting page (for M-PESA STK Push)
     */
    public function waiting(Request $request, PaymentTransaction $transaction)
    {
        return view('landlord.billing.payment-waiting', compact('transaction'));
    }

    /**
     * Check payment status (API endpoint for AJAX)
     */
    public function checkStatus(Request $request, PaymentTransaction $transaction)
    {
        try {
            $service = $this->getPaymentService($transaction->gateway);
            $verification = $service->verifyPayment($transaction->transaction_id);

            $status = 'pending';
            $message = 'Payment is still pending...';

            if ($verification['is_completed']) {
                $status = 'completed';
                $message = 'Payment completed successfully!';
                
                // Update transaction and invoice
                DB::transaction(function () use ($transaction, $verification) {
                    $transaction->markAsCompleted($verification['raw_response'] ?? []);
                    
                    if ($transaction->transaction_type === 'invoice') {
                        $invoice = LandlordInvoice::find($transaction->related_id);
                        if ($invoice) {
                            $invoice->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                            ]);
                        }
                    }
                });
            } elseif (isset($verification['status']) && in_array(strtolower($verification['status']), ['failed', 'cancelled', 'rejected'])) {
                $status = 'failed';
                $message = 'Payment failed. Please try again.';
                $transaction->markAsFailed($verification['raw_response'] ?? []);
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
                'transaction' => [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'gateway' => $transaction->gateway,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment status check failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to check payment status',
            ], 500);
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
            'pesapal' => new \App\Services\PaymentGateways\PesaPalService('landlord_billing'),
            'dpo' => new \App\Services\PaymentGateways\DpoService('landlord_billing'),
            'mtn_momo' => new \App\Services\PaymentGateways\MtnMomoService('landlord_billing'),
            'airtel_money' => new \App\Services\PaymentGateways\AirtelMoneyService('landlord_billing'),
            default => throw new \Exception("Unsupported gateway: {$gateway}"),
        };
    }
}
