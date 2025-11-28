<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Student Portal')) · {{ config('app.name', 'SMATCAMPUS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Quicksand', sans-serif;
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
            max-width: 1200px;
        }

        .sidebar-header {
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .sidebar-header .navbar-brand {
            font-weight: 600;
            color: #4f46e5;
        }

        .sidebar-user .avatar-circle {
            width: 44px;
            height: 44px;
            border-radius: 999px;
        }

        .tenant-sidebar .nav-link {
            color: #4b5563;
            font-weight: 500;
            border-radius: 0.65rem;
        }

        .tenant-sidebar .nav-link.active,
        .tenant-sidebar .nav-link:hover {
            background-color: #eef2ff;
            color: #312e81;
        }

        .student-topbar {
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
        $studentUser = auth()->user();

        $studentNavLinks = [];
        if (Route::has('tenant.student.classroom.index')) {
            $studentNavLinks[] = [
                'label' => __('Classroom'),
                'icon' => 'bi bi-easel2',
                'url' => route('tenant.student.classroom.index'),
                'active' => request()->routeIs('tenant.student.classroom.*'),
            ];
        }
        if (Route::has('tenant.student.classroom.exercises.index')) {
            $studentNavLinks[] = [
                'label' => __('Assignments'),
                'icon' => 'bi bi-card-checklist',
                'url' => route('tenant.student.classroom.exercises.index'),
                'active' => request()->routeIs('tenant.student.classroom.exercises.*'),
            ];
        }

        $studentNavLinks[] = [
            'label' => __('Quizzes'),
            'icon' => 'bi bi-puzzle',
            'url' => route('tenant.student.quizzes.index'),
            'active' => request()->routeIs('tenant.student.quizzes.*'),
        ];

        $studentNavLinks[] = [
            'label' => __('Exams'),
            'icon' => 'bi bi-journal-check',
            'url' => route('tenant.student.exams.index'),
            'active' => request()->routeIs('tenant.student.exams.*'),
        ];
        if (Route::has('tenant.student.classroom.materials.index')) {
            $studentNavLinks[] = [
                'label' => __('Materials'),
                'icon' => 'bi bi-folder2-open',
                'url' => route('tenant.student.classroom.materials.index'),
                'active' => request()->routeIs('tenant.student.classroom.materials.*'),
            ];
        }
        if (Route::has('tenant.finance.payments.pay')) {
            $studentNavLinks[] = [
                'label' => __('Pay Fees'),
                'icon' => 'bi bi-credit-card',
                'url' => route('tenant.finance.payments.pay'),
                'active' => request()->routeIs('tenant.finance.payments.pay'),
            ];
        }
        if (Route::has('tenant.student.academic')) {
            $studentNavLinks[] = [
                'label' => __('Academic Reports'),
                'icon' => 'bi bi-file-earmark-text',
                'url' => route('tenant.student.academic'),
                'active' => request()->routeIs('tenant.student.academic*'),
            ];
        }
        if (Route::has('tenant.student.notifications.index')) {
            $studentNavLinks[] = [
                'label' => __('Notifications'),
                'icon' => 'bi bi-bell',
                'url' => route('tenant.student.notifications.index'),
                'active' => request()->routeIs('tenant.student.notifications.*'),
            ];
        }
    @endphp

    @php($studentNavLinks = $studentNavLinks ?? [])

    <div class="tenant-shell">
        <aside class="tenant-sidebar" id="studentSidebar">
            <div class="sidebar-header px-3 py-4 d-flex align-items-center gap-3">
                @if (setting('school_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(setting('school_logo')) }}"
                        alt="{{ $school->name ?? config('app.name') }}"
                        style="width: 44px; height: 44px; object-fit: contain;" onerror="this.style.display='none';">
                @else
                    <span
                        class="bg-primary rounded-circle text-white d-inline-flex align-items-center justify-content-center"
                        style="width: 44px; height: 44px;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                @endif
                <div>
                    <div class="fw-semibold">{{ setting('school_name', $school->name ?? config('app.name')) }}</div>
                    <small class="text-muted">{{ __('Student Workspace') }}</small>
                </div>
                <button class="btn btn-link ms-auto d-lg-none" type="button" data-close-sidebar>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="sidebar-user px-3 py-3 border-bottom d-flex align-items-center gap-2">
                <span
                    class="avatar-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-person-circle"></i>
                </span>
                <div>
                    <div class="fw-semibold">{{ $studentUser->name ?? __('Student') }}</div>
                    <small class="text-muted">{{ __('Student Portal') }}</small>
                </div>
            </div>

            @if (!empty($studentNavLinks))
                <nav class="py-4">
                    <ul class="list-unstyled mb-0">
                        @foreach ($studentNavLinks as $link)
                            <li class="px-3">
                                <a href="{{ $link['url'] }}"
                                    class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none {{ $link['active'] ? 'bg-primary text-white' : 'text-body' }}">
                                    <i class="{{ $link['icon'] }}"></i>
                                    <span class="fw-medium">{{ $link['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                        <li class="px-3">
                            <a href="{{ route('tenant.student.academic') }}"
                                class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none {{ request()->routeIs('tenant.student.academic*') ? 'bg-primary text-white' : 'text-body' }}">
                                <i class="bi bi-file-earmark-text"></i>
                                <span class="fw-medium">{{ __('My Report') }}</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            @endif
        </aside>

        <div class="tenant-main">
            <header class="student-topbar bg-white px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-toggle-sidebar>
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('M d, Y') }}
                    </span>
                    @php($tenantLogout = Route::has('tenant.logout') ? 'tenant.logout' : (Route::has('logout') ? 'logout' : null))
                    @if ($tenantLogout)
                        <form method="POST" action="{{ route($tenantLogout) }}">
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
                    <span>&copy; {{ now()->year }} {{ $school->name ?? config('app.name') }}</span>
                    <span>{{ __('Student Workspace') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('studentSidebar');
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
