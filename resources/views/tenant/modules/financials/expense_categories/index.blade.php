@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Expense Categories') }}</h1>
    <div class="small text-secondary">{{ __('Organize and manage expense categories for better tracking.') }}</div>
  </div>
  <a href="{{ route('tenant.modules.financials.expense_categories.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-circle me-1"></i>{{ __('Add Category') }}
  </a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="text-primary mb-2">
          <i class="bi bi-tags display-6"></i>
        </div>
        <h6 class="card-title mb-1">{{ $categories->count() }}</h6>
        <small class="text-secondary">{{ __('Total Categories') }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="text-success mb-2">
          <i class="bi bi-check-circle display-6"></i>
        </div>
        <h6 class="card-title mb-1">{{ $categories->where('is_active', true)->count() }}</h6>
        <small class="text-secondary">{{ __('Active Categories') }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="text-warning mb-2">
          <i class="bi bi-pause-circle display-6"></i>
        </div>
        <h6 class="card-title mb-1">{{ $categories->where('is_active', false)->count() }}</h6>
        <small class="text-secondary">{{ __('Inactive Categories') }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="text-info mb-2">
          <i class="bi bi-diagram-3 display-6"></i>
        </div>
        <h6 class="card-title mb-1">{{ $categories->whereNotNull('parent_id')->count() }}</h6>
        <small class="text-secondary">{{ __('Subcategories') }}</small>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-light">
    <div class="d-flex align-items-center justify-content-between">
      <h6 class="mb-0">{{ __('All Categories') }}</h6>
      <div class="d-flex gap-2">
        <select class="form-select form-select-sm" style="width: auto;" id="statusFilter">
          <option value="">{{ __('All Status') }}</option>
          <option value="1">{{ __('Active') }}</option>
          <option value="0">{{ __('Inactive') }}</option>
        </select>
        <input type="text" class="form-control form-control-sm" placeholder="{{ __('Search categories...') }}" id="searchInput" style="width: 200px;">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="categoriesTable">
        <thead class="table-light">
          <tr>
            <th class="border-0">{{ __('Category') }}</th>
            <th class="border-0">{{ __('Parent Category') }}</th>
            <th class="border-0">{{ __('Description') }}</th>
            <th class="border-0 text-center">{{ __('Status') }}</th>
            <th class="border-0 text-center">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($categories as $category)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                  <span class="badge rounded-pill" style="background-color: {{ $category->color }}; width: 12px; height: 12px;"></span>
                </div>
                <div>
                  <div class="fw-medium">{{ $category->name }}</div>
                  <small class="text-secondary">
                    <i class="{{ $category->icon }}"></i> {{ $category->icon }}
                  </small>
                </div>
              </div>
            </td>
            <td>
              @if($category->parent)
                <span class="badge bg-light text-dark">{{ $category->parent->name }}</span>
              @else
                <span class="text-secondary">{{ __('Main Category') }}</span>
              @endif
            </td>
            <td>
              <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $category->description }}">
                {{ Str::limit($category->description, 50) }}
              </span>
            </td>
            <td class="text-center">
              @if($category->is_active)
                <span class="badge bg-success">{{ __('Active') }}</span>
              @else
                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
              @endif
            </td>
            <td class="text-center">
              <div class="btn-group" role="group">
                <a href="{{ route('tenant.modules.financials.expense_categories.show', $category) }}"
                   class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('tenant.modules.financials.expense_categories.edit', $category) }}"
                   class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}">
                  <i class="bi bi-pencil"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}')"
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
  </div>
  @if($categories->isEmpty())
  <div class="card-body text-center py-5">
    <div class="text-secondary mb-3">
      <i class="bi bi-tags display-4"></i>
    </div>
    <h6 class="text-secondary">{{ __('No expense categories found') }}</h6>
    <p class="text-muted small mb-3">{{ __('Create your first expense category to start organizing expenses.') }}</p>
    <a href="{{ route('tenant.modules.financials.expense_categories.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i>{{ __('Create First Category') }}
    </a>
  </div>
  @endif
</div>

@can('delete', \App\Models\ExpenseCategory::class)
<form id="deleteForm" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>
@endcan

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const table = document.getElementById('categoriesTable');
  const rows = table.querySelectorAll('tbody tr');

  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      const statusBadge = row.querySelector('.badge');
      const isActive = statusBadge && statusBadge.classList.contains('bg-success');

      const matchesSearch = text.includes(searchTerm);
      const matchesStatus = !statusValue || (statusValue === '1' && isActive) || (statusValue === '0' && !isActive);

      row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterTable);
  statusFilter.addEventListener('change', filterTable);
});

function confirmDelete(id, name) {
  if (confirm(`{{ __('Are you sure you want to delete the category') }} "${name}"? {{ __('This action cannot be undone.') }}`)) {
    const form = document.getElementById('deleteForm');
    form.action = `{{ url('tenant/modules/financials/expense-categories') }}/${id}`;
    form.submit();
  }
}
</script>
@endsection