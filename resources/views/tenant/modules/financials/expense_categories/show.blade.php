@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Expense Category Details') }}</h1>
    <div class="small text-secondary">{{ __('View category information and related expenses.') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('tenant.modules.financials.expense_categories.edit', $expenseCategory) }}" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
    </a>
    <a href="{{ route('tenant.modules.financials.expense_categories') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Categories') }}
    </a>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-header bg-light">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0 me-3">
            <span class="badge rounded-pill" style="background-color: {{ $expenseCategory->color }}; width: 16px; height: 16px;"></span>
          </div>
          <div>
            <h5 class="mb-1">{{ $expenseCategory->name }}</h5>
            <small class="text-secondary">
              <i class="{{ $expenseCategory->icon }}"></i> {{ $expenseCategory->icon }}
            </small>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border-start border-primary border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Status') }}</small>
              <div class="mt-1">
                @if($expenseCategory->is_active)
                  <span class="badge bg-success">{{ __('Active') }}</span>
                @else
                  <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-start border-info border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Parent Category') }}</small>
              <div class="mt-1">
                @if($expenseCategory->parent)
                  <span class="badge bg-light text-dark">{{ $expenseCategory->parent->name }}</span>
                @else
                  <span class="text-secondary">{{ __('Main Category') }}</span>
                @endif
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="border-start border-warning border-4 ps-3">
              <small class="text-secondary text-uppercase fw-medium">{{ __('Description') }}</small>
              <div class="mt-1">
                @if($expenseCategory->description)
                  <p class="mb-0">{{ $expenseCategory->description }}</p>
                @else
                  <span class="text-secondary">{{ __('No description provided') }}</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($expenseCategory->children && $expenseCategory->children->count() > 0)
    <div class="card shadow-sm mt-4">
      <div class="card-header bg-light">
        <h6 class="mb-0">{{ __('Subcategories') }}</h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          @foreach($expenseCategory->children as $subcategory)
          <div class="col-md-6">
            <div class="card border">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <span class="badge rounded-pill" style="background-color: {{ $subcategory->color }}; width: 12px; height: 12px;"></span>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $subcategory->name }}</h6>
                    <small class="text-secondary">
                      <i class="{{ $subcategory->icon }}"></i>
                      @if($subcategory->is_active)
                        <span class="badge bg-success ms-1">{{ __('Active') }}</span>
                      @else
                        <span class="badge bg-secondary ms-1">{{ __('Inactive') }}</span>
                      @endif
                    </small>
                  </div>
                  <a href="{{ route('tenant.modules.financials.expense_categories.show', $subcategory) }}"
                     class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header bg-light">
        <h6 class="mb-0">{{ __('Category Statistics') }}</h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="text-center">
              <div class="text-primary mb-2">
                <i class="bi bi-receipt display-6"></i>
              </div>
              <h4 class="mb-1">{{ $expenseCategory->expenses()->count() }}</h4>
              <small class="text-secondary">{{ __('Total Expenses') }}</small>
            </div>
          </div>
          <div class="col-6">
            <div class="text-center">
              <div class="text-success mb-2">
                <i class="bi bi-check-circle"></i>
              </div>
              <div class="fw-medium">{{ $expenseCategory->expenses()->where('status', 'approved')->count() }}</div>
              <small class="text-secondary">{{ __('Approved') }}</small>
            </div>
          </div>
          <div class="col-6">
            <div class="text-warning mb-2">
                <i class="bi bi-clock"></i>
              </div>
              <div class="fw-medium">{{ $expenseCategory->expenses()->where('status', 'pending')->count() }}</div>
              <small class="text-secondary">{{ __('Pending') }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mt-3 border-danger">
      <div class="card-header bg-danger bg-opacity-10">
        <h6 class="mb-0 text-danger">{{ __('Danger Zone') }}</h6>
      </div>
      <div class="card-body">
        <p class="text-secondary small mb-3">{{ __('Deleting this category will permanently remove it and cannot be undone. All associated expenses will remain but may become uncategorized.') }}</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">
          <i class="bi bi-trash me-1"></i>{{ __('Delete Category') }}
        </button>
      </div>
    </div>
  </div>
</div>

@if($expenseCategory->expenses()->count() > 0)
<div class="card shadow-sm mt-4">
  <div class="card-header bg-light">
    <h6 class="mb-0">{{ __('Recent Expenses in this Category') }}</h6>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($expenseCategory->expenses()->latest()->take(5) as $expense)
          <tr>
            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
            <td>{{ Str::limit($expense->description, 40) }}</td>
            <td>{{ $expense->currency->symbol ?? '$' }}{{ number_format($expense->amount, 2) }}</td>
            <td>
              @if($expense->status === 'approved')
                <span class="badge bg-success">{{ __('Approved') }}</span>
              @elseif($expense->status === 'rejected')
                <span class="badge bg-danger">{{ __('Rejected') }}</span>
              @else
                <span class="badge bg-warning">{{ __('Pending') }}</span>
              @endif
            </td>
            <td>
              <a href="{{ route('tenant.modules.financials.expenses.show', $expense) }}"
                 class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-center">
    <a href="{{ route('tenant.modules.financials.expenses.index', ['category' => $expenseCategory->id]) }}"
       class="btn btn-outline-primary btn-sm">
      {{ __('View All Expenses in this Category') }}
    </a>
  </div>
</div>
@endif

<form id="deleteForm" method="POST" action="{{ route('tenant.modules.financials.expense_categories.destroy', $expenseCategory) }}" style="display: none;">
  @csrf
  @method('DELETE')
</form>

<script>
function confirmDelete() {
  if (confirm(`{{ __('Are you sure you want to delete the category') }} "{{ $expenseCategory->name }}"?\n\n{{ __('This action cannot be undone and may affect existing expenses.') }}`)) {
    document.getElementById('deleteForm').submit();
  }
}
</script>
@endsection