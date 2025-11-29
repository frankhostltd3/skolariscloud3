<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\MobileMoneyGateway;
use App\Models\PaymentTransaction;
use App\Services\Payments\MobileMoneyPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PaymentApiController extends Controller
{
    protected MobileMoneyPaymentService $paymentService;

    public function __construct(MobileMoneyPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get available payment gateways
     * GET /api/payments/gateways
     */
    public function gateways(Request $request): JsonResponse
    {
        $school = $request->attributes->get('currentSchool');

        $gateways = MobileMoneyGateway::where('school_id', $school->id)
            ->where('is_active', true)
            ->select(['id', 'name', 'slug', 'provider', 'currency_code', 'is_default', 'supports_ussd', 'supports_qr'])
            ->orderByDesc('is_default')
            ->get()
            ->map(function ($gateway) {
                return [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'slug' => $gateway->slug,
                    'provider' => $gateway->provider,
                    'provider_label' => ucwords(str_replace('_', ' ', $gateway->provider)),
                    'currency' => $gateway->currency_code,
                    'is_default' => $gateway->is_default,
                    'supports_ussd' => $gateway->supports_ussd,
                    'supports_qr' => $gateway->supports_qr,
                ];
            });

        return response()->json([
            'success' => true,
            'gateways' => $gateways,
        ]);
    }

    /**
     * Initiate a payment
     * POST /api/payments/initiate
     */
    public function initiate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'phone_number' => 'required|string',
            'gateway_id' => 'nullable|exists:mobile_money_gateways,id',
            'gateway_slug' => 'nullable|string',
            'description' => 'nullable|string|max:255',
            'invoice_id' => 'nullable|exists:invoices,id',
            'payable_type' => 'nullable|string',
            'payable_id' => 'nullable|integer',
            'customer_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $school = $request->attributes->get('currentSchool');

        try {
            // Set up the gateway
            if ($request->gateway_id) {
                $gateway = MobileMoneyGateway::findOrFail($request->gateway_id);
                $this->paymentService->setGateway($gateway);
            } elseif ($request->gateway_slug) {
                $this->paymentService->useGateway($request->gateway_slug);
            } else {
                $this->paymentService->useDefault();
            }

            // Prepare payment data
            $paymentData = [
                'amount' => $request->amount,
                'phone' => $request->phone_number,
                'description' => $request->description ?? 'Payment',
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'metadata' => array_merge($request->metadata ?? [], [
                    'school_id' => $school->id,
                    'user_id' => auth()->id(),
                ]),
            ];

            // If paying for an invoice
            if ($request->invoice_id) {
                $invoice = Invoice::findOrFail($request->invoice_id);
                $paymentData['payable_type'] = Invoice::class;
                $paymentData['payable_id'] = $invoice->id;
                $paymentData['description'] = "Invoice #{$invoice->invoice_number}";
                $paymentData['amount'] = $request->amount ?? $invoice->balance;
                $paymentData['reference'] = $invoice->invoice_number;
            } elseif ($request->payable_type && $request->payable_id) {
                $paymentData['payable_type'] = $request->payable_type;
                $paymentData['payable_id'] = $request->payable_id;
            }

            // Initiate payment
            $result = $this->paymentService->initiatePayment($paymentData);

            // Create transaction record
            $transaction = PaymentTransaction::create([
                'school_id' => $school->id,
                'gateway_id' => $this->paymentService->getGateway()?->id,
                'transaction_id' => $result->transactionId,
                'external_id' => $result->providerTransactionId ?? $result->externalId,
                'amount' => $paymentData['amount'],
                'currency' => $this->paymentService->getGateway()?->currency_code ?? 'USD',
                'phone_number' => $paymentData['phone'],
                'email' => $paymentData['email'],
                'customer_name' => $paymentData['customer_name'],
                'payable_type' => $paymentData['payable_type'] ?? null,
                'payable_id' => $paymentData['payable_id'] ?? null,
                'description' => $paymentData['description'],
                'metadata' => $paymentData['metadata'],
                'status' => $result->status,
                'provider_request' => $paymentData,
                'provider_response' => $result->providerResponse,
                'initiated_at' => now(),
                'initiated_by' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => $result->success,
                'status' => $result->status,
                'message' => $result->message,
                'transaction_id' => $result->transactionId,
                'payment_url' => $result->paymentUrl,
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->formatted_amount,
                    'status' => $transaction->status,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status
     * GET /api/payments/status/{transactionId}
     */
    public function status(Request $request, string $transactionId): JsonResponse
    {
        // Find the transaction
        $transaction = PaymentTransaction::findByIdentifier($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        // If already finalized, return cached status
        if ($transaction->isFinalized()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction->getSummary(),
                'is_final' => true,
            ]);
        }

        // Check status with provider
        try {
            if ($transaction->gateway_id) {
                $gateway = MobileMoneyGateway::find($transaction->gateway_id);
                if ($gateway) {
                    $this->paymentService->setGateway($gateway);
                    $result = $this->paymentService->checkStatus($transactionId);

                    // Update transaction
                    if ($result->status !== $transaction->status) {
                        $this->updateTransactionFromResult($transaction, $result);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log but don't fail - return cached status
        }

        $transaction->refresh();

        return response()->json([
            'success' => true,
            'transaction' => $transaction->getSummary(),
            'is_final' => $transaction->isFinalized(),
        ]);
    }

    /**
     * Get payment history
     * GET /api/payments/history
     */
    public function history(Request $request): JsonResponse
    {
        $school = $request->attributes->get('currentSchool');

        $query = PaymentTransaction::where('school_id', $school->id)
            ->with('mobileMoneyGateway:id,name,provider')
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Filter by phone
        if ($request->phone) {
            $query->where('phone_number', 'like', '%' . $request->phone . '%');
        }

        // Paginate
        $transactions = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'transactions' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Get payment statistics
     * GET /api/payments/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $school = $request->attributes->get('currentSchool');
        $period = $request->period ?? 'today';

        $stats = MobileMoneyPaymentService::getStatistics($school->id, $period);

        return response()->json([
            'success' => true,
            'period' => $period,
            'stats' => $stats,
        ]);
    }

    /**
     * Cancel a pending payment
     * POST /api/payments/{transactionId}/cancel
     */
    public function cancel(Request $request, string $transactionId): JsonResponse
    {
        $transaction = PaymentTransaction::findByIdentifier($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if (!$transaction->isPending() && !$transaction->isProcessing()) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending transactions can be cancelled',
            ], 400);
        }

        $transaction->markAsCancelled($request->reason ?? 'Cancelled by user');

        return response()->json([
            'success' => true,
            'message' => 'Payment cancelled successfully',
            'transaction' => $transaction->getSummary(),
        ]);
    }

    /**
     * Retry a failed payment
     * POST /api/payments/{transactionId}/retry
     */
    public function retry(Request $request, string $transactionId): JsonResponse
    {
        $transaction = PaymentTransaction::findByIdentifier($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if (!$transaction->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'This transaction cannot be retried',
            ], 400);
        }

        // Create new payment with same details
        $request->merge([
            'amount' => $transaction->amount,
            'phone_number' => $transaction->phone_number,
            'gateway_id' => $transaction->gateway_id,
            'description' => $transaction->description,
            'payable_type' => $transaction->payable_type,
            'payable_id' => $transaction->payable_id,
            'customer_name' => $transaction->customer_name,
            'email' => $transaction->email,
            'metadata' => array_merge($transaction->metadata ?? [], [
                'retry_of' => $transaction->transaction_id,
            ]),
        ]);

        return $this->initiate($request);
    }

    /**
     * Update transaction from result
     */
    protected function updateTransactionFromResult(PaymentTransaction $transaction, $result): void
    {
        switch ($result->status) {
            case 'completed':
            case 'successful':
                $transaction->markAsCompleted($result->providerResponse);
                break;
            case 'failed':
            case 'rejected':
                $transaction->markAsFailed($result->message ?? 'Payment failed', $result->errorCode);
                break;
            case 'expired':
                $transaction->markAsExpired();
                break;
            case 'cancelled':
                $transaction->markAsCancelled();
                break;
        }
    }
}
