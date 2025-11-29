<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Parent Portal')) · {{ config('app.name', 'SMATCAMPUS') }}</title>
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
        $user = auth()->user();
        $parentNavLinks = [
            [
                'label' => __('Dashboard'),
                'icon' => 'bi bi-speedometer2',
                'url' => route('tenant.parent.dashboard'),
                'active' => request()->routeIs('tenant.parent.dashboard'),
            ],
            [
                'label' => __('Performance'),
                'icon' => 'bi bi-graph-up',
                'url' => route('tenant.parent.performance.index'),
                'active' => request()->routeIs('tenant.parent.performance.*'),
            ],
            [
                'label' => __('Attendance'),
                'icon' => 'bi bi-calendar-check',
                'url' => route('tenant.parent.attendance.index'),
                'active' => request()->routeIs('tenant.parent.attendance.*'),
            ],
            [
                'label' => __('Fees & Payments'),
                'icon' => 'bi bi-wallet2',
                'url' => route('tenant.parent.fees.index'),
                'active' => request()->routeIs('tenant.parent.fees.*'),
            ],
            [
                'label' => __('Behaviour'),
                'icon' => 'bi bi-emoji-smile',
                'url' => route('tenant.parent.behaviour.index'),
                'active' => request()->routeIs('tenant.parent.behaviour.*'),
            ],
            [
                'label' => __('Announcements'),
                'icon' => 'bi bi-megaphone',
                'url' => route('tenant.parent.announcements.index'),
                'active' => request()->routeIs('tenant.parent.announcements.*'),
            ],
            [
                'label' => __('Meetings'),
                'icon' => 'bi bi-people',
                'url' => route('tenant.parent.meetings.index'),
                'active' => request()->routeIs('tenant.parent.meetings.*'),
            ],
        ];
    @endphp

    <div class="tenant-shell">
        <aside class="tenant-sidebar" id="parentSidebar">
            <div class="sidebar-header px-3 py-4 d-flex align-items-center gap-3">
                @if (setting('school_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(setting('school_logo')) }}"
                        alt="{{ $school->name ?? config('app.name') }}"
                        style="width: 44px; height: 44px; object-fit: contain;" onerror="this.style.display='none';">
                @else
                    <span
                        class="bg-primary rounded-circle text-white d-inline-flex align-items-center justify-content-center"
                        style="width: 44px; height: 44px;"><i class="bi bi-mortarboard-fill"></i></span>
                @endif
                <div>
                    <div class="fw-semibold">{{ setting('school_name', $school->name ?? config('app.name')) }}</div>
                    <small class="text-muted">{{ __('Parent Portal') }}</small>
                </div>
                <button class="btn btn-link ms-auto d-lg-none" type="button" data-close-sidebar><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <div class="sidebar-user px-3 py-3 border-bottom d-flex align-items-center gap-2">
                <span
                    class="avatar-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center"><i
                        class="bi bi-person-circle"></i></span>
                <div>
                    <div class="fw-semibold">{{ $user->name ?? __('Parent') }}</div>
                    <small class="text-muted">{{ __('Guardian') }}</small>
                </div>
            </div>
            <nav class="py-4">
                <ul class="list-unstyled mb-0">
                    @foreach ($parentNavLinks as $link)
                        <li class="px-3">
                            <a href="{{ $link['url'] }}"
                                class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none {{ $link['active'] ? 'bg-primary text-white' : 'text-body' }}">
                                <i class="{{ $link['icon'] }}"></i>
                                <span class="fw-medium">{{ $link['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>
        <div class="tenant-main">
            <header class="student-topbar bg-white px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-toggle-sidebar><i
                        class="bi bi-list"></i></button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="text-muted small"><i
                            class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('M d, Y') }}</span>
                    <form method="POST" action="{{ route('tenant.logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary" type="submit"><i
                                class="bi bi-box-arrow-right me-1"></i>{{ __('Logout') }}</button>
                    </form>
                </div>
            </header>
            <main class="flex-grow-1 py-4">
                <div class="tenant-content mx-auto px-3 px-md-4">
                    @if (session('status'))
                        <div class="alert alert-success shadow-sm" role="alert">{{ session('status') }}</div>
                    @endif
                    @yield('content')
                </div>
            </main>
            <footer class="border-top bg-white py-3">
                <div class="tenant-content mx-auto px-3 px-md-4 text-muted small d-flex justify-content-between">
                    <span>&copy; {{ now()->year }} {{ $school->name ?? config('app.name') }}</span>
                    <span>{{ __('Parent Portal') }}</span>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('parentSidebar');
            const toggleBtn = document.querySelector('[data-toggle-sidebar]');
            const closeBtn = document.querySelector('[data-close-sidebar]');
            if (toggleBtn) toggleBtn.addEventListener('click', () => sidebar.classList.add('open'));
            if (closeBtn) closeBtn.addEventListener('click', () => sidebar.classList.remove('open'));
        });
    </script>
</body>

</html>
