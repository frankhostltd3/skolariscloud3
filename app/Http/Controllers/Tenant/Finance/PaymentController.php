<?php

namespace App\Http\Controllers\Tenant\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $query = Payment::where('school_id', $school->id)
            ->with(['invoice.student', 'receiver']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('invoice.student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(perPage());

        // Statistics
        $stats = [
            'total_payments' => Payment::where('school_id', $school->id)->count(),
            'total_amount' => Payment::where('school_id', $school->id)->sum('amount'),
            'today_amount' => Payment::where('school_id', $school->id)->whereDate('payment_date', today())->sum('amount'),
            'this_month' => Payment::where('school_id', $school->id)->whereMonth('payment_date', now()->month)->sum('amount'),
        ];

        return view('tenant.finance.payments.index', compact('payments', 'stats'));
    }

    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        // Get invoice if specified
        $invoice = null;
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::where('school_id', $school->id)
                ->where('id', $request->invoice_id)
                ->first();
        }

        // Get unpaid/partial invoices
        $invoices = Invoice::where('school_id', $school->id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->with('student')
            ->orderBy('due_date')
            ->get();

        return view('tenant.finance.payments.create', compact('invoices', 'invoice'));
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'invoice_id' => 'required|exists:tenant.invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,mobile_money',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Validate amount doesn't exceed balance
        if ($validated['amount'] > $invoice->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment amount cannot exceed invoice balance of ' . formatMoney($invoice->balance));
        }

        $validated['school_id'] = $school->id;
        $validated['student_id'] = $invoice->student_id;
        $validated['receipt_number'] = $this->generateReceiptNumber($school->id);
        $validated['received_by'] = auth()->id();

        DB::transaction(function () use ($validated, $invoice) {
            // Create payment
            Payment::create($validated);

            // Update invoice
            $newPaidAmount = $invoice->paid_amount + $validated['amount'];
            $newBalance = $invoice->total_amount - $newPaidAmount;

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance' => $newBalance,
                'status' => $newBalance <= 0 ? 'paid' : 'partial',
            ]);
        });

        return redirect()
            ->route('tenant.finance.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.student', 'invoice.feeStructure', 'receiver', 'school']);

        return view('tenant.finance.payments.show', compact('payment'));
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['invoice.student', 'invoice.feeStructure', 'receiver', 'school']);

        return view('tenant.finance.payments.receipt', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        return redirect()
            ->route('tenant.finance.payments.index')
            ->with('error', 'Payments cannot be deleted. Contact administrator if reversal is needed.');
    }

    private function generateReceiptNumber($schoolId): string
    {
        $prefix = 'RCP';
        $year = date('Y');
        $lastPayment = Payment::where('school_id', $schoolId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastPayment ? (int) substr($lastPayment->receipt_number, -6) + 1 : 1;

        return $prefix . $year . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
