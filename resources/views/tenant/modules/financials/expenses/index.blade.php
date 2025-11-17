@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Expense Management') }}</h1>
    <div class="small text-secondary">{{ __('Track and manage operational expenses') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('tenant.modules.financials.expense_categories') }}" class="btn btn-outline-primary">
      <i class="bi bi-tags me-1"></i>{{ __('Categories') }}
    </a>
    <a href="{{ route('tenant.modules.financials.expenses.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i>{{ __('Add Expense') }}
    </a>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card border-primary">
      <div class="card-body text-center">
        <div class="text-primary mb-2">
          <i class="bi bi-cash-stack display-6"></i>
        </div>
  <h6 class="card-title">{{ __('Total Expenses') }}</h6>
  <h4 class="mb-0">{{ format_money($totalExpenses) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-warning">
      <div class="card-body text-center">
        <div class="text-warning mb-2">
          <i class="bi bi-clock-history display-6"></i>
        </div>
  <h6 class="card-title">{{ __('Pending Approval') }}</h6>
  <h4 class="mb-0">{{ format_money($pendingExpenses) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-info">
      <div class="card-body text-center">
        <div class="text-info mb-2">
          <i class="bi bi-calendar-month display-6"></i>
        </div>
  <h6 class="card-title">{{ __('This Month') }}</h6>
  <h4 class="mb-0">{{ format_money($thisMonthExpenses) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-success">
      <div class="card-body text-center">
        <div class="text-success mb-2">
          <i class="bi bi-check-circle display-6"></i>
        </div>
        <h6 class="card-title">{{ __('Approved') }}</h6>
        <h4 class="mb-0">{{ $expenses->where('status', 'approved')->count() }}</h4>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-3">
        <label for="category" class="form-label">{{ __('Category') }}</label>
        <select class="form-select" id="category" name="category">
          <option value="">{{ __('All Categories') }}</option>
          @foreach(\App\Models\ExpenseCategory::active()->get() as $category)
            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="status" class="form-label">{{ __('Status') }}</label>
        <select class="form-select" id="status" name="status">
          <option value="">{{ __('All Status') }}</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
        </select>
      </div>
      <div class="col-md-2">
        <label for="date_from" class="form-label">{{ __('From Date') }}</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
      </div>
      <div class="col-md-2">
        <label for="date_to" class="form-label">{{ __('To Date') }}</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-outline-primary me-2">{{ __('Filter') }}</button>
        <a href="{{ route('tenant.modules.financials.expenses') }}" class="btn btn-outline-secondary me-2">{{ __('Clear') }}</a>
        <a href="{{ route('tenant.modules.financials.expenses.export') }}" class="btn btn-outline-success">{{ __('Export') }}</a>
      </div>
    </form>
  </div>
</div>

<!-- Expenses Table -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Vendor') }}</th>
            <th class="text-end">{{ __('Amount') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $expense)
            <tr>
              <td>{{ $expense->expense_date->format('M d, Y') }}</td>
              <td>
                <div class="fw-semibold">{{ $expense->title }}</div>
                @if($expense->reference_number)
                  <small class="text-muted">{{ __('Ref:') }} {{ $expense->reference_number }}</small>
                @endif
              </td>
              <td>
                @if($expense->category)
                  <span class="badge" style="background-color: {{ $expense->category->color }}; color: white;">
                    <i class="bi {{ $expense->category->icon }} me-1"></i>{{ $expense->category->name }}
                  </span>
                @endif
              </td>
              <td>{{ $expense->vendor_name ?: '-' }}</td>
              <td class="text-end fw-semibold">{{ format_money($expense->amount) }}</td>
              <td>
                @if($expense->status === 'approved')
                  <span class="badge bg-success">{{ __('Approved') }}</span>
                @elseif($expense->status === 'pending')
                  <span class="badge bg-warning">{{ __('Pending') }}</span>
                @elseif($expense->status === 'rejected')
                  <span class="badge bg-danger">{{ __('Rejected') }}</span>
                @endif
              </td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('tenant.modules.financials.expenses.show', $expense) }}" class="btn btn-outline-info" title="{{ __('View') }}">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('tenant.modules.financials.expenses.edit', $expense) }}" class="btn btn-outline-warning" title="{{ __('Edit') }}">
                    <i class="bi bi-pencil"></i>
                  </a>
                  @can('approve expenses')
                    @if($expense->status === 'pending')
                      <form method="POST" action="{{ route('tenant.modules.financials.expenses.approve', $expense) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success" title="{{ __('Approve') }}" onclick="return confirm('{{ __('Approve this expense?') }}')">
                          <i class="bi bi-check-circle"></i>
                        </button>
                      </form>
                      <button type="button" class="btn btn-outline-danger" title="{{ __('Reject') }}" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $expense->id }}">
                        <i class="bi bi-x-circle"></i>
                      </button>
                    @endif
                  @endcan
                  <form method="POST" action="{{ route('tenant.modules.financials.expenses.destroy', $expense) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure?') }}')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>

            <!-- Reject Modal -->
            @if($expense->status === 'pending')
              <div class="modal fade" id="rejectModal{{ $expense->id }}" tabindex="-1">
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
                          <label for="reason{{ $expense->id }}" class="form-label">{{ __('Reason for rejection') }}</label>
                          <textarea class="form-control" id="reason{{ $expense->id }}" name="reason" rows="3" required></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            @endif
          @empty
            <tr>
              <td colspan="7" class="text-center text-secondary py-4">
                <i class="bi bi-receipt display-4 text-muted mb-3"></i>
                <div>{{ __('No expenses recorded yet') }}</div>
                <a href="{{ route('tenant.modules.financials.expenses.create') }}" class="btn btn-primary mt-3">
                  {{ __('Add Your First Expense') }}
                </a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($expenses->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $expenses->links() }}
      </div>
    @endif
  </div>
</div>
@endsection