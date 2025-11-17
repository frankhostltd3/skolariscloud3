@extends('tenant.layouts.app')

@section('title', 'Expense Category Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ $expenseCategory->name }}</h1>
            <div>
                <a href="{{ route('tenant.finance.expense-categories.edit', $expenseCategory) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('tenant.finance.expense-categories.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Category Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Icon & Color</label>
                            <div><i class="{{ $expenseCategory->icon }} fs-1"
                                    style="color: {{ $expenseCategory->color }}"></i></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Code</label>
                            <div>{{ $expenseCategory->code ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Parent Category</label>
                            <div>{{ $expenseCategory->parent?->name ?? 'None' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Budget Limit</label>
                            <div>
                                {{ $expenseCategory->budget_limit ? formatMoney($expenseCategory->budget_limit) : 'No limit' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Status</label>
                            <div><span
                                    class="badge {{ $expenseCategory->status_badge_class }}">{{ $expenseCategory->status_text }}</span>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small">Description</label>
                            <div>{{ $expenseCategory->description ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Expenses</h6>
                                <h3 class="mb-0">{{ $stats['total_expenses'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Amount</h6>
                                <h3 class="mb-0">{{ formatMoney($stats['total_amount']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title">Pending</h6>
                                <h3 class="mb-0">{{ $stats['pending_expenses'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">This Month</h6>
                                <h3 class="mb-0">{{ formatMoney($stats['this_month_amount']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sub-Categories</h5>
                    </div>
                    <div class="card-body">
                        @if ($expenseCategory->children->count() > 0)
                            <div class="list-group">
                                @foreach ($expenseCategory->children as $child)
                                    <a href="{{ route('tenant.finance.expense-categories.show', $child) }}"
                                        class="list-group-item list-group-item-action">
                                        <i class="{{ $child->icon }} me-2" style="color: {{ $child->color }}"></i>
                                        {{ $child->name }}
                                        <span
                                            class="badge {{ $child->status_badge_class }} float-end">{{ $child->status_text }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No sub-categories</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
