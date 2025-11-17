<div class="card shadow-sm">
  <div class="card-header fw-semibold">{{ __('Teachers Menu') }}</div>
  <div class="list-group list-group-flush">
    <a
      class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.teachers.index') ? 'active' : '' }}"
      href="{{ route('tenant.modules.teachers.index') }}"
      @if(request()->routeIs('tenant.modules.teachers.index')) aria-current="page" @endif
    >
      <span class="bi bi-people me-2"></span>{{ __('All Teachers') }}
    </a>

    @can('create', App\Models\Teacher::class)
      <a
        class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.teachers.create') ? 'active' : '' }}"
        href="{{ route('tenant.modules.teachers.create') }}"
        @if(request()->routeIs('tenant.modules.teachers.create')) aria-current="page" @endif
      >
        <span class="bi bi-person-plus me-2"></span>{{ __('Add New Teacher') }}
      </a>
    @endcan

    @if(Route::has('tenant.academics.allocations.teachers.index'))
      <a
        class="list-group-item list-group-item-action {{ request()->routeIs('tenant.academics.allocations.teachers.*') ? 'active' : '' }}"
        href="{{ route('tenant.academics.allocations.teachers.index') }}"
        @if(request()->routeIs('tenant.academics.allocations.teachers.*')) aria-current="page" @endif
      >
        <span class="bi bi-book me-2"></span>{{ __('Teacher Allocations') }}
      </a>
    @endif

    @php($employeesActive = request()->routeIs('tenant.modules.employees.*'))
    <a
      class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $employeesActive ? 'active' : '' }}"
      data-bs-toggle="collapse"
      href="#employeesMenu"
      role="button"
      aria-expanded="{{ $employeesActive ? 'true' : 'false' }}"
      aria-controls="employeesMenu"
    >
      <span><span class="bi bi-briefcase me-2"></span>{{ __('Employee Management') }}</span>
      <span class="bi bi-chevron-down small"></span>
    </a>
    <div class="collapse {{ $employeesActive ? 'show' : '' }}" id="employeesMenu">
      <div class="list-group list-group-flush ms-3">
        @if(Route::has('tenant.modules.employees.index'))
          <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.employees.index') ? 'active' : '' }}" href="{{ route('tenant.modules.employees.index') }}">
            <span class="bi bi-people me-2"></span>{{ __('All Employees') }}
          </a>
        @endif
        @if(Route::has('tenant.modules.employees.create'))
          <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.employees.create') ? 'active' : '' }}" href="{{ route('tenant.modules.employees.create') }}">
            <span class="bi bi-person-plus-fill me-2"></span>{{ __('Add Employee') }}
          </a>
        @endif
      </div>
    </div>

    @php($reportsActive = request()->routeIs('tenant.reports.*'))
    <a
      class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $reportsActive ? 'active' : '' }}"
      data-bs-toggle="collapse"
      href="#teacherReportsMenu"
      role="button"
      aria-expanded="{{ $reportsActive ? 'true' : 'false' }}"
      aria-controls="teacherReportsMenu"
    >
      <span><span class="bi bi-bar-chart-line me-2"></span>{{ __('Reports') }}</span>
      <span class="bi bi-chevron-down small"></span>
    </a>
    <div class="collapse {{ $reportsActive ? 'show' : '' }}" id="teacherReportsMenu">
      <div class="list-group list-group-flush ms-3">
        @if(Route::has('tenant.reports.academic'))
          <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.academic') ? 'active' : '' }}" href="{{ route('tenant.reports.academic') }}">
            <span class="bi bi-mortarboard me-2"></span>{{ __('Academic Reports') }}
          </a>
        @endif
        @if(Route::has('tenant.reports.attendance'))
          <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.attendance') ? 'active' : '' }}" href="{{ route('tenant.reports.attendance') }}">
            <span class="bi bi-calendar-check me-2"></span>{{ __('Attendance Reports') }}
          </a>
        @endif
      </div>
    </div>

    <a
      class="list-group-item list-group-item-action {{ request()->routeIs('tenant.admin') ? 'active' : '' }}"
      href="{{ route('tenant.admin') }}"
      @if(request()->routeIs('tenant.admin')) aria-current="page" @endif
    >
      <span class="bi bi-speedometer2 me-2"></span>{{ __('Admin Dashboard') }}
    </a>
  </div>
</div>
