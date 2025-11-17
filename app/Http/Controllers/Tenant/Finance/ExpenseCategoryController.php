<?php

namespace App\Http\Controllers\Tenant\Finance;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $query = ExpenseCategory::where('school_id', $school->id)
            ->with('parent', 'children');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('name')->paginate(perPage());

        return view('tenant.finance.expense-categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('school_id', request()->attributes->get('currentSchool')->id)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.expense-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool');

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:191|unique:expense_categories,code,NULL,id,school_id,' . $school->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:191',
            'budget_limit' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:expense_categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['school_id'] = $school->id;
        $validated['is_active'] = $request->has('is_active');
        $validated['color'] = $validated['color'] ?? '#6c757d';
        $validated['icon'] = $validated['icon'] ?? 'bi-receipt';

        ExpenseCategory::create($validated);

        return redirect()
            ->route('tenant.finance.expense-categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load('parent', 'children', 'expenses');

        $stats = [
            'total_expenses' => $expenseCategory->expenses()->count(),
            'total_amount' => $expenseCategory->expenses()->where('status', 'approved')->sum('amount'),
            'pending_expenses' => $expenseCategory->expenses()->where('status', 'pending')->count(),
            'this_month_amount' => $expenseCategory->expenses()->where('status', 'approved')->whereMonth('expense_date', now()->month)->sum('amount'),
        ];

        return view('tenant.finance.expense-categories.show', compact('expenseCategory', 'stats'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $categories = ExpenseCategory::where('school_id', $expenseCategory->school_id)
            ->whereNull('parent_id')
            ->where('id', '!=', $expenseCategory->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.finance.expense-categories.edit', compact('expenseCategory', 'categories'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:191|unique:expense_categories,code,' . $expenseCategory->id . ',id,school_id,' . $expenseCategory->school_id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:191',
            'budget_limit' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:expense_categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $expenseCategory->update($validated);

        return redirect()
            ->route('tenant.finance.expense-categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        // Check if category has expenses
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()
                ->route('tenant.finance.expense-categories.index')
                ->with('error', 'Cannot delete category with existing expenses.');
        }

        // Check if category has children
        if ($expenseCategory->children()->count() > 0) {
            return redirect()
                ->route('tenant.finance.expense-categories.index')
                ->with('error', 'Cannot delete category with sub-categories.');
        }

        $expenseCategory->delete();

        return redirect()
            ->route('tenant.finance.expense-categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}
