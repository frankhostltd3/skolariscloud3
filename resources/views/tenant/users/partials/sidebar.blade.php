<div class="card shadow-sm mb-3">
  <div class="card-header fw-semibold bg-primary text-white">
    <i class="bi bi-people me-2"></i>{{ __('User Management') }}
  </div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.users.admins*') ? 'active' : '' }}" href="{{ route('tenant.users.admins') }}">
      <i class="bi bi-person-gear me-2"></i>{{ __('Administrators') }}
    </a>
    <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.users.parents*') ? 'active' : '' }}" href="{{ route('tenant.users.parents') }}">
      <i class="bi bi-people me-2"></i>{{ __('Parents') }}
    </a>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header fw-semibold bg-success text-white">
    <i class="bi bi-grid me-2"></i>{{ __('Enhanced Modules') }}
  </div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.teachers*') ? 'active' : '' }}" href="{{ route('tenant.modules.teachers.index') }}">
      <i class="bi bi-person-video3 me-2"></i>{{ __('Teachers') }}
      <span class="badge bg-success bg-opacity-10 text-success float-end">{{ __('Enhanced') }}</span>
    </a>
    <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.students*') ? 'active' : '' }}" href="{{ route('tenant.modules.students.index') }}">
      <i class="bi bi-mortarboard-fill me-2"></i>{{ __('Students') }}
      <span class="badge bg-success bg-opacity-10 text-success float-end">{{ __('Enhanced') }}</span>
    </a>
    <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.human_resources.employees*') ? 'active' : '' }}" href="{{ route('tenant.modules.human_resources.employees.index') }}">
      <i class="bi bi-person-badge me-2"></i>{{ __('Employees') }}
      <span class="badge bg-success bg-opacity-10 text-success float-end">{{ __('Enhanced') }}</span>
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">
    <i class="bi bi-info-circle me-2"></i>{{ __('Quick Info') }}
  </div>
  <div class="card-body small text-secondary">
    <p class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>{{ __('Enhanced modules include comprehensive forms with 80+ fields') }}</p>
    <p class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>{{ __('Professional card-based UI') }}</p>
    <p class="mb-0"><i class="bi bi-check-circle text-success me-1"></i>{{ __('Advanced validation & file uploads') }}</p>
  </div>
</div>
