<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Teacher Portal')) · {{ config('app.name', 'SMATCAMPUS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Quicksand', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .tenant-shell {
            min-height: 100vh;
            display: flex;
        }

        .tenant-sidebar {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            background-color: #ffffff;
            border-right: 1px solid rgba(15, 23, 42, 0.08);
        }

        .tenant-main {
            background-color: #f9fafb;
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .tenant-content {
            max-width: 1280px;
        }

        .sidebar-header {
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .sidebar-header .navbar-brand {
            font-weight: 600;
            color: #1d4ed8;
        }

        .sidebar-user .avatar-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
        }

        .tenant-sidebar .nav-link {
            color: #4b5563;
            font-weight: 500;
            border-radius: 0.65rem;
        }

        .tenant-sidebar .nav-link.active,
        .tenant-sidebar .nav-link:hover {
            background-color: #e0e7ff;
            color: #1e3a8a;
        }

        .teacher-topbar {
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        @media (max-width: 991.98px) {
            .tenant-sidebar {
                position: fixed;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1050;
            }

            .tenant-sidebar.open {
                transform: translateX(0);
            }

            .tenant-main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @php
        $school = $school ?? request()->attributes->get('currentSchool');
        $teacherUser = auth()->user();

        $teacherNavLinks = [];
        if (Route::has('tenant.teacher.dashboard')) {
            $teacherNavLinks[] = [
                'label' => __('Dashboard'),
                'icon' => 'bi bi-speedometer2',
                'url' => route('tenant.teacher.dashboard'),
                'active' => request()->routeIs('tenant.teacher.dashboard'),
            ];
        }
        if (Route::has('tenant.teacher.classes.index')) {
            $teacherNavLinks[] = [
                'label' => __('Classes'),
                'icon' => 'bi bi-journal-bookmark',
                'url' => route('tenant.teacher.classes.index'),
                'active' => request()->routeIs('tenant.teacher.classes.*'),
            ];
        }
        if (Route::has('tenant.teacher.students.index')) {
            $teacherNavLinks[] = [
                'label' => __('Students'),
                'icon' => 'bi bi-people',
                'url' => route('tenant.teacher.students.index'),
                'active' => request()->routeIs('tenant.teacher.students.*'),
            ];
        }
        if (Route::has('tenant.teacher.subjects.index')) {
            $teacherNavLinks[] = [
                'label' => __('Subjects'),
                'icon' => 'bi bi-book',
                'url' => route('tenant.teacher.subjects.index'),
                'active' => request()->routeIs('tenant.teacher.subjects.*'),
            ];
        }
        if (Route::has('tenant.teacher.attendance.index')) {
            $teacherNavLinks[] = [
                'label' => __('Attendance'),
                'icon' => 'bi bi-clipboard-check',
                'url' => route('tenant.teacher.attendance.index'),
                'active' => request()->routeIs('tenant.teacher.attendance.*'),
            ];
        }
        if (Route::has('tenant.teacher.timetable.index')) {
            $teacherNavLinks[] = [
                'label' => __('Timetable'),
                'icon' => 'bi bi-calendar-week',
                'url' => route('tenant.teacher.timetable.index'),
                'active' => request()->routeIs('tenant.teacher.timetable.*'),
            ];
        }
        if (Route::has('tenant.teacher.grades.index')) {
            $teacherNavLinks[] = [
                'label' => __('Grades'),
                'icon' => 'bi bi-award',
                'url' => route('tenant.teacher.grades.index'),
                'active' => request()->routeIs('tenant.teacher.grades.*'),
            ];
        }
        if (Route::has('tenant.teacher.classroom.index')) {
            $teacherNavLinks[] = [
                'label' => __('Online Classroom'),
                'icon' => 'bi bi-easel2',
                'url' => route('tenant.teacher.classroom.index'),
                'active' =>
                    request()->routeIs('tenant.teacher.classroom.index') ||
                    request()->routeIs('tenant.teacher.classroom.virtual.*'),
            ];
        }

        // Assignment System Section Header
        $teacherNavLinks[] = [
            'type' => 'section',
            'label' => __('ASSIGNMENT SYSTEM'),
        ];

        if (Route::has('tenant.teacher.classroom.exercises.index')) {
            $teacherNavLinks[] = [
                'label' => __('All Assignments'),
                'icon' => 'bi bi-list-task',
                'url' => route('tenant.teacher.classroom.exercises.index'),
                'active' =>
                    request()->routeIs('tenant.teacher.classroom.exercises.index') ||
                    request()->routeIs('tenant.teacher.classroom.exercises.show') ||
                    request()->routeIs('tenant.teacher.classroom.exercises.edit') ||
                    request()->routeIs('tenant.teacher.classroom.exercises.submissions') ||
                    request()->routeIs('tenant.teacher.classroom.exercises.analytics'),
                'badge' => 'NEW',
            ];
        }
        if (Route::has('tenant.teacher.classroom.exercises.create')) {
            $teacherNavLinks[] = [
                'label' => __('Create Assignment'),
                'icon' => 'bi bi-plus-circle',
                'url' => route('tenant.teacher.classroom.exercises.create'),
                'active' => request()->routeIs('tenant.teacher.classroom.exercises.create'),
            ];
        }

        // Divider after Assignment System
        $teacherNavLinks[] = [
            'type' => 'divider',
        ];

        if (Route::has('tenant.teacher.classroom.quizzes.index')) {
            $teacherNavLinks[] = [
                'label' => __('Quizzes'),
                'icon' => 'bi bi-question-circle',
                'url' => route('tenant.teacher.classroom.quizzes.index'),
                'active' => request()->routeIs('tenant.teacher.classroom.quizzes.*'),
            ];
        }
        if (Route::has('tenant.teacher.classroom.exams.index')) {
            $teacherNavLinks[] = [
                'label' => __('Exams'),
                'icon' => 'bi bi-laptop',
                'url' => route('tenant.teacher.classroom.exams.index'),
                'active' => request()->routeIs('tenant.teacher.classroom.exams.*'),
            ];
        }
        if (Route::has('tenant.teacher.classroom.lessons.index')) {
            $teacherNavLinks[] = [
                'label' => __('Lesson Plans'),
                'icon' => 'bi bi-journal-text',
                'url' => route('tenant.teacher.classroom.lessons.index'),
                'active' => request()->routeIs('tenant.teacher.classroom.lessons.*'),
            ];
        }
        if (Route::has('tenant.teacher.settings')) {
            $teacherNavLinks[] = [
                'label' => __('Settings'),
                'icon' => 'bi bi-gear',
                'url' => route('tenant.teacher.settings'),
                'active' => request()->routeIs('tenant.teacher.settings*'),
            ];
        }
        if (Route::has('tenant.teacher.profile.show')) {
            $teacherNavLinks[] = [
                'label' => __('Profile'),
                'icon' => 'bi bi-person-circle',
                'url' => route('tenant.teacher.profile.show'),
                'active' => request()->routeIs('tenant.teacher.profile.*'),
            ];
        }
    @endphp

    <div class="tenant-shell">
        <aside class="tenant-sidebar" id="teacherSidebar">
            <div class="sidebar-header px-3 py-4 d-flex align-items-center gap-3">
                @if (setting('school_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(setting('school_logo')) }}"
                        alt="{{ setting('school_name', optional($school)->name ?? config('app.name')) }}"
                        style="width: 44px; height: 44px; object-fit: contain;" onerror="this.style.display='none';">
                @else
                    <span
                        class="bg-primary rounded-circle text-white d-inline-flex align-items-center justify-content-center"
                        style="width: 44px; height: 44px;">
                        <i class="bi bi-mortarboard"></i>
                    </span>
                @endif
                <div>
                    <div class="fw-semibold">{{ setting('school_name', optional($school)->name ?? config('app.name')) }}
                    </div>
                    <small class="text-muted">{{ __('Teacher Workspace') }}</small>
                </div>
                <button class="btn btn-link ms-auto d-lg-none" type="button" data-close-sidebar>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="sidebar-user px-3 py-3 border-bottom d-flex align-items-center gap-2">
                @php($avatar = optional($teacherUser)->profile_photo_url)
                @if ($avatar)
                    <img src="{{ $avatar }}" alt="{{ $teacherUser->name ?? __('Teacher') }}"
                        class="avatar-circle border" style="object-fit: cover;">
                @else
                    <span
                        class="avatar-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-person-badge"></i>
                    </span>
                @endif
                <div>
                    <div class="fw-semibold">{{ $teacherUser->name ?? __('Teacher') }}</div>
                    <small class="text-muted">{{ __('Teacher Portal') }}</small>
                </div>
            </div>

            @if (!empty($teacherNavLinks))
                <nav class="py-4">
                    <ul class="list-unstyled mb-0">
                        @foreach ($teacherNavLinks as $link)
                            @if (isset($link['type']) && $link['type'] === 'section')
                                <li class="px-4 mt-3 mb-2">
                                    <small class="text-muted fw-bold text-uppercase"
                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        {{ $link['label'] }}
                                    </small>
                                </li>
                            @elseif (isset($link['type']) && $link['type'] === 'divider')
                                <li class="px-3 my-2">
                                    <hr class="m-0 opacity-25">
                                </li>
                            @else
                                <li class="px-3">
                                    <a href="{{ $link['url'] }}"
                                        class="nav-link d-flex align-items-center gap-3 px-3 py-2 mb-1 text-decoration-none {{ $link['active'] ? 'active' : '' }}">
                                        <i class="{{ $link['icon'] }}"></i>
                                        <span class="fw-medium flex-grow-1">{{ $link['label'] }}</span>
                                        @if (isset($link['badge']))
                                            <span class="badge bg-success badge-sm">{{ $link['badge'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            @endif
        </aside>

        <div class="tenant-main">
            <header class="teacher-topbar bg-white px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-toggle-sidebar>
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('M d, Y') }}
                    </span>
                    @if (Route::has('tenant.teacher.profile.show'))
                        <a href="{{ route('tenant.teacher.profile.show') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person"></i>
                            <span class="ms-1">{{ __('Profile') }}</span>
                        </a>
                    @endif
                    @php($tenantLogout = Route::has('tenant.logout') ? 'tenant.logout' : (Route::has('logout') ? 'logout' : null))
                    @if ($tenantLogout)
                        <form method="POST" action="{{ route($tenantLogout) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                <i class="bi bi-box-arrow-right me-1"></i>{{ __('Logout') }}
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            <main class="flex-grow-1 py-4">
                <div class="tenant-content mx-auto px-3 px-md-4">
                    @if (session('status'))
                        <div class="alert alert-success shadow-sm" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <footer class="border-top bg-white py-3">
                <div class="tenant-content mx-auto px-3 px-md-4 text-muted small d-flex justify-content-between">
                    <span>&copy; {{ now()->year }}
                        {{ setting('school_name', optional($school)->name ?? config('app.name')) }}</span>
                    <span>{{ __('Teacher Workspace') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('teacherSidebar');
            const toggleBtn = document.querySelector('[data-toggle-sidebar]');
            const closeBtn = document.querySelector('[data-close-sidebar]');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => sidebar.classList.add('open'));
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', () => sidebar.classList.remove('open'));
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
