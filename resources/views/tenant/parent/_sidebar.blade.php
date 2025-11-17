<div class="card shadow-sm">
    <div class="card-header fw-semibold">{{ __('Parent Menu') }}</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.dashboard') ? 'active' : '' }}"
           href="{{ route('tenant.parent.dashboard') }}" @if (request()->routeIs('tenant.parent.dashboard')) aria-current="page" @endif>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Dashboard') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.children*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.children.index') }}">
            <span class="bi bi-people me-2"></span>{{ __('My Children') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.attendance*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.attendance.index') }}">
            <span class="bi bi-calendar-check me-2"></span>{{ __('Attendance') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.grades*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.grades.index') }}">
            <span class="bi bi-trophy me-2"></span>{{ __('Academic Progress') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.fees*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.fees.index') }}">
            <span class="bi bi-cash-coin me-2"></span>{{ __('Fees & Payments') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.reports*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.reports.index') }}">
            <span class="bi bi-file-text me-2"></span>{{ __('Report Cards') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.timetable*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.timetable.index') }}">
            <span class="bi bi-calendar2-week me-2"></span>{{ __('Timetable') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.messages*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.messages.index') }}">
            <span class="bi bi-envelope me-2"></span>{{ __('Messages') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.events*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.events.index') }}">
            <span class="bi bi-calendar-event me-2"></span>{{ __('Events & Notices') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.parent.profile*') ? 'active' : '' }}"
           href="{{ route('tenant.parent.profile') }}">
            <span class="bi bi-person me-2"></span>{{ __('My Profile') }}
        </a>
    </div>
</div>
