<?php

namespace App\Http\Controllers\Tenant\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FeeStructure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $query = Invoice::where('school_id', $school->id)
            ->with(['student', 'feeStructure', 'payments']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(perPage());

        // Statistics
        $stats = [
            'total_invoices' => Invoice::where('school_id', $school->id)->count(),
            'total_amount' => Invoice::where('school_id', $school->id)->sum('total_amount'),
            'paid_amount' => Invoice::where('school_id', $school->id)->sum('paid_amount'),
            'outstanding' => Invoice::where('school_id', $school->id)->sum('balance'),
        ];

        return view('tenant.finance.invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $school = request()->attributes->get('currentSchool');

        $feeStructures = FeeStructure::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('fee_name')
            ->get();

        $students = User::where('school_id', $school->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.invoices.create', compact('feeStructures', 'students'));
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'total_amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'academic_year' => 'required|string|max:191',
            'term' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $validated['school_id'] = $school->id;
        $validated['invoice_number'] = $this->generateInvoiceNumber($school->id);
        $validated['paid_amount'] = 0;
        $validated['balance'] = $validated['total_amount'];
        $validated['status'] = 'unpaid';

        DB::transaction(function () use ($validated) {
            Invoice::create($validated);
        });

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'feeStructure', 'payments.receiver', 'school']);

        return view('tenant.finance.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot edit paid invoices.');
        }

        $feeStructures = FeeStructure::where('school_id', $invoice->school_id)
            ->where('is_active', true)
            ->orderBy('fee_name')
            ->get();

        $students = User::where('school_id', $invoice->school_id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.invoices.edit', compact('invoice', 'feeStructures', 'students'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot update paid invoices.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'total_amount' => 'required|numeric|min:' . $invoice->paid_amount,
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'academic_year' => 'required|string|max:191',
            'term' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $validated['balance'] = $validated['total_amount'] - $invoice->paid_amount;

        // Update status based on payment
        if ($validated['balance'] <= 0) {
            $validated['status'] = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $validated['status'] = 'partial';
        }

        $invoice->update($validated);

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->count() > 0) {
            return redirect()
                ->route('tenant.finance.invoices.index')
                ->with('error', 'Cannot delete invoice with payments.');
        }

        $invoice->delete();

        return redirect()
            ->route('tenant.finance.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    private function generateInvoiceNumber($schoolId): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastInvoice = Invoice::where('school_id', $schoolId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -5) + 1 : 1;

        return $prefix . $year . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
