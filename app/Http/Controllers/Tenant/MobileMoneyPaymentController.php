<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\MobileMoneyGateway;
use App\Models\PaymentTransaction;
use App\Services\Payments\MobileMoneyPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobileMoneyPaymentController extends Controller
{
    protected MobileMoneyPaymentService $paymentService;

    public function __construct(MobileMoneyPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show the payment form
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $gateways = MobileMoneyGateway::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->get();

        // Get invoice if provided
        $invoice = null;
        if ($request->invoice_id) {
            $invoice = Invoice::with('student', 'feeStructure')
                ->findOrFail($request->invoice_id);
        }

        return view('tenant.payments.mobile-money.create', [
            'gateways' => $gateways,
            'invoice' => $invoice,
            'defaultAmount' => $invoice ? $invoice->balance : null,
            'defaultPhone' => auth()->user()->phone ?? '',
            'defaultEmail' => auth()->user()->email ?? '',
            'defaultName' => auth()->user()->name ?? '',
        ]);
    }

    /**
     * Process the payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_id' => 'required|exists:mobile_money_gateways,id',
            'amount' => 'required|numeric|min:1',
            'phone_number' => 'required|string|min:9|max:15',
            'description' => 'nullable|string|max:255',
            'invoice_id' => 'nullable|exists:invoices,id',
        ], [
            'gateway_id.required' => 'Please select a payment method.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be at least 1.',
            'phone_number.required' => 'Please enter your mobile money phone number.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $school = $request->attributes->get('currentSchool');
        
        try {
            $gateway = MobileMoneyGateway::findOrFail($request->gateway_id);
            $this->paymentService->setGateway($gateway);

            $paymentData = [
                'amount' => $request->amount,
                'phone' => $request->phone_number,
                'description' => $request->description ?? 'Payment',
                'email' => auth()->user()->email,
                'customer_name' => auth()->user()->name,
                'metadata' => [
                    'school_id' => $school->id,
                    'user_id' => auth()->id(),
                ],
            ];

            // If paying for an invoice
            if ($request->invoice_id) {
                $invoice = Invoice::findOrFail($request->invoice_id);
                $paymentData['payable_type'] = Invoice::class;
                $paymentData['payable_id'] = $invoice->id;
                $paymentData['reference'] = $invoice->invoice_number;
            }

            $result = $this->paymentService->initiatePayment($paymentData);

            // Create transaction record
            $transaction = PaymentTransaction::create([
                'school_id' => $school->id,
                'gateway_id' => $gateway->id,
                'transaction_id' => $result->transactionId,
                'external_id' => $result->providerTransactionId ?? $result->externalId,
                'amount' => $request->amount,
                'currency' => $gateway->currency_code ?? 'UGX',
                'phone_number' => $request->phone_number,
                'email' => auth()->user()->email,
                'customer_name' => auth()->user()->name,
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

            if ($result->success) {
                // If redirect-based payment
                if ($result->paymentUrl) {
                    return redirect($result->paymentUrl);
                }

                return redirect()
                    ->route('tenant.payments.mobile-money.status', $transaction->transaction_id)
                    ->with('success', $result->message ?? 'Payment request sent! Please check your phone to approve.');
            }

            return redirect()->back()
                ->with('error', $result->message ?? 'Payment initiation failed. Please try again.')
                ->withInput();

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show payment status
     */
    public function status(Request $request, string $transactionId)
    {
        $transaction = PaymentTransaction::findByIdentifier($transactionId);

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        // Check latest status from provider if not finalized
        if (!$transaction->isFinalized() && $transaction->gateway_id) {
            try {
                $gateway = MobileMoneyGateway::find($transaction->gateway_id);
                if ($gateway) {
                    $this->paymentService->setGateway($gateway);
                    $result = $this->paymentService->checkStatus($transaction->transaction_id);
                    
                    // Update status based on result
                    if ($result->status !== $transaction->status) {
                        switch ($result->status) {
                            case 'completed':
                            case 'successful':
                                $transaction->markAsCompleted($result->providerResponse);
                                break;
                            case 'failed':
                            case 'rejected':
                                $transaction->markAsFailed($result->message ?? 'Payment failed', $result->errorCode);
                                break;
                        }
                        $transaction->refresh();
                    }
                }
            } catch (\Exception $e) {
                // Silent fail - use cached status
            }
        }

        return view('tenant.payments.mobile-money.status', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show transaction history
     */
    public function history(Request $request)
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
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // For non-admin users, only show their own transactions
        if (!auth()->user()->hasRole(['admin', 'super-admin', 'accountant'])) {
            $query->where('initiated_by', auth()->id());
        }

        $transactions = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => PaymentTransaction::where('school_id', $school->id)->count(),
            'completed' => PaymentTransaction::where('school_id', $school->id)->completed()->count(),
            'pending' => PaymentTransaction::where('school_id', $school->id)->pending()->count(),
            'failed' => PaymentTransaction::where('school_id', $school->id)->failed()->count(),
            'total_amount' => PaymentTransaction::where('school_id', $school->id)->completed()->sum('amount'),
        ];

        return view('tenant.payments.mobile-money.history', [
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }

    /**
     * Cancel a pending payment
     */
    public function cancel(Request $request, string $transactionId)
    {
        $transaction = PaymentTransaction::findByIdentifier($transactionId);

        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaction not found');
        }

        // Authorization check
        if ($transaction->initiated_by !== auth()->id() && !auth()->user()->hasRole(['admin', 'super-admin'])) {
            abort(403);
        }

        if (!$transaction->isPending() && !$transaction->isProcessing()) {
            return redirect()->back()->with('error', 'Only pending transactions can be cancelled');
        }

        $transaction->markAsCancelled($request->reason ?? 'Cancelled by user');

        return redirect()->back()->with('success', 'Payment has been cancelled');
    }

    /**
     * AJAX: Check status
     */
    public function checkStatus(Request $request, string $transactionId)
    {
        $transaction = PaymentTransaction::findByIdentifier($transactionId);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Refresh status if pending
        if (!$transaction->isFinalized() && $transaction->gateway_id) {
            try {
                $gateway = MobileMoneyGateway::find($transaction->gateway_id);
                if ($gateway) {
                    $this->paymentService->setGateway($gateway);
                    $result = $this->paymentService->checkStatus($transaction->transaction_id);
                    
                    if ($result->status !== $transaction->status) {
                        switch ($result->status) {
                            case 'completed':
                            case 'successful':
                                $transaction->markAsCompleted($result->providerResponse);
                                break;
                            case 'failed':
                            case 'rejected':
                                $transaction->markAsFailed($result->message ?? 'Payment failed');
                                break;
                        }
                        $transaction->refresh();
                    }
                }
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return response()->json([
            'status' => $transaction->status,
            'is_final' => $transaction->isFinalized(),
            'message' => $this->getStatusMessage($transaction),
            'transaction' => $transaction->getSummary(),
        ]);
    }

    /**
     * Get human-readable status message
     */
    protected function getStatusMessage(PaymentTransaction $transaction): string
    {
        return match($transaction->status) {
            'pending' => 'Waiting for payment approval on your phone...',
            'processing' => 'Processing your payment...',
            'completed' => 'Payment successful!',
            'failed' => 'Payment failed: ' . ($transaction->failure_reason ?? 'Unknown error'),
            'cancelled' => 'Payment was cancelled.',
            'expired' => 'Payment request expired.',
            'refunded' => 'Payment has been refunded.',
            default => 'Unknown status',
        };
    }
}
