<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? (isset($school) ? $school->name : config('app.name')) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            font-family: 'Quicksand', sans-serif;
        }

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
        }

        .tenant-main {
            background-color: #f9fafb;
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .tenant-content {
            max-width: 1200px;
        }

        .tenant-sidebar .list-group-item {
            font-weight: 400;
        }

        .tenant-sidebar .card-header {
            font-weight: 500;
        }

        .tenant-sidebar .fw-semibold {
            font-weight: 500;
        }

        .tenant-sidebar .fw-medium {
            font-weight: 400;
        }
    </style>

    @stack('styles')
</head>

<body>
    @php($school = $school ?? request()->attributes->get('currentSchool'))
    @php($authUser = $authUser ?? auth()->user())

    <div class="tenant-shell">
        <aside class="tenant-sidebar">
            @include('tenant.layouts.partials.sidebar', ['user' => $authUser, 'school' => $school])
        </aside>

        <div class="d-flex flex-column tenant-main">
            @include('tenant.layouts.partials.topbar', ['user' => $authUser, 'school' => $school])

            <main class="flex-grow-1 py-4">
                <div class="tenant-content mx-auto px-3 px-md-4">
                    @yield('content')
                </div>
            </main>

            <footer class="border-top bg-white py-3">
                <div class="tenant-content mx-auto px-3 px-md-4 text-muted small d-flex justify-content-between">
                    <span>&copy; {{ now()->year }} {{ $school->name ?? config('app.name') }}</span>
                    <span>SMATCAMPUS Workspace</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
