@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.users.partials.sidebar')
@endsection

@section('content')
  {{-- Enhanced Module Notice --}}
  @if(in_array($routePrefix, ['tenant.users.students', 'tenant.users.staff']))
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
      <div class="d-flex align-items-start">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>
          <h6 class="alert-heading mb-2">{{ __('Enhanced Module Available!') }}</h6>
          <p class="mb-2">{{ __('We have new enhanced modules with comprehensive forms featuring 50+ fields, professional UI, and advanced validation.') }}</p>
          <div class="d-flex gap-2">
            @if($routePrefix === 'tenant.users.students')
              <a href="{{ route('tenant.modules.students.index') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-mortarboard-fill me-1"></i>{{ __('Go to Enhanced Students Module') }}
              </a>
            @elseif($routePrefix === 'tenant.users.staff')
              <a href="{{ route('tenant.modules.teachers.index') }}" class="btn btn-sm btn-primary me-2">
                <i class="bi bi-person-video3 me-1"></i>{{ __('Go to Enhanced Teachers Module') }}
              </a>
              <a href="{{ route('tenant.modules.human-resource.employees.index') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-person-badge me-1"></i>{{ __('Go to Enhanced Employees Module') }}
              </a>
            @endif
          </div>
        </div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Header with Actions --}}
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
    <div class="mb-3 mb-md-0">
      <h1 class="h4 fw-semibold mb-1">{{ $title }}</h1>
      <p class="text-secondary small mb-0">{{ __('Manage user accounts and permissions') }}</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#bulkActionsModal">
        <span class="bi bi-lightning-charge me-1"></span>{{ __('Bulk Actions') }}
      </button>
      <a class="btn btn-primary btn-sm" href="{{ route($routePrefix . '.create') }}">
        <span class="bi bi-plus-lg me-1"></span>{{ __('Add New') }}
      </a>
    </div>
  </div>

  {{-- Statistics Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="text-secondary small mb-1">{{ __('Total Users') }}</p>
              <h3 class="mb-0">{{ $users->total() }}</h3>
            </div>
            <div class="text-primary fs-2">
              <span class="bi bi-people"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="text-secondary small mb-1">{{ __('Active') }}</p>
              <h3 class="mb-0">{{ $users->where('email_verified_at', '!=', null)->count() }}</h3>
            </div>
            <div class="text-success fs-2">
              <span class="bi bi-check-circle"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="text-secondary small mb-1">{{ __('Pending') }}</p>
              <h3 class="mb-0">{{ $users->where('email_verified_at', null)->count() }}</h3>
            </div>
            <div class="text-warning fs-2">
              <span class="bi bi-clock-history"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="text-secondary small mb-1">{{ __('This Month') }}</p>
              <h3 class="mb-0">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
            </div>
            <div class="text-info fs-2">
              <span class="bi bi-calendar-plus"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Search and Filters --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" action="" class="row g-3 align-items-center">
        <div class="col-md-6 col-lg-4">
          <div class="input-group">
            <span class="input-group-text"><span class="bi bi-search"></span></span>
            <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Search by name or email...') }}">
          </div>
        </div>
        <div class="col-md-3 col-lg-2">
          <select name="status" class="form-select">
            <option value="">{{ __('All Status') }}</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
          </select>
        </div>
        <div class="col-md-3 col-lg-2">
          <select name="sort" class="form-select">
            <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>{{ __('Sort by Name') }}</option>
            <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>{{ __('Most Recent') }}</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" type="submit">
            <span class="bi bi-funnel me-1"></span>{{ __('Filter') }}
          </button>
        </div>
        @if($q || request('status') || request('sort'))
          <div class="col-auto">
            <a class="btn btn-outline-secondary" href="{{ route($routePrefix) }}">
              <span class="bi bi-x-lg me-1"></span>{{ __('Clear') }}
            </a>
          </div>
        @endif
      </form>
    </div>
  </div>

  {{-- Users Table --}}
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">{{ __('User List') }} ({{ $users->total() }})</h6>
        <div class="d-flex gap-2 small">
          <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <span class="bi bi-printer me-1"></span>{{ __('Print') }}
          </button>
          <button class="btn btn-sm btn-outline-secondary">
            <span class="bi bi-download me-1"></span>{{ __('Export') }}
          </button>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 50px;">
              <input type="checkbox" class="form-check-input" id="selectAll">
            </th>
            <th>{{ __('User') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Joined') }}</th>
            <th>{{ __('Last Active') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>
                <input type="checkbox" class="form-check-input user-checkbox" value="{{ $u->id }}">
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 14px; font-weight: 600;">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                  </div>
                  <div>
                    <a href="{{ route($routePrefix . '.show', $u) }}" class="fw-semibold text-decoration-none">{{ $u->name }}</a>
                    @if($u->id === auth()->id())
                      <span class="badge bg-info bg-opacity-10 text-info small ms-1">{{ __('You') }}</span>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                <small class="text-secondary">{{ $u->email }}</small>
              </td>
              <td>
                @if(isset($u->is_active) && !$u->is_active)
                  <span class="badge bg-danger bg-opacity-10 text-danger">
                    <span class="bi bi-x-circle me-1"></span>{{ __('Inactive') }}
                  </span>
                @elseif($u->email_verified_at)
                  <span class="badge bg-success bg-opacity-10 text-success">
                    <span class="bi bi-check-circle me-1"></span>{{ __('Active') }}
                  </span>
                @else
                  <span class="badge bg-warning bg-opacity-10 text-warning">
                    <span class="bi bi-clock me-1"></span>{{ __('Pending') }}
                  </span>
                @endif
              </td>
              <td>
                <small class="text-secondary">{{ $u->created_at->format('M d, Y') }}</small>
              </td>
              <td>
                <small class="text-secondary">{{ $u->updated_at->diffForHumans() }}</small>
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <a class="btn btn-outline-secondary" href="{{ route($routePrefix . '.show', $u) }}" title="{{ __('View') }}">
                    <span class="bi bi-eye"></span>
                  </a>
                  <a class="btn btn-outline-secondary" href="{{ route($routePrefix . '.edit', $u) }}" title="{{ __('Edit') }}">
                    <span class="bi bi-pencil"></span>
                  </a>
                  @if($u->id !== auth()->id())
                    @if(isset($u->is_active) && $u->is_active)
                      <button class="btn btn-outline-warning" onclick="confirmDeactivate({{ $u->id }}, '{{ $u->name }}')" title="{{ __('Deactivate') }}">
                        <span class="bi bi-pause-circle"></span>
                      </button>
                    @else
                      <form action="{{ route($routePrefix . '.activate', $u) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-success" type="submit" title="{{ __('Activate') }}">
                          <span class="bi bi-play-circle"></span>
                        </button>
                      </form>
                    @endif
                    <button class="btn btn-outline-danger" onclick="confirmDelete({{ $u->id }}, '{{ $u->name }}')" title="{{ __('Delete') }}">
                      <span class="bi bi-trash"></span>
                    </button>
                  @endif
                </div>
                <form id="delete-form-{{ $u->id }}" method="POST" action="{{ route($routePrefix . '.destroy', $u) }}" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
                <form id="deactivate-form-{{ $u->id }}" method="POST" action="{{ route($routePrefix . '.deactivate', $u) }}" class="d-none">
                  @csrf
                  <input type="hidden" name="reason" id="deactivate-reason-{{ $u->id }}">
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <div class="text-secondary">
                  <span class="bi bi-inbox fs-1 d-block mb-2"></span>
                  <p class="mb-1">{{ __('No users found.') }}</p>
                  <small>{{ __('Try adjusting your search or filter criteria.') }}</small>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if(method_exists($users, 'links') && $users->hasPages())
      <div class="card-footer bg-white">
        <div class="d-flex align-items-center justify-content-between">
          <small class="text-secondary">
            {{ __('Showing') }} {{ $users->firstItem() }} {{ __('to') }} {{ $users->lastItem() }} {{ __('of') }} {{ $users->total() }} {{ __('results') }}
          </small>
          <div>
            {{ $users->links() }}
          </div>
        </div>
      </div>
    @endif
  </div>

  {{-- Bulk Actions Modal --}}
  <div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Bulk Actions') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-secondary small">{{ __('Select users from the table and choose an action:') }}</p>
          <div class="list-group">
            <button class="list-group-item list-group-item-action" onclick="bulkAction('activate')">
              <span class="bi bi-check-circle text-success me-2"></span>{{ __('Activate Selected') }}
            </button>
            <button class="list-group-item list-group-item-action" onclick="bulkAction('deactivate')">
              <span class="bi bi-x-circle text-warning me-2"></span>{{ __('Deactivate Selected') }}
            </button>
            <button class="list-group-item list-group-item-action" onclick="bulkAction('export')">
              <span class="bi bi-download text-info me-2"></span>{{ __('Export Selected') }}
            </button>
            <button class="list-group-item list-group-item-action text-danger" onclick="bulkAction('delete')">
              <span class="bi bi-trash me-2"></span>{{ __('Delete Selected') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
// Select all checkboxes
document.getElementById('selectAll')?.addEventListener('change', function() {
  document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
});

// Confirm delete
function confirmDelete(id, name) {
  if (confirm(`{{ __('Delete user') }} "${name}"? {{ __('This action cannot be undone.') }}`)) {
    document.getElementById('delete-form-' + id).submit();
  }
}

// Confirm deactivate with reason
function confirmDeactivate(id, name) {
  const reason = prompt(`{{ __('Deactivate user') }} "${name}"?\n\n{{ __('Please provide a reason (optional):') }}`);
  if (reason !== null) { // User clicked OK (even with empty reason)
    document.getElementById('deactivate-reason-' + id).value = reason;
    document.getElementById('deactivate-form-' + id).submit();
  }
}

// Bulk actions
function bulkAction(action) {
  const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
  if (selected.length === 0) {
    alert('{{ __('Please select at least one user.') }}');
    return;
  }
  alert(`{{ __('Action') }}: ${action}\n{{ __('Selected') }}: ${selected.length} {{ __('users') }}\n\n{{ __('This feature will be implemented soon.') }}`);
}
</script>
@endpush
