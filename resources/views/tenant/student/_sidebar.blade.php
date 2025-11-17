<div class="card shadow-sm">
    <div class="card-header fw-semibold">{{ __('Student Menu') }}</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.dashboard') ? 'active' : '' }}"
           href="{{ route('tenant.student.dashboard') }}" @if (request()->routeIs('tenant.student.dashboard')) aria-current="page" @endif>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Dashboard') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.classroom*') ? 'active' : '' }}"
           href="{{ route('tenant.student.classroom.index') }}">
            <span class="bi bi-book me-2"></span>{{ __('My Classroom') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.assignments*') ? 'active' : '' }}"
           href="{{ route('tenant.student.assignments.index') }}">
            <span class="bi bi-journal-check me-2"></span>{{ __('Assignments') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.exams*') ? 'active' : '' }}"
           href="{{ route('tenant.student.exams.index') }}">
            <span class="bi bi-pencil-square me-2"></span>{{ __('Exams & Quizzes') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.attendance*') ? 'active' : '' }}"
           href="{{ route('tenant.student.attendance.index') }}">
            <span class="bi bi-calendar-check me-2"></span>{{ __('My Attendance') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.grades*') ? 'active' : '' }}"
           href="{{ route('tenant.student.grades.index') }}">
            <span class="bi bi-trophy me-2"></span>{{ __('My Grades') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.timetable*') ? 'active' : '' }}"
           href="{{ route('tenant.student.timetable.index') }}">
            <span class="bi bi-calendar2-week me-2"></span>{{ __('Timetable') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.library*') ? 'active' : '' }}"
           href="{{ route('tenant.student.library.index') }}">
            <span class="bi bi-book-half me-2"></span>{{ __('Library') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.fees*') ? 'active' : '' }}"
           href="{{ route('tenant.student.fees.index') }}">
            <span class="bi bi-cash-coin me-2"></span>{{ __('Fees & Payments') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.messages*') ? 'active' : '' }}"
           href="{{ route('tenant.student.messages.index') }}">
            <span class="bi bi-envelope me-2"></span>{{ __('Messages') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.profile*') ? 'active' : '' }}"
           href="{{ route('tenant.student.profile') }}">
            <span class="bi bi-person me-2"></span>{{ __('My Profile') }}
        </a>
    </div>
</div>
