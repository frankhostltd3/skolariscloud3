{{-- Academics Sidebar --}}
<div class="bg-white border-end" style="width: 250px; min-height: calc(100vh - 56px);">
    <div class="p-3">
        <h6 class="text-uppercase text-muted mb-3 small fw-bold">{{ __('Academics') }}</h6>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('tenant.academics.education-levels.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/education-levels') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>{{ __('Education Levels') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.examination-bodies.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/examination-bodies') }}">
                <i class="bi bi-award me-2"></i>{{ __('Examination Bodies') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.countries.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/countries') }}">
                <i class="bi bi-globe me-2"></i>{{ __('Countries') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.grading_schemes.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/grading_schemes') }}">
                <i class="bi bi-award-fill me-2"></i>{{ __('Grading Systems') }}
            </a>
            <hr class="my-2">
            <a class="nav-link {{ request()->routeIs('tenant.academics.subjects.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/subjects') }}">
                <i class="bi bi-book me-2"></i>{{ __('Subjects') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.teacher-allocations.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/teacher-allocations') }}">
                <i class="bi bi-person-badge me-2"></i>{{ __('Teacher Allocation') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.terms.*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/terms') }}">
                <i class="bi bi-calendar-event me-2"></i>{{ __('Terms') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.timetable.index') || request()->routeIs('tenant.academics.timetable.edit') || request()->routeIs('tenant.academics.timetable.create') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/timetable') }}">
                <i class="bi bi-calendar3 me-2"></i>{{ __('Timetable') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.timetable.generate') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/timetable/generate') }}">
                <i class="bi bi-magic me-2"></i>{{ __('Generate Timetable') }}
            </a>
            <a class="nav-link {{ request()->routeIs('tenant.academics.classes.*') && !request()->is('tenant/academics/classes/*/streams*') ? 'active' : '' }}"
                href="{{ url('/tenant/academics/classes') }}">
                <i class="bi bi-building me-2"></i>{{ __('Classes') }}
            </a>
            <a class="nav-link {{ request()->is('tenant/academics/classes/*/streams*') ? 'active' : '' }} {{ request()->route('class') ? '' : 'disabled' }}"
                href="{{ request()->route('class') ? url('/tenant/academics/classes/' . request()->route('class')->id . '/streams') : '#' }}">
                <i class="bi bi-diagram-3 me-2"></i>{{ __('Class Streams') }}
            </a>
            <a class="nav-link disabled" href="#">
                <i class="bi bi-mortarboard me-2"></i>{{ __('Students') }}
            </a>
            <a class="nav-link disabled" href="#">
                <i class="bi bi-person-badge me-2"></i>{{ __('Teachers') }}
            </a>
            <a class="nav-link disabled" href="#">
                <i class="bi bi-clipboard-check me-2"></i>{{ __('Attendance') }}
            </a>
            <a class="nav-link disabled" href="#">
                <i class="bi bi-journal-text me-2"></i>{{ __('Grades') }}
            </a>
            <a class="nav-link disabled" href="#">
                <i class="bi bi-file-earmark-text me-2"></i>{{ __('Assignments') }}
            </a>
            <a class="nav-link disabled" href="#">
                <i class="bi bi-clipboard2-check me-2"></i>{{ __('Exams') }}
            </a>
        </nav>
    </div>
</div>

<style>
    .nav-link {
        color: #6c757d;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }

    .nav-link:hover:not(.disabled) {
        background-color: #f8f9fa;
        color: #0d6efd;
    }

    .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }

    .nav-link.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

