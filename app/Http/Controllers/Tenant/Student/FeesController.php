<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Finance\FeePayment;
use App\Models\Finance\FeeInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class FeesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Get fees assigned to this student
        $fees = Fee::whereHas('assignments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->with(['assignments' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])->get();

        // Calculate payment status for each fee
        $feesWithStatus = $fees->map(function ($fee) use ($student) {
            $assignment = $fee->assignments->first();
            // Sum confirmed payments linked via meta->fee_id
            $totalPaid = FeePayment::where('student_id', $student->id)
                ->where('status', 'confirmed')
                ->where(function($q) use ($fee) {
                    $q->where('meta->fee_id', $fee->id)
                      ->orWhereJsonContains('meta->fee_id', $fee->id);
                })
                ->sum('amount');

            $assignedAmount = $assignment ? (float) ($assignment->amount ?? $fee->amount ?? 0) : (float)($fee->amount ?? 0);
            $outstanding = max(0, $assignedAmount - (float)$totalPaid);

            return [
                'fee' => $fee,
                'assignment' => $assignment,
                'total_paid' => (float)$totalPaid,
                'outstanding' => $outstanding,
                'is_paid' => $outstanding <= 0.00001,
                'payment_percentage' => $assignedAmount > 0
                    ? min(100, round(((float)$totalPaid / $assignedAmount) * 100))
                    : 0,
            ];
        });

        // Get recent payments
        $recentPayments = FeePayment::where('student_id', $student->id)
            ->with('fee')
            ->latest()
            ->take(5)
            ->get();

        // Calculate totals
        $totalOutstanding = $feesWithStatus->sum('outstanding');
        $totalPaid = $feesWithStatus->sum('total_paid');

        return view('tenant.student.fees.index', [
            'fees' => $feesWithStatus,
            'recentPayments' => $recentPayments,
            'totalOutstanding' => $totalOutstanding,
            'totalPaid' => $totalPaid,
        ]);
    }

    public function show(Fee $fee)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Check if this fee is assigned to the student
        $assignment = $fee->assignments()->where('student_id', $student->id)->first();

        if (!$assignment) {
            abort(403, 'You are not authorized to view this fee.');
        }

        // Get payment history for this fee using JSON meta->fee_id
        $payments = FeePayment::where('student_id', $student->id)
            ->where(function($q) use ($fee) {
                $q->where('meta->fee_id', $fee->id)
                  ->orWhereJsonContains('meta->fee_id', $fee->id);
            })
            ->latest()
            ->get();

    $assignedAmount = $assignment ? (float) ($assignment->amount ?? $fee->amount ?? 0) : (float)($fee->amount ?? 0);
    $totalPaid = (float) $payments->where('status', 'confirmed')->sum('amount');
    $outstanding = max(0, $assignedAmount - $totalPaid);

        return view('tenant.student.fees.show', [
            'fee' => $fee,
            'assignment' => $assignment,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'isPaid' => $outstanding <= 0,
        ]);
    }

    /**
     * Handle student-initiated payment (supports partial payments).
     */
    public function pay(Request $request, Fee $fee)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Ensure fee is assigned to this student
        $assignment = $fee->assignments()->where('student_id', $student->id)->first();
        if (!$assignment) {
            return redirect()->back()->with('error', 'You are not authorized to pay this fee.');
        }

        // Find or create a simple invoice record for this fee+student
        $invoice = FeeInvoice::firstOrCreate([
            'student_id' => $student->id,
            'currency' => currency_code(),
            'status' => 'pending',
        ], [
            'total_amount' => $assignment->amount,
            'due_date' => $assignment->due_date ?? now()->addDays(14),
            'notes' => 'Auto-created from Fee ' . $fee->name,
            'created_by' => auth()->id(),
        ]);

        // Compute outstanding from confirmed payments tied to this invoice
        $confirmedPaid = (float) $invoice->payments()->where('status', 'confirmed')->sum('amount');
        $outstanding = max(0.0, (float)$invoice->total_amount - $confirmedPaid);
        if ($outstanding <= 0) {
            // Update invoice status if needed
            if ($invoice->status !== 'paid') {
                $invoice->update(['status' => 'paid']);
            }
            return redirect()->back()->with('info', 'This fee is already fully paid.');
        }

        // Validate requested amount for partial payment
        $validated = $request->validate([
            'amount' => ['required','numeric','min:0.01','max:'.number_format($outstanding, 2, '.', '')],
            'payment_method' => ['required','in:card,bank,mobile'],
        ]);

        $amount = (float) $validated['amount'];

        // Create payment as confirmed for now (no gateway integration here)
        $payment = FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'student_id' => $student->id,
            'amount' => $amount,
            'currency' => currency_code(),
            'method' => match($validated['payment_method']) {
                'card' => 'card',
                'bank' => 'bank',
                'mobile' => 'mtn',
                default => 'cash',
            },
            'reference' => 'FP-'.now()->format('YmdHis').'-'.$student->id,
            'paid_at' => now(),
            'status' => 'confirmed',
            'meta' => [
                'source' => 'student-portal',
                'fee_id' => $fee->id,
            ],
            'received_by' => auth()->id(),
        ]);

        // Update invoice status based on new balance
        $newConfirmedPaid = (float) $invoice->payments()->where('status', 'confirmed')->sum('amount');
        $balance = max(0.0, (float)$invoice->total_amount - $newConfirmedPaid);
        $newStatus = $balance <= 0 ? 'paid' : ($newConfirmedPaid > 0 ? 'partial' : 'pending');
        $invoice->update(['status' => $newStatus]);

        return redirect()
            ->route('tenant.student.fees.show', $fee)
            ->with('success', 'Payment of '.format_money($amount).' recorded. Remaining balance: '.format_money($balance).'.');
    }

    public function paymentStatus($transactionId)
    {
        $user = Auth::user();
        $student = $user->student;

        $payment = FeePayment::where('id', $transactionId)
            ->where('student_id', $student->id)
            ->with('invoice')
            ->first();

        if (!$payment) {
            abort(404, 'Payment not found.');
        }

        return view('tenant.student.fees.payment-status', [
            'payment' => $payment,
        ]);
    }

    /**
     * Generate a Bank Payment Slip PDF for this fee (with outstanding amount).
     */
    public function bankSlip(Fee $fee)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Ensure fee is assigned to this student
        $assignment = $fee->assignments()->where('student_id', $student->id)->first();
        if (!$assignment) {
            return redirect()->back()->with('error', 'You are not authorized to access this fee.');
        }

        // Compute outstanding based on confirmed payments tagged with this fee
        $assignedAmount = (float) ($assignment->amount ?? $fee->amount ?? 0);
        $paid = (float) FeePayment::where('student_id', $student->id)
            ->where('status', 'confirmed')
            ->where(function($q) use ($fee) {
                $q->where('meta->fee_id', $fee->id)
                  ->orWhereJsonContains('meta->fee_id', $fee->id);
            })
            ->sum('amount');
        $outstanding = max(0.0, $assignedAmount - $paid);

        if ($outstanding <= 0) {
            return redirect()->route('tenant.student.fees.show', $fee)
                ->with('info', __('This fee is fully paid. No bank slip needed.'));
        }

        $reference = 'FEE-' . $fee->id . '-STU-' . $student->id;

        $data = [
            'school_name' => setting('school_name', config('app.name')),
            'school_address' => setting('school_address', ''),
            'school_phone' => setting('school_phone', ''),
            'bank_name' => setting('bank_name', ''),
            'bank_account_name' => setting('bank_account_name', ''),
            'bank_account_number' => setting('bank_account_number', ''),
            'bank_branch' => setting('bank_branch', ''),
            'student' => $student,
            'fee' => $fee,
            'assignedAmount' => $assignedAmount,
            'paid' => $paid,
            'outstanding' => $outstanding,
            'currency' => currency_code(),
            'reference' => $reference,
            'generated_at' => now(),
        ];

        // Generate PDF
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = Pdf::loadView('pdf.fee_bank_slip', $data)->setPaper('A4', 'portrait');
            $filename = 'bank-slip-' . $reference . '.pdf';
            return $pdf->download($filename);
        }

        // Fallback: render HTML view (if DOMPDF not installed)
        return view('pdf.fee_bank_slip', $data);
    }

    /**
     * Upload Bank Deposit Slip proof and create a pending bank payment for review.
     */
    public function uploadProof(Request $request, Fee $fee)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Ensure fee is assigned to this student
        $assignment = $fee->assignments()->where('student_id', $student->id)->first();
        if (!$assignment) {
            return redirect()->back()->with('error', 'You are not authorized to access this fee.');
        }

        // Compute outstanding from confirmed payments linked to this fee
        $assignedAmount = (float) ($assignment->amount ?? $fee->amount ?? 0);
        $confirmedPaid = (float) FeePayment::where('student_id', $student->id)
            ->where('status', 'confirmed')
            ->where(function($q) use ($fee) {
                $q->where('meta->fee_id', $fee->id)
                  ->orWhereJsonContains('meta->fee_id', $fee->id);
            })
            ->sum('amount');
        $outstanding = max(0.0, $assignedAmount - $confirmedPaid);
        if ($outstanding <= 0) {
            return redirect()->route('tenant.student.fees.show', $fee)
                ->with('info', __('This fee is fully paid.'));
        }

        $validated = $request->validate([
            'amount' => ['required','numeric','min:0.01','max:'.number_format($outstanding, 2, '.', '')],
            'reference' => ['nullable','string','max:120'],
            'proof' => ['required','file','mimes:pdf,jpg,jpeg,png,webp','max:5120'], // 5MB
        ]);

        // Find or create an invoice for this fee+student
        $invoice = FeeInvoice::firstOrCreate([
            'student_id' => $student->id,
            'currency' => currency_code(),
            'status' => 'pending',
        ], [
            'total_amount' => $assignment->amount,
            'due_date' => $assignment->due_date ?? now()->addDays(14),
            'notes' => 'Auto-created from Fee ' . $fee->name,
            'created_by' => auth()->id(),
        ]);

        // Store the uploaded file on public disk
        $path = $request->file('proof')->store('fee-proofs/'.$student->id, 'public');

        // Create a pending bank payment
        $payment = FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'student_id' => $student->id,
            'amount' => (float)$validated['amount'],
            'currency' => currency_code(),
            'method' => 'bank',
            'reference' => $validated['reference'] ?: 'BANK-'.now()->format('YmdHis').'-'.$student->id,
            'paid_at' => now(),
            'status' => 'pending',
            'meta' => [
                'source' => 'student-portal',
                'fee_id' => $fee->id,
                'proof_path' => $path,
                'proof_original_name' => $request->file('proof')->getClientOriginalName(),
            ],
            'received_by' => null,
        ]);

        // Update invoice status: if any payment exists but not fully paid, mark partial
        $newConfirmedPaid = (float) $invoice->payments()->where('status', 'confirmed')->sum('amount');
        $balance = max(0.0, (float)$invoice->total_amount - $newConfirmedPaid);
        $newStatus = $balance <= 0 ? 'paid' : ($newConfirmedPaid > 0 ? 'partial' : 'pending');
        $invoice->update(['status' => $newStatus]);

        return redirect()
            ->route('tenant.student.fees.show', $fee)
            ->with('success', __('Bank slip uploaded. Your payment is pending verification.'));
    }
}