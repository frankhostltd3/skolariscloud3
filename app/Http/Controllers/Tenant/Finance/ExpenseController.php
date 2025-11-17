<?php

namespace App\Http\Controllers\Tenant\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $query = Expense::where('school_id', $school->id)
            ->with(['category', 'currency', 'creator', 'approver']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(perPage());

        // Get categories for filter
        $categories = ExpenseCategory::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total_pending' => Expense::where('school_id', $school->id)->where('status', 'pending')->count(),
            'total_approved' => Expense::where('school_id', $school->id)->where('status', 'approved')->sum('amount'),
            'this_month' => Expense::where('school_id', $school->id)->where('status', 'approved')->whereMonth('expense_date', now()->month)->sum('amount'),
            'rejected_count' => Expense::where('school_id', $school->id)->where('status', 'rejected')->count(),
        ];

        return view('tenant.finance.expenses.index', compact('expenses', 'categories', 'stats'));
    }

    public function create()
    {
        $school = request()->attributes->get('currentSchool');

        $categories = ExpenseCategory::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('tenant.finance.expenses.create', compact('categories', 'currencies'));
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,online_payment,other',
            'reference_number' => 'nullable|string|max:191',
            'vendor_name' => 'nullable|string|max:191',
            'vendor_contact' => 'nullable|string|max:191',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        $validated['school_id'] = $school->id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt_path'] = $path;
        }

        DB::transaction(function () use ($validated) {
            Expense::create($validated);
        });

        return redirect()
            ->route('tenant.finance.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'currency', 'creator', 'approver', 'school']);

        return view('tenant.finance.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()
                ->route('tenant.finance.expenses.index')
                ->with('error', 'Only pending expenses can be edited.');
        }

        $categories = ExpenseCategory::where('school_id', $expense->school_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('tenant.finance.expenses.edit', compact('expense', 'categories', 'currencies'));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()
                ->route('tenant.finance.expenses.index')
                ->with('error', 'Only pending expenses can be updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,online_payment,other',
            'reference_number' => 'nullable|string|max:191',
            'vendor_name' => 'nullable|string|max:191',
            'vendor_contact' => 'nullable|string|max:191',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt_path'] = $path;
        }

        $expense->update($validated);

        return redirect()
            ->route('tenant.finance.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->status === 'approved') {
            return redirect()
                ->route('tenant.finance.expenses.index')
                ->with('error', 'Cannot delete approved expenses.');
        }

        // Delete receipt file
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()
            ->route('tenant.finance.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function approve(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()
                ->route('tenant.finance.expenses.index')
                ->with('error', 'Only pending expenses can be approved.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('tenant.finance.expenses.show', $expense)
            ->with('success', 'Expense approved successfully.');
    }

    public function reject(Request $request, Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()
                ->route('tenant.finance.expenses.index')
                ->with('error', 'Only pending expenses can be rejected.');
        }

        $validated = $request->validate([
            'rejected_reason' => 'required|string',
        ]);

        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_reason' => $validated['rejected_reason'],
        ]);

        return redirect()
            ->route('tenant.finance.expenses.show', $expense)
            ->with('success', 'Expense rejected.');
    }
}
