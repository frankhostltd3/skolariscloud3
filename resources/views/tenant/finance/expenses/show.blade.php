@extends('tenant.layouts.app')
@section('title', 'Expense Details')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h1 class="h3">Expense #{{ $expense->id }}</h1>
            <div>
                @if ($expense->status === 'pending')
                    <form action="{{ route('tenant.finance.expenses.approve', $expense) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Approve this expense?')"><i
                                class="bi bi-check-circle me-1"></i> Approve</button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"><i
                            class="bi bi-x-circle me-1"></i> Reject</button>
                @endif
                <a href="{{ route('tenant.finance.expenses.index') }}" class="btn btn-secondary"><i
                        class="bi bi-arrow-left me-1"></i> Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Expense Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Title:</strong> {{ $expense->title }}</p>
                        <p><strong>Amount:</strong> {{ formatMoney($expense->amount) }}</p>
                        <p><strong>Category:</strong> {{ $expense->category->name }}</p>
                        <p><strong>Date:</strong> {{ $expense->expense_date->format('M d, Y') }}</p>
                        <p><strong>Payment Method:</strong> {{ $expense->payment_method_label }}</p>
                        <p><strong>Status:</strong> <span
                                class="badge {{ $expense->status_badge_class }}">{{ ucfirst($expense->status) }}</span></p>
                        <p><strong>Created By:</strong> {{ $expense->creator->name }}</p>
                        @if ($expense->approver)
                            <p><strong>Approved By:</strong> {{ $expense->approver->name }}</p>
                        @endif
                        @if ($expense->description)
                            <p><strong>Description:</strong> {{ $expense->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('tenant.finance.expenses.reject', $expense) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Expense</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea name="rejected_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
