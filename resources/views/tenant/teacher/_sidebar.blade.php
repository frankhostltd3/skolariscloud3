<div class="card shadow-sm">
    <div class="card-header fw-semibold">{{ __('Teacher Menu') }}</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.dashboard') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.dashboard') }}" @if (request()->routeIs('tenant.teacher.dashboard')) aria-current="page" @endif>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Dashboard') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.classes*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.classes.index') }}">
            <span class="bi bi-people me-2"></span>{{ __('My Classes') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.students*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.students.index') }}">
            <span class="bi bi-person-badge me-2"></span>{{ __('Students') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.attendance*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.attendance.index') }}">
            <span class="bi bi-calendar-check me-2"></span>{{ __('Attendance') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.assignments*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.assignments.index') }}">
            <span class="bi bi-journal-check me-2"></span>{{ __('Assignments') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.grades*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.grades.index') }}">
            <span class="bi bi-clipboard-check me-2"></span>{{ __('Grades') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.exams*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.exams.index') }}">
            <span class="bi bi-pencil-square me-2"></span>{{ __('Exams & Tests') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.timetable*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.timetable.index') }}">
            <span class="bi bi-calendar2-week me-2"></span>{{ __('Timetable') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.materials*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.materials.index') }}">
            <span class="bi bi-files me-2"></span>{{ __('Course Materials') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.classroom*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.classroom.index') }}">
            <span class="bi bi-camera-video me-2"></span>{{ __('Virtual Classroom') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.messages*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.messages.index') }}">
            <span class="bi bi-envelope me-2"></span>{{ __('Messages') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.teacher.profile*') ? 'active' : '' }}"
           href="{{ route('tenant.teacher.profile') }}">
            <span class="bi bi-person me-2"></span>{{ __('My Profile') }}
        </a>
    </div>
</div>
