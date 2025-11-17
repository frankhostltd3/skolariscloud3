@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ $expense->title }}</h1>
    <div class="small text-secondary">{{ __('Expense Details') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('tenant.modules.financials.expenses.edit', $expense) }}" class="btn btn-outline-warning">
      <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
    </a>
    <a href="{{ route('tenant.modules.financials.expenses') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Expenses') }}
    </a>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('Expense Information') }}</h6>
        @if($expense->status === 'approved')
          <span class="badge bg-success">{{ __('Approved') }}</span>
        @elseif($expense->status === 'pending')
          <span class="badge bg-warning">{{ __('Pending Approval') }}</span>
        @elseif($expense->status === 'rejected')
          <span class="badge bg-danger">{{ __('Rejected') }}</span>
        @endif
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">{{ __('Title') }}</label>
              <p class="mb-0">{{ $expense->title }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">{{ __('Amount') }}</label>
              <p class="mb-0 h5 text-primary">{{ format_money($expense->amount) }}</p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">{{ __('Category') }}</label>
              <p class="mb-0">
                @if($expense->category)
                  <span class="badge" style="background-color: {{ $expense->category->color }}; color: white;">
                    <i class="bi {{ $expense->category->icon }} me-1"></i>{{ $expense->category->name }}
                  </span>
                @else
                  <span class="text-muted">{{ __('Uncategorized') }}</span>
                @endif
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">{{ __('Expense Date') }}</label>
              <p class="mb-0">{{ $expense->expense_date->format('F d, Y') }}</p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">{{ __('Payment Method') }}</label>
              <p class="mb-0">{{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">{{ __('Reference Number') }}</label>
              <p class="mb-0">{{ $expense->reference_number ?: '-' }}</p>
            </div>
          </div>
        </div>

        @if($expense->vendor_name)
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Vendor Name') }}</label>
                <p class="mb-0">{{ $expense->vendor_name }}</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Vendor Contact') }}</label>
                <p class="mb-0">{{ $expense->vendor_contact ?: '-' }}</p>
              </div>
            </div>
          </div>
        @endif

        @if($expense->description)
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Description') }}</label>
            <p class="mb-0">{{ $expense->description }}</p>
          </div>
        @endif

        @if($expense->notes)
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Notes') }}</label>
            <p class="mb-0">{{ $expense->notes }}</p>
          </div>
        @endif

        @if($expense->receipt_path)
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Receipt') }}</label>
            <p class="mb-0">
              <a href="{{ \Storage::disk('public')->url($expense->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark me-1"></i>{{ __('View Receipt') }}
              </a>
            </p>
          </div>
        @endif
      </div>
    </div>

    @if($expense->status === 'rejected' && $expense->rejected_reason)
      <div class="card border-danger mt-3">
        <div class="card-header bg-danger text-white">
          <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>{{ __('Rejection Reason') }}</h6>
        </div>
        <div class="card-body">
          <p class="mb-0">{{ $expense->rejected_reason }}</p>
        </div>
      </div>
    @endif
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Audit Trail') }}</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-semibold small">{{ __('Created By') }}</label>
          <p class="mb-1">{{ $expense->creator->name ?? 'Unknown' }}</p>
          <small class="text-muted">{{ $expense->created_at->format('M d, Y H:i') }}</small>
        </div>

        @if($expense->approver)
          <div class="mb-3">
            <label class="form-label fw-semibold small">
              @if($expense->status === 'approved')
                {{ __('Approved By') }}
              @else
                {{ __('Reviewed By') }}
              @endif
            </label>
            <p class="mb-1">{{ $expense->approver->name }}</p>
            <small class="text-muted">{{ $expense->approved_at?->format('M d, Y H:i') }}</small>
          </div>
        @endif

        <div class="mb-0">
          <label class="form-label fw-semibold small">{{ __('Last Updated') }}</label>
          <p class="mb-0"><small class="text-muted">{{ $expense->updated_at->format('M d, Y H:i') }}</small></p>
        </div>
      </div>
    </div>

    @can('approve expenses')
      @if($expense->status === 'pending')
        <div class="card mt-3">
          <div class="card-header">
            <h6 class="mb-0">{{ __('Approval Actions') }}</h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <form method="POST" action="{{ route('tenant.modules.financials.expenses.approve', $expense) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('Approve this expense?') }}')">
                  <i class="bi bi-check-circle me-1"></i>{{ __('Approve Expense') }}
                </button>
              </form>

              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i>{{ __('Reject Expense') }}
              </button>
            </div>
          </div>
        </div>
      @endif
    @endcan
  </div>
</div>

<!-- Reject Modal -->
@if($expense->status === 'pending')
  <div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('tenant.modules.financials.expenses.reject', $expense) }}">
        @csrf
        @method('PATCH')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Reject Expense') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="reason" class="form-label">{{ __('Reason for rejection') }}</label>
              <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger">{{ __('Reject Expense') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endif
@endsection