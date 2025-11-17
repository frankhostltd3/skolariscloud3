@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Fee Management') }}</h1>
    <div class="small text-secondary">{{ __('Manage school fees, assignments, and payments') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('tenant.modules.fees.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i>{{ __('Create Fee') }}
    </a>
  </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-3 mb-4">
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="p-3 bg-primary bg-opacity-10 rounded">
              <i class="bi bi-cash-stack text-primary fs-4"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <div class="small text-secondary text-uppercase fw-medium">{{ __('Total Fees') }}</div>
            <div class="h4 mb-0 fw-semibold">{{ format_money($totalFees) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="p-3 bg-success bg-opacity-10 rounded">
              <i class="bi bi-check-circle text-success fs-4"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <div class="small text-secondary text-uppercase fw-medium">{{ __('Collected') }}</div>
            <div class="h4 mb-0 fw-semibold text-success">{{ format_money($paidAmount) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="p-3 bg-warning bg-opacity-10 rounded">
              <i class="bi bi-exclamation-circle text-warning fs-4"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <div class="small text-secondary text-uppercase fw-medium">{{ __('Outstanding') }}</div>
            <div class="h4 mb-0 fw-semibold text-warning">{{ format_money($outstandingAmount) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="p-3 bg-danger bg-opacity-10 rounded">
              <i class="bi bi-calendar-x text-danger fs-4"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <div class="small text-secondary text-uppercase fw-medium">{{ __('Overdue Fees') }}</div>
            <div class="h4 mb-0 fw-semibold text-danger">{{ $overdueFees }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('tenant.modules.fees.index') }}" class="row g-3">
      <div class="col-md-3">
        <label class="form-label small fw-medium">{{ __('Search') }}</label>
        <input type="text" name="search" class="form-control" placeholder="{{ __('Search fees...') }}" value="{{ request('search') }}">
      </div>

      <div class="col-md-2">
        <label class="form-label small fw-medium">{{ __('Category') }}</label>
        <select name="category" class="form-select">
          <option value="">{{ __('All Categories') }}</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
              {{ ucfirst($cat) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label small fw-medium">{{ __('Status') }}</label>
        <select name="status" class="form-select">
          <option value="">{{ __('All Status') }}</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
          <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label small fw-medium">{{ __('Recurring Type') }}</label>
        <select name="recurring_type" class="form-select">
          <option value="">{{ __('All Types') }}</option>
          <option value="one-time" {{ request('recurring_type') == 'one-time' ? 'selected' : '' }}>{{ __('One-time') }}</option>
          <option value="monthly" {{ request('recurring_type') == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
          <option value="quarterly" {{ request('recurring_type') == 'quarterly' ? 'selected' : '' }}>{{ __('Quarterly') }}</option>
          <option value="yearly" {{ request('recurring_type') == 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
          <option value="term-based" {{ request('recurring_type') == 'term-based' ? 'selected' : '' }}>{{ __('Term-based') }}</option>
        </select>
      </div>

      <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-funnel me-1"></i>{{ __('Filter') }}
        </button>
        <a href="{{ route('tenant.modules.fees.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle me-1"></i>{{ __('Clear') }}
        </a>
      </div>
    </form>
  </div>
</div>

{{-- Success/Error Messages --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Fees Table --}}
<div class="card shadow-sm">
  <div class="card-header bg-light">
    <h6 class="mb-0">{{ __('All Fees') }} ({{ $fees->total() }})</h6>
  </div>
  <div class="card-body p-0">
    @if($fees->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Fee Name') }}</th>
              <th>{{ __('Category') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Due Date') }}</th>
              <th>{{ __('Recurring') }}</th>
              <th>{{ __('Assigned To') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-end">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fees as $fee)
            <tr>
              <td>
                <div class="fw-medium">{{ $fee->name }}</div>
                @if($fee->description)
                  <small class="text-secondary">{{ Str::limit($fee->description, 50) }}</small>
                @endif
              </td>
              <td>
                <span class="badge bg-light text-dark">{{ ucfirst($fee->category) }}</span>
              </td>
              <td class="fw-semibold">{{ format_money($fee->amount) }}</td>
              <td>
                @if($fee->due_date)
                  <div>{{ $fee->due_date->format('M d, Y') }}</div>
                  @if($fee->isOverdue())
                    <small class="text-danger">{{ __('Overdue') }}</small>
                  @elseif($fee->isUpcoming())
                    <small class="text-warning">{{ __('Due Soon') }}</small>
                  @endif
                @else
                  <span class="text-secondary">{{ __('N/A') }}</span>
                @endif
              </td>
              <td>
                <small class="text-secondary">{{ $fee->getRecurringTypeLabel() }}</small>
              </td>
              <td>
                <div>{{ $fee->getApplicableToLabel() }}</div>
                <small class="text-secondary">
                  {{ $fee->getAssignedStudentsCount() }} {{ __('assigned') }}
                </small>
              </td>
              <td>
                <span class="badge {{ $fee->getStatusBadgeClass() }}">
                  {{ $fee->getStatusText() }}
                </span>
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('tenant.modules.fees.show', $fee) }}" 
                     class="btn btn-outline-primary" 
                     title="{{ __('View Details') }}">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('tenant.modules.fees.edit', $fee) }}" 
                     class="btn btn-outline-warning" 
                     title="{{ __('Edit') }}">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="{{ route('tenant.modules.fees.assign', $fee) }}" 
                     class="btn btn-outline-info" 
                     title="{{ __('Assign') }}">
                    <i class="bi bi-person-plus"></i>
                  </a>
                  <a href="{{ route('tenant.modules.fees.record-payment', $fee) }}" 
                     class="btn btn-outline-success" 
                     title="{{ __('Record Payment') }}">
                    <i class="bi bi-cash"></i>
                  </a>
                  <button type="button" 
                          class="btn btn-outline-danger" 
                          onclick="confirmDelete({{ $fee->id }}, '{{ addslashes($fee->name) }}')"
                          title="{{ __('Delete') }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="card-footer">
        {{ $fees->links() }}
      </div>
    @else
      <div class="text-center py-5">
        <div class="text-secondary mb-3">
          <i class="bi bi-cash-stack display-4"></i>
        </div>
        <h6 class="text-secondary">{{ __('No fees found') }}</h6>
        <p class="text-muted small mb-3">
          @if(request()->hasAny(['search', 'category', 'status', 'recurring_type']))
            {{ __('No fees match your search criteria. Try adjusting your filters.') }}
          @else
            {{ __('Create your first fee to start managing school payments.') }}
          @endif
        </p>
        @if(!request()->hasAny(['search', 'category', 'status', 'recurring_type']))
          <a href="{{ route('tenant.modules.fees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>{{ __('Create First Fee') }}
          </a>
        @endif
      </div>
    @endif
  </div>
</div>

{{-- Recent Payments --}}
@if($recentPayments->count() > 0)
<div class="card shadow-sm mt-4">
  <div class="card-header bg-light">
    <h6 class="mb-0">{{ __('Recent Payments') }}</h6>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>{{ __('Transaction ID') }}</th>
            <th>{{ __('Reference') }}</th>
            <th>{{ __('Payer') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Status') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentPayments as $payment)
          <tr>
            <td><code class="small">{{ $payment->transaction_id }}</code></td>
            <td>{{ $payment->reference }}</td>
            <td>
              <div>{{ $payment->payer_name }}</div>
              @if($payment->payer_email)
                <small class="text-secondary">{{ $payment->payer_email }}</small>
              @endif
            </td>
            <td class="fw-semibold">{{ format_money($payment->amount) }}</td>
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
  </div>
</div>
@endif

{{-- Delete Form (Hidden) --}}
<form id="deleteForm" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

<script>
function confirmDelete(feeId, feeName) {
  if (confirm(`{{ __('Are you sure you want to delete the fee') }} "${feeName}"?\n\n{{ __('This action cannot be undone if the fee has no payments.') }}`)) {
    const form = document.getElementById('deleteForm');
    form.action = `/modules/fees/${feeId}`;
    form.submit();
  }
}
</script>
@endsection
