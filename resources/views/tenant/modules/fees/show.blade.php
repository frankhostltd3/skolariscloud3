@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ $fee->name }}</h1>
    <div class="small text-secondary">{{ __('Fee Details') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('tenant.modules.fees.record-payment', $fee) }}" class="btn btn-success btn-sm">
      <i class="bi bi-cash me-1"></i>{{ __('Record Payment') }}
    </a>
    <a href="{{ route('tenant.modules.fees.assign', $fee) }}" class="btn btn-info btn-sm">
      <i class="bi bi-person-plus me-1"></i>{{ __('Assign Fee') }}
    </a>
    <a href="{{ route('tenant.modules.fees.edit', $fee) }}" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
    </a>
    <a href="{{ route('tenant.modules.fees.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row g-4">
  <div class="col-lg-8">
    {{-- Fee Information --}}
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0">{{ __('Fee Information') }}</h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border-start border-primary border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Amount') }}</small>
              <div class="h4 mb-0 fw-semibold text-primary">{{ format_money($fee->amount) }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-success border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Category') }}</small>
              <div class="mt-1">
                <span class="badge bg-light text-dark fs-6">{{ ucfirst($fee->category) }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Due Date') }}</small>
              <div class="mt-1">{{ $fee->due_date?->format('M d, Y') ?? 'N/A' }}</div>
              @if($fee->isOverdue())
                <small class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ __('Overdue') }}</small>
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-warning border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Recurring Type') }}</small>
              <div class="mt-1">{{ $fee->getRecurringTypeLabel() }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-secondary border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Applicable To') }}</small>
              <div class="mt-1">{{ $fee->getApplicableToLabel() }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-secondary border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Status') }}</small>
              <div class="mt-1">
                <span class="badge {{ $fee->getStatusBadgeClass() }}">{{ $fee->getStatusText() }}</span>
              </div>
            </div>
          </div>
          @if($fee->description)
          <div class="col-12">
            <div class="border-start border-secondary border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Description') }}</small>
              <div class="mt-1">{{ $fee->description }}</div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Assignments --}}
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('Assignments') }} ({{ $fee->assignments->count() }})</h6>
        <a href="{{ route('tenant.modules.fees.assign', $fee) }}" class="btn btn-sm btn-primary">
          <i class="bi bi-plus-circle me-1"></i>{{ __('Add Assignment') }}
        </a>
      </div>
      <div class="card-body p-0">
        @if($fee->assignments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Assigned To') }}</th>
                <th>{{ __('Effective Date') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($fee->assignments as $assignment)
              <tr>
                <td>
                  <span class="badge bg-info">{{ ucfirst($assignment->assignment_type) }}</span>
                </td>
                <td>
                  @if($assignment->assignment_type === 'class' && $assignment->assignedClass)
                    <i class="bi bi-people me-1"></i>{{ $assignment->assignedClass->name }}
                  @elseif($assignment->assignment_type === 'student' && $assignment->assignedStudent)
                    <i class="bi bi-person me-1"></i>{{ $assignment->assignedStudent->full_name }}
                  @else
                    <span class="text-secondary">{{ __('N/A') }}</span>
                  @endif
                </td>
                <td>{{ $assignment->effective_date->format('M d, Y') }}</td>
                <td>
                  @if($assignment->is_active)
                    <span class="badge bg-success">{{ __('Active') }}</span>
                  @else
                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                  @endif
                </td>
                <td>
                  <form method="POST" action="{{ route('tenant.modules.fees.assignment.remove', $assignment) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                            onclick="return confirm('{{ __('Remove this assignment?') }}')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <p class="text-secondary">{{ __('No assignments yet') }}</p>
          <a href="{{ route('tenant.modules.fees.assign', $fee) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i>{{ __('Create First Assignment') }}
          </a>
        </div>
        @endif
      </div>
    </div>

    {{-- Payment History --}}
    <div class="card shadow-sm">
      <div class="card-header bg-light">
        <h6 class="mb-0">{{ __('Payment History') }} ({{ $payments->count() }})</h6>
      </div>
      <div class="card-body p-0">
        @if($payments->count() > 0)
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>{{ __('Transaction ID') }}</th>
                <th>{{ __('Payer') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payments as $payment)
              <tr>
                <td><code class="small">{{ $payment->transaction_id }}</code></td>
                <td>{{ $payment->payer_name }}</td>
                <td class="fw-semibold">{{ format_money($payment->amount) }}</td>
                <td><span class="badge bg-light text-dark">{{ ucfirst($payment->gateway) }}</span></td>
                <td>{{ $payment->completed_at?->format('M d, Y') ?? $payment->initiated_at->format('M d, Y') }}</td>
                <td>
                  @if($payment->status === 'completed')
                    <span class="badge bg-success">{{ __('Completed') }}</span>
                  @elseif($payment->status === 'pending')
                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                  @else
                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <p class="text-secondary">{{ __('No payments recorded yet') }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    {{-- Statistics --}}
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-light">
        <h6 class="mb-0">{{ __('Statistics') }}</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-secondary small">{{ __('Total Collected') }}</span>
            <span class="fw-semibold text-success">{{ format_money($totalCollected) }}</span>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-success" style="width: {{ $fee->amount > 0 ? min(($totalCollected / $fee->amount) * 100, 100) : 0 }}%"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-secondary small">{{ __('Assigned Students') }}</span>
            <span class="fw-semibold">{{ $assignedStudentsCount }}</span>
          </div>
        </div>
        <div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-secondary small">{{ __('Pending Payments') }}</span>
            <span class="fw-semibold text-warning">{{ $pendingPayments }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="card shadow-sm border-danger">
      <div class="card-header bg-danger bg-opacity-10">
        <h6 class="mb-0 text-danger">{{ __('Danger Zone') }}</h6>
      </div>
      <div class="card-body">
        <p class="small text-secondary mb-3">{{ __('Deleting this fee will permanently remove it. This action cannot be undone.') }}</p>
        <form method="POST" action="{{ route('tenant.modules.fees.destroy', $fee) }}" 
              onsubmit="return confirm('{{ __('Are you sure you want to delete this fee?') }}')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm w-100">
            <i class="bi bi-trash me-1"></i>{{ __('Delete Fee') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
