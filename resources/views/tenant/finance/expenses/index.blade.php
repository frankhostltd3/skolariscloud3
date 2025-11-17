@extends('tenant.layouts.app')
@section('title', 'Expenses')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Expenses</h1>
            <a href="{{ route('tenant.finance.expenses.create') }}" class="btn btn-primary"><i
                    class="bi bi-plus-circle me-1"></i> Record Expense</a>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Pending</h6>
                        <h3>{{ $stats['total_pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Approved</h6>
                        <h3>{{ formatMoney($stats['total_approved']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>This Month</h6>
                        <h3>{{ formatMoney($stats['this_month']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Rejected</h6>
                        <h3>{{ $stats['rejected_count'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($expenses->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                        <td>{{ $expense->title }}</td>
                                        <td>{{ $expense->category->name }}</td>
                                        <td>{{ formatMoney($expense->amount) }}</td>
                                        <td><span
                                                class="badge {{ $expense->status_badge_class }}">{{ ucfirst($expense->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tenant.finance.expenses.show', $expense) }}"
                                                class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                            @if ($expense->status === 'pending')
                                                <a href="{{ route('tenant.finance.expenses.edit', $expense) }}"
                                                    class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $expenses->links() }}
                @else
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> No expenses found.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
