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
            background-color: #f3f4f6;
        }

        .tenant-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .tenant-navbar .navbar-brand {
            font-weight: 600;
            color: #0f172a;
        }

        .tenant-navbar .nav-link {
            color: #475569;
            font-weight: 500;
        }

        .tenant-navbar .nav-link.active,
        .tenant-navbar .nav-link:hover {
            color: #111827;
        }

        .tenant-wrapper {
            min-height: calc(100vh - 64px);
        }
    </style>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-md tenant-navbar shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-easel2-fill me-2"></i>{{ config('app.name', 'SMATCAMPUS') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="teacherNav">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    @if (Route::has('tenant.teacher.classroom.virtual.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tenant.teacher.classroom.*') ? 'active' : '' }}"
                                href="{{ route('tenant.teacher.classroom.virtual.index') }}">
                                <i class="bi bi-door-open me-1"></i>{{ __('Classroom') }}
                            </a>
                        </li>
                    @endif
                    @if (Route::has('tenant.teacher.attendance.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tenant.teacher.attendance.*') ? 'active' : '' }}"
                                href="{{ route('tenant.teacher.attendance.index') }}">
                                <i class="bi bi-clipboard-check me-1"></i>{{ __('Attendance') }}
                            </a>
                        </li>
                    @endif
                    @if (Route::has('tenant.reports.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tenant.reports.*') ? 'active' : '' }}"
                                href="{{ route('tenant.reports.index') }}">
                                <i class="bi bi-file-earmark-text me-1"></i>{{ __('Reports') }}
                            </a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    @php($teacherUser = auth()->user())
                    @if ($teacherUser)
                        <li class="nav-item me-3">
                            <span class="nav-link">
                                <i class="bi bi-person-badge me-1"></i>{{ $teacherUser->name ?? __('Teacher') }}
                            </span>
                        </li>
                    @endif
                    @php($tenantLogout = Route::has('tenant.logout') ? 'tenant.logout' : (Route::has('logout') ? 'logout' : null))
                    @if ($tenantLogout)
                        <li class="nav-item">
                            <form method="POST" action="{{ route($tenantLogout) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                    <i class="bi bi-box-arrow-right me-1"></i>{{ __('Logout') }}
                                </button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="tenant-wrapper">
        <div class="container-fluid py-4">
            @if (session('status'))
                <div class="alert alert-success shadow-sm" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer class="py-3 text-center text-muted small">
        &copy; {{ date('Y') }} {{ config('app.name', 'SMATCAMPUS') }} · {{ __('Teacher Portal') }}
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
