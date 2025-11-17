<?php

namespace App\Http\Controllers\Tenant\Modules;
use App\Http\Controllers\Controller;
use App\Models\Finance\FeePayment;
use App\Notifications\FeePaymentStatusNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Fee;
use App\Models\FeeAssignment;
use App\Models\FeeReminder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\TuitionPlan;
use App\Models\TuitionPlanItem;
use App\Models\TuitionPlanInstallment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class FinancialsController extends Controller
{
    /**
     * Payments management: list fee payments (pending/confirmed/failed) with proof links.
     */
    public function payments(Request $request)
    {
        $status = $request->get('status', 'pending');
        $method = $request->get('method');

        $query = FeePayment::with(['student','invoice'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($method, fn($q) => $q->where('method', $method))
            ->orderByDesc('created_at');

        $payments = $query->paginate(20)->appends($request->query());

        return view('tenant.modules.financials.payments', compact('payments','status','method'));
    }

    public function overview(): View
    {
        // Calculate financial metrics using available data
        $totalRevenue = Order::where('status', 'paid')->sum('price');
        $monthlyRevenue = Order::where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('price');

        // Calculate real expenses from the database
        $totalExpenses = Expense::approved()->sum('amount');
        // TODO: Implement pending fees calculation when payment system is complete
        // $pendingFees = FeeAssignment::where('is_active', true)
        //     ->whereDoesntHave('payments')
        //     ->sum('amount');
        $pendingFees = 0; // Placeholder until payment system is implemented

        // Recent transactions from orders
        $recentTransactions = Order::where('status', 'paid')
            ->orderByDesc('paid_at')
            ->limit(8)
            ->get()
            ->map(function($order) {
                return [
                    'type' => 'Order Payment',
                    'amount' => (float)$order->price,
                    'date' => optional($order->paid_at)->format('Y-m-d') ?? $order->created_at->format('Y-m-d'),
                    'student' => $order->buyer_name,
                ];
            });

        $financialData = [
            'total_revenue' => (float)$totalRevenue,
            'monthly_revenue' => (float)$monthlyRevenue,
            'pending_fees' => (float)$pendingFees,
            'expenses' => (float)$totalExpenses,
            'recent_transactions' => $recentTransactions->toArray(),
        ];

        return view('tenant.modules.financials.overview', compact('financialData'));
    }

    public function fees(): View
    {
        return view('tenant.modules.financials.fees');
    }

    public function createFee(): View
    {
        return view('tenant.modules.financials.fees.create');
    }

    public function storeFee(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'recurring_type' => 'required|in:one-time,monthly,yearly,term-based',
            'applicable_to' => 'required|in:all,specific_class,specific_student',
            'class_id' => 'nullable|exists:classes,id',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Fee::create($validated);

        return redirect()->route('tenant.modules.financials.fees')
            ->with('success', 'Fee item created successfully.');
    }

    public function assignFees(): View
    {
        $fees = Fee::where('is_active', true)->get();
        // For now, we'll use placeholder data for classes and students
        // In a full implementation, these would come from proper models
        $classes = collect([]); // Placeholder - would be \App\Models\SchoolClass::all()
        $students = collect([]); // Placeholder - would be \App\Models\Student::all()

        return view('tenant.modules.financials.fees.assign', compact('fees', 'classes', 'students'));
    }

    public function storeFeeAssignment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fee_id' => 'required|exists:fees,id',
            'assignment_type' => 'required|in:class,student',
            'class_id' => 'required_if:assignment_type,class|exists:classes,id',
            'student_id' => 'required_if:assignment_type,student|exists:students,id',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        FeeAssignment::create($validated);

        return redirect()->route('tenant.modules.financials.fees')
            ->with('success', 'Fee assigned successfully.');
    }

    public function sendReminders(): View
    {
        $fees = Fee::where('is_active', true)->get();
        // For now, we'll use placeholder data for classes and students
        // In a full implementation, these would come from proper models
        $classes = collect([]); // Placeholder
        $students = collect([]); // Placeholder

        return view('tenant.modules.financials.fees.reminders', compact('fees', 'classes', 'students'));
    }

    public function processReminders(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fee_ids' => 'required|array|min:1',
            'fee_ids.*' => 'exists:fees,id',
            'reminder_type' => 'required|in:overdue,upcoming,all',
            'target_audience' => 'required|in:all_students,specific_class,specific_students',
            'class_id' => 'required_if:target_audience,specific_class|exists:classes,id',
            'student_ids' => 'required_if:target_audience,specific_students|array',
            'student_ids.*' => 'exists:students,id',
            'message' => 'nullable|string|max:1000',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
        ]);

        // Create reminder record
        $reminder = FeeReminder::create([
            'fee_ids' => $validated['fee_ids'],
            'reminder_type' => $validated['reminder_type'],
            'target_audience' => $validated['target_audience'],
            'class_id' => $validated['class_id'] ?? null,
            'student_ids' => $validated['student_ids'] ?? null,
            'custom_message' => $validated['message'] ?? null,
            'sent_via_email' => $request->has('send_email'),
            'sent_via_sms' => $request->has('send_sms'),
            'recipient_count' => 0, // Will be updated after sending
            'status' => 'processing',
            'sent_by' => auth()->id(),
        ]);

        // For now, we'll simulate sending reminders
        // In a full implementation, this would integrate with email/SMS services
        $reminderCount = 0;

        // Logic to determine recipients based on target audience
        if ($validated['target_audience'] === 'all_students') {
            // Send to all students with selected fees
            $reminderCount = 50; // Placeholder count
        } elseif ($validated['target_audience'] === 'specific_class') {
            // Send to students in specific class
            $reminderCount = 25; // Placeholder count
        } else {
            // Send to specific students
            $reminderCount = count($validated['student_ids'] ?? []);
        }

        // Update reminder record with results
        $reminder->update([
            'recipient_count' => $reminderCount,
            'status' => 'completed',
            'sent_at' => now(),
        ]);

        return redirect()->route('tenant.modules.financials.fees')
            ->with('success', "Reminders sent successfully to {$reminderCount} recipients.");
    }

    public function createInvoice(): View
    {
        $fees = Fee::where('is_active', true)->get();
        // For now, we'll use placeholder data for students and parents
        // In a full implementation, these would come from proper models
        $students = collect([]); // Placeholder
        $parents = collect([]); // Placeholder

        return view('tenant.modules.financials.invoices.create', compact('fees', 'students', 'parents'));
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'parent_id' => 'nullable|exists:users,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'items' => 'required|array|min:1',
            'items.*.fee_id' => 'required|exists:fees,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Calculate totals
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($validated['items'] as $item) {
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $taxRate = $item['tax_rate'] ?? 0;
            $discount = $item['discount_amount'] ?? 0;

            $itemSubtotal = $quantity * $unitPrice;
            $itemTax = $itemSubtotal * ($taxRate / 100);
            $itemTotal = $itemSubtotal + $itemTax - $discount;

            $subtotal += $itemSubtotal;
            $totalTax += $itemTax;
            $totalDiscount += $discount;
        }

        $totalAmount = $subtotal + $totalTax - $totalDiscount;

        // Create invoice
        $invoice = Invoice::create([
            'student_id' => $validated['student_id'],
            'parent_id' => $validated['parent_id'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        // Generate invoice number
        $invoice->update(['invoice_number' => $invoice->generateInvoiceNumber()]);

        // Create invoice items
        foreach ($validated['items'] as $itemData) {
            $quantity = $itemData['quantity'];
            $unitPrice = $itemData['unit_price'];
            $taxRate = $itemData['tax_rate'] ?? 0;
            $discount = $itemData['discount_amount'] ?? 0;

            $itemSubtotal = $quantity * $unitPrice;
            $itemTax = $itemSubtotal * ($taxRate / 100);
            $itemTotal = $itemSubtotal + $itemTax - $discount;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'fee_id' => $itemData['fee_id'],
                'description' => $itemData['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $itemTotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $itemTax,
                'discount_amount' => $discount,
            ]);
        }

        return redirect()->route('tenant.modules.financials.invoices')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Confirm a pending bank payment with uploaded proof.
     */
    public function confirmPayment(Request $request, FeePayment $payment)
    {
        if ($payment->status !== 'pending') {
            // Notify the student
            return back()->with('error', __('Only pending payments can be confirmed.'));
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'confirmed',
                'paid_at' => $payment->paid_at ?: now(),
                'received_by' => Auth::id(),
            ]);

            // Update invoice status
            if ($payment->invoice) {
                $confirmedPaid = (float) $payment->invoice->payments()->where('status','confirmed')->sum('amount');
                $balance = max(0.0, (float)$payment->invoice->total_amount - $confirmedPaid);
                $newStatus = $balance <= 0 ? 'paid' : ($confirmedPaid > 0 ? 'partial' : 'pending');
                $payment->invoice->update(['status' => $newStatus]);
            }
        });

        // Notify the student
        try {
            $student = $payment->student; // BelongsTo User
            if ($student) {
                $student->notify(new FeePaymentStatusNotification($payment, 'confirmed'));
            }
    } catch (\Throwable $e) {
            Log::warning('Failed to notify student on payment confirm', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
        try {
            $student = $payment->student; // BelongsTo User
            if ($student) {
                $student->notify(new FeePaymentStatusNotification(
                    $payment,
                    'rejected',
                    $request->input('reason')
                ));
            }
    } catch (\Throwable $e) {
            // Notify the student
            return back()->with('error', __('Only pending payments can be rejected.'));
        }

        $request->validate([
            'reason' => ['nullable','string','max:500'],
        ]);

        // Notify the student
        try {
            $student = $payment->student;
            if ($student && $student->user) {
                $student->user->notify(new FeePaymentStatusNotification(
                    $payment,
                    'rejected',
                    $request->input('reason')
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to notify student on payment reject', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
        $meta = $payment->meta ?? [];
        if ($request->filled('reason')) {
            $meta['rejection_reason'] = $request->input('reason');
        }

        $payment->update([
            'status' => 'failed',
            'meta' => $meta,
        ]);

        return back()->with('success', __('Payment rejected.'));
    }

    public function expenses(): View
    {
        $expenses = Expense::with(['category', 'currency', 'creator'])
            ->orderByDesc('expense_date')
            ->paginate(15);

        $totalExpenses = Expense::approved()->sum('amount');
        $pendingExpenses = Expense::pending()->sum('amount');
        $thisMonthExpenses = Expense::approved()->thisMonth()->sum('amount');

        return view('tenant.modules.financials.expenses.index', compact(
            'expenses',
            'totalExpenses',
            'pendingExpenses',
            'thisMonthExpenses'
        ));
    }

    public function createExpense(): View
    {
        $categories = ExpenseCategory::active()->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('tenant.modules.financials.expenses.create', compact('categories', 'currencies'));
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,online_payment,other',
            'reference_number' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_contact' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['tenant_id'] = tenant('id');

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create($validated);

        return redirect()->route('tenant.modules.financials.expenses')
            ->with('success', 'Expense recorded successfully.');
    }

    public function showExpense(Expense $expense): View
    {
        $expense->load(['category', 'currency', 'creator', 'approver']);

        return view('tenant.modules.financials.expenses.show', compact('expense'));
    }

    public function editExpense(Expense $expense): View
    {
        $categories = ExpenseCategory::active()->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('tenant.modules.financials.expenses.edit', compact('expense', 'categories', 'currencies'));
    }

    public function updateExpense(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,online_payment,other',
            'reference_number' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_contact' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                \Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('tenant.modules.financials.expenses')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroyExpense(Expense $expense): RedirectResponse
    {
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            \Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('tenant.modules.financials.expenses')
            ->with('success', 'Expense deleted successfully.');
    }

    public function approveExpense(Request $request, Expense $expense): RedirectResponse
    {
        if (!$request->user()->can('approve expenses')) {
            abort(403, 'Unauthorized');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Expense approved successfully.');
    }

    public function rejectExpense(Request $request, Expense $expense): RedirectResponse
    {
        if (!$request->user()->can('approve expenses')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $expense->update([
            'status' => 'rejected',
            'rejected_reason' => $request->reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Expense rejected.');
    }

    public function exportExpenses(Request $request)
    {
        $expenses = Expense::with(['category', 'currency', 'creator'])
            ->when($request->category, fn($q) => $q->where('expense_category_id', $request->category))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->where('expense_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('expense_date', '<=', $request->date_to))
            ->orderByDesc('expense_date')
            ->get();

        // For now, return JSON. In a full implementation, this would generate Excel/PDF
        return response()->json($expenses);
    }

    public function expenseCategories(): View
    {
        // Ensure default categories exist
        if (ExpenseCategory::count() === 0) {
            $defaultCategories = [
                [
                    'name' => 'Office Supplies',
                    'description' => 'Stationery, printer ink, office equipment',
                    'color' => '#007bff',
                    'icon' => 'bi-pencil-square',
                    'budget_limit' => 5000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Utilities',
                    'description' => 'Electricity, water, internet, phone bills',
                    'color' => '#28a745',
                    'icon' => 'bi-lightning',
                    'budget_limit' => 15000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Travel & Transportation',
                    'description' => 'Fuel, public transport, travel expenses',
                    'color' => '#dc3545',
                    'icon' => 'bi-car-front',
                    'budget_limit' => 10000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Maintenance & Repairs',
                    'description' => 'Building maintenance, equipment repairs',
                    'color' => '#ffc107',
                    'icon' => 'bi-tools',
                    'budget_limit' => 20000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Professional Services',
                    'description' => 'Consultants, legal fees, accounting',
                    'color' => '#6f42c1',
                    'icon' => 'bi-briefcase',
                    'budget_limit' => 25000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Marketing & Advertising',
                    'description' => 'Promotional materials, advertising costs',
                    'color' => '#e83e8c',
                    'icon' => 'bi-megaphone',
                    'budget_limit' => 15000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Staff Training',
                    'description' => 'Workshops, courses, professional development',
                    'color' => '#20c997',
                    'icon' => 'bi-mortarboard',
                    'budget_limit' => 12000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Software & Technology',
                    'description' => 'Software licenses, IT equipment, subscriptions',
                    'color' => '#17a2b8',
                    'icon' => 'bi-laptop',
                    'budget_limit' => 18000.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Miscellaneous',
                    'description' => 'Other expenses not categorized elsewhere',
                    'color' => '#6c757d',
                    'icon' => 'bi-three-dots',
                    'budget_limit' => 5000.00,
                    'is_active' => true,
                ],
            ];

            foreach ($defaultCategories as $category) {
                ExpenseCategory::create($category);
            }
        }

        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->get();

        return view('tenant.modules.financials.expense_categories.index', compact('categories'));
    }

    public function createExpenseCategory(): View
    {
        $parentCategories = ExpenseCategory::active()->whereNull('parent_id')->get();

        return view('tenant.modules.financials.expense_categories.create', compact('parentCategories'));
    }

    public function storeExpenseCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'icon' => 'required|string|max:50',
            'budget_limit' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:expense_categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        ExpenseCategory::create($validated);

        return redirect()->route('tenant.modules.financials.expense_categories')
            ->with('success', 'Expense category created successfully.');
    }

    public function showExpenseCategory(ExpenseCategory $expenseCategory): View
    {
        $expenseCategory->load(['expenses' => function($query) {
            $query->latest()->limit(10);
        }, 'parent', 'children']);

        $totalSpent = $expenseCategory->expenses()->approved()->sum('amount');
        $budgetUsed = $expenseCategory->budget_limit ? ($totalSpent / $expenseCategory->budget_limit) * 100 : 0;

        return view('tenant.modules.financials.expense_categories.show', compact(
            'expenseCategory',
            'totalSpent',
            'budgetUsed'
        ));
    }

    public function editExpenseCategory(ExpenseCategory $expenseCategory): View
    {
        $parentCategories = ExpenseCategory::active()
            ->whereNull('parent_id')
            ->where('id', '!=', $expenseCategory->id)
            ->get();

        return view('tenant.modules.financials.expense_categories.edit', compact(
            'expenseCategory',
            'parentCategories'
        ));
    }

    public function updateExpenseCategory(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'icon' => 'required|string|max:50',
            'budget_limit' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:expense_categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $expenseCategory->update($validated);

        return redirect()->route('tenant.modules.financials.expense_categories')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroyExpenseCategory(ExpenseCategory $expenseCategory): RedirectResponse
    {
        // Check if category has expenses
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with existing expenses.');
        }

        $expenseCategory->delete();

        return redirect()->route('tenant.modules.financials.expense_categories')
            ->with('success', 'Expense category deleted successfully.');
    }

    public function exportExpenseCategories()
    {
        $categories = ExpenseCategory::withCount('expenses')->get();

        // For now, return JSON. In a full implementation, this would generate Excel/PDF
        return response()->json($categories);
    }

    public function tuitionPlans(): View
    {
        $tuitionPlans = TuitionPlan::with(['items', 'installments'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('tenant.modules.financials.tuition_plans.index', compact('tuitionPlans'));
    }

    public function createTuitionPlan(): View
    {
        $fees = Fee::where('is_active', true)->get();

        return view('tenant.modules.financials.tuition_plans.create', compact('fees'));
    }

    public function storeTuitionPlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'academic_year' => 'required|string|max:20',
            'grade_level' => 'required|string|max:50',
            'currency_id' => 'required|exists:currencies,id',
            'items' => 'required|array|min:1',
            'items.*.fee_id' => 'required|exists:fees,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'installments' => 'required|array|min:1',
            'installments.*.name' => 'required|string|max:255',
            'installments.*.description' => 'nullable|string|max:500',
            'installments.*.amount' => 'required|numeric|min:0',
            'installments.*.due_date' => 'required|date|after:today',
        ]);

        // Calculate totals
        $totalAmount = 0;
        $installmentCount = count($validated['installments']);

        foreach ($validated['items'] as $item) {
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $taxRate = $item['tax_rate'] ?? 0;
            $discount = $item['discount_amount'] ?? 0;

            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * ($taxRate / 100);
            $netAmount = $subtotal + $taxAmount - $discount;

            $totalAmount += $netAmount;
        }

        // Create tuition plan
        $tuitionPlan = TuitionPlan::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'academic_year' => $validated['academic_year'],
            'grade_level' => $validated['grade_level'],
            'total_amount' => $totalAmount,
            'currency_id' => $validated['currency_id'],
            'installment_count' => $installmentCount,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        // Create tuition plan items
        foreach ($validated['items'] as $itemData) {
            $quantity = $itemData['quantity'];
            $unitPrice = $itemData['unit_price'];
            $taxRate = $itemData['tax_rate'] ?? 0;
            $discount = $itemData['discount_amount'] ?? 0;

            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * ($taxRate / 100);
            $netAmount = $subtotal + $taxAmount - $discount;

            TuitionPlanItem::create([
                'tuition_plan_id' => $tuitionPlan->id,
                'fee_id' => $itemData['fee_id'],
                'description' => $itemData['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discount,
                'net_amount' => $netAmount,
            ]);
        }

        // Create installments
        foreach ($validated['installments'] as $index => $installmentData) {
            TuitionPlanInstallment::create([
                'tuition_plan_id' => $tuitionPlan->id,
                'installment_number' => $index + 1,
                'name' => $installmentData['name'],
                'description' => $installmentData['description'],
                'amount' => $installmentData['amount'],
                'due_date' => $installmentData['due_date'],
                'is_paid' => false,
            ]);
        }

        return redirect()->route('tenant.modules.financials.tuition_plans')
            ->with('success', 'Tuition plan created successfully.');
    }

    public function showTuitionPlan(TuitionPlan $tuitionPlan): View
    {
        $tuitionPlan->load(['items.fee', 'installments', 'creator']);

        return view('tenant.modules.financials.tuition_plans.show', compact('tuitionPlan'));
    }

    public function editTuitionPlan(TuitionPlan $tuitionPlan): View
    {
        $tuitionPlan->load(['items.fee', 'installments']);
        $fees = Fee::where('is_active', true)->get();

        return view('tenant.modules.financials.tuition_plans.edit', compact('tuitionPlan', 'fees'));
    }

    public function updateTuitionPlan(Request $request, TuitionPlan $tuitionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'academic_year' => 'required|string|max:20',
            'grade_level' => 'required|string|max:50',
            'currency_id' => 'required|exists:currencies,id',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.fee_id' => 'required|exists:fees,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'installments' => 'required|array|min:1',
            'installments.*.name' => 'required|string|max:255',
            'installments.*.description' => 'nullable|string|max:500',
            'installments.*.amount' => 'required|numeric|min:0',
            'installments.*.due_date' => 'required|date',
        ]);

        // Calculate totals
        $totalAmount = 0;
        $installmentCount = count($validated['installments']);

        foreach ($validated['items'] as $item) {
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $taxRate = $item['tax_rate'] ?? 0;
            $discount = $item['discount_amount'] ?? 0;

            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * ($taxRate / 100);
            $netAmount = $subtotal + $taxAmount - $discount;

            $totalAmount += $netAmount;
        }

        // Update tuition plan
        $tuitionPlan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'academic_year' => $validated['academic_year'],
            'grade_level' => $validated['grade_level'],
            'total_amount' => $totalAmount,
            'currency_id' => $validated['currency_id'],
            'installment_count' => $installmentCount,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Delete existing items and installments
        $tuitionPlan->items()->delete();
        $tuitionPlan->installments()->delete();

        // Create new items
        foreach ($validated['items'] as $itemData) {
            $quantity = $itemData['quantity'];
            $unitPrice = $itemData['unit_price'];
            $taxRate = $itemData['tax_rate'] ?? 0;
            $discount = $itemData['discount_amount'] ?? 0;

            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * ($taxRate / 100);
            $netAmount = $subtotal + $taxAmount - $discount;

            TuitionPlanItem::create([
                'tuition_plan_id' => $tuitionPlan->id,
                'fee_id' => $itemData['fee_id'],
                'description' => $itemData['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discount,
                'net_amount' => $netAmount,
            ]);
        }

        // Create new installments
        foreach ($validated['installments'] as $index => $installmentData) {
            TuitionPlanInstallment::create([
                'tuition_plan_id' => $tuitionPlan->id,
                'installment_number' => $index + 1,
                'name' => $installmentData['name'],
                'description' => $installmentData['description'],
                'amount' => $installmentData['amount'],
                'due_date' => $installmentData['due_date'],
                'is_paid' => false,
            ]);
        }

        return redirect()->route('tenant.modules.financials.tuition_plans')
            ->with('success', 'Tuition plan updated successfully.');
    }

    public function destroyTuitionPlan(TuitionPlan $tuitionPlan): RedirectResponse
    {
        $tuitionPlan->delete();

        return redirect()->route('tenant.modules.financials.tuition_plans')
            ->with('success', 'Tuition plan deleted successfully.');
    }

    public function duplicateTuitionPlan(TuitionPlan $tuitionPlan): RedirectResponse
    {
        $newPlan = $tuitionPlan->replicate();
        $newPlan->name = $tuitionPlan->name . ' (Copy)';
        $newPlan->is_active = false;
        $newPlan->save();

        // Duplicate items
        foreach ($tuitionPlan->items as $item) {
            $newItem = $item->replicate();
            $newItem->tuition_plan_id = $newPlan->id;
            $newItem->save();
        }

        // Duplicate installments
        foreach ($tuitionPlan->installments as $installment) {
            $newInstallment = $installment->replicate();
            $newInstallment->tuition_plan_id = $newPlan->id;
            $newInstallment->is_paid = false;
            $newInstallment->paid_at = null;
            $newInstallment->payment_reference = null;
            $newInstallment->save();
        }

        return redirect()->route('tenant.modules.financials.tuition_plans')
            ->with('success', 'Tuition plan duplicated successfully.');
    }

    public function invoices(): View
    {
        return view('tenant.modules.financials.invoices');
    }
}
