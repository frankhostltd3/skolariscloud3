<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} · {{ __('Landlord Console') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom Styles for Landlord Panel -->
    <style>
        .bg-body-tertiary {
            background-color: #f8f9fa !important;
        }

        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-body-tertiary text-body">
    @php
        $landlord = auth('landlord')->user();
    @endphp

    <header class="border-bottom bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg py-3">
            <div class="container-xxl">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold"
                    href="{{ route('landlord.dashboard') }}">
                    <span class="bi bi-building-check"></span>
                    <span>{{ config('app.name') }}</span>
                </a>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <div class="text-end small text-secondary d-none d-md-block">
                        <div class="fw-semibold text-body">{{ $landlord?->name }}</div>
                        <div>{{ __('Platform Admin') }}</div>
                    </div>
                    <form action="{{ route('landlord.logout', absolute: false) }}" method="post" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <span class="bi bi-box-arrow-right me-1"></span>{{ __('Log out') }}
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-4 py-lg-5">
        <div class="container-xxl">
            @php
                if (!isset($navigation)) {
                    $navigation = [
                        ['label' => __('Overview'), 'icon' => 'bi-speedometer2', 'route' => 'landlord.dashboard'],
                        ['label' => __('Profile'), 'icon' => 'bi-person-circle', 'route' => 'landlord.profile'],
                        ['label' => __('Tenant directory'), 'icon' => 'bi-people', 'route' => 'landlord.tenants.index'],
                        [
                            'label' => __('Create tenant'),
                            'icon' => 'bi-building-add',
                            'route' => 'landlord.tenants.create',
                        ],
                        ['label' => __('Import tenants'), 'icon' => 'bi-upload', 'route' => 'landlord.tenants.import'],
                        ['label' => __('Domains'), 'icon' => 'bi-globe2', 'route' => 'landlord.tenants.domains'],
                        ['label' => __('Billing & plans'), 'icon' => 'bi-credit-card', 'route' => 'landlord.billing'],
                        [
                            'label' => __('Pricing catalogue'),
                            'icon' => 'bi-tags',
                            'route' => 'landlord.billing.plans.index',
                            'pattern' => 'landlord.billing.plans.*',
                        ],
                        ['label' => __('Invoices'), 'icon' => 'bi-receipt', 'route' => 'landlord.billing.invoices'],
                        [
                            'label' => __('Payment methods'),
                            'icon' => 'bi-wallet2',
                            'route' => 'landlord.billing.payment-methods',
                        ],
                        [
                            'label' => __('Dunning'),
                            'icon' => 'bi-exclamation-triangle',
                            'route' => 'landlord.billing.dunning',
                        ],
                        ['label' => __('Analytics'), 'icon' => 'bi-graph-up', 'route' => 'landlord.analytics'],
                        ['label' => __('Users'), 'icon' => 'bi-people-fill', 'route' => 'landlord.users'],
                        ['label' => __('Audit logs'), 'icon' => 'bi-clipboard-data', 'route' => 'landlord.audit'],
                        [
                            'label' => __('Notifications'),
                            'icon' => 'bi-bell',
                            'route' => 'landlord.notifications.index',
                        ],
                        ['label' => __('Integrations'), 'icon' => 'bi-plug', 'route' => 'landlord.integrations'],
                        ['label' => __('System health'), 'icon' => 'bi-activity', 'route' => 'landlord.health'],
                        ['label' => __('Roles & permissions'), 'icon' => 'bi-shield-lock', 'route' => 'landlord.rbac'],
                        ['label' => __('Settings'), 'icon' => 'bi-gear', 'route' => 'landlord.settings'],
                    ];
                }
            @endphp

            <div class="row g-4">
                <div class="col-12 col-lg-3 col-xl-2">
                    <aside class="card border-0 shadow-sm h-100">
                        <div class="card-body p-0">
                            <nav class="nav flex-column">
                                @foreach ($navigation as $item)
                                    @if (!empty($item['children']))
                                        @php
                                            $groupId = 'nav-group-' . \Illuminate\Support\Str::slug($item['label']);
                                            $childActive = collect($item['children'])->contains(function ($child) {
                                                $pattern = $child['pattern'] ?? $child['route'];
                                                return request()->routeIs($pattern);
                                            });
                                        @endphp
                                        <button
                                            class="btn text-start d-flex align-items-center gap-2 px-3 py-2 w-100 @if ($childActive) text-primary fw-semibold @else text-secondary @endif"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#{{ $groupId }}"
                                            aria-expanded="{{ $childActive ? 'true' : 'false' }}"
                                            aria-controls="{{ $groupId }}">
                                            <span class="bi {{ $item['icon'] }} nav-icon"></span>
                                            <span>{{ $item['label'] }}</span>
                                            <span
                                                class="ms-auto bi @if ($childActive) bi-chevron-up @else bi-chevron-down @endif"></span>
                                        </button>
                                        <div class="collapse @if ($childActive) show @endif"
                                            id="{{ $groupId }}">
                                            <div class="d-flex flex-column ms-4 border-start ps-2">
                                                @foreach ($item['children'] as $child)
                                                    @php
                                                        $pattern = $child['pattern'] ?? $child['route'];
                                                        $isActive = request()->routeIs($pattern);
                                                    @endphp
                                                    <a href="{{ route($child['route']) }}"
                                                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 @if ($isActive) active text-primary fw-semibold @else text-secondary @endif">
                                                        <span class="bi {{ $child['icon'] }} nav-icon"></span>
                                                        <span>{{ $child['label'] }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $pattern = $item['pattern'] ?? $item['route'];
                                            $isActive = request()->routeIs($pattern);
                                        @endphp
                                        <a href="{{ route($item['route']) }}"
                                            class="nav-link d-flex align-items-center gap-2 px-3 py-2 @if ($isActive) active text-primary fw-semibold @else text-secondary @endif">
                                            <span class="bi {{ $item['icon'] }} nav-icon"></span>
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </nav>
                        </div>
                    </aside>
                </div>

                <div class="col-12 col-lg-9 col-xl-10">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 bg-body-secondary text-secondary small">
        @php
            $marketingUrl = Route::has('landing') ? route('landing', ['locale' => app()->getLocale()]) : url('/');
        @endphp
        <div class="container-xxl d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <span>&copy; {{ now()->year }} {{ config('app.name') }}.
                {{ __('Multi-tenant orchestration for schools.') }}</span>
            <div class="d-flex align-items-center gap-3">
                <a class="link-secondary text-decoration-none"
                    href="{{ $marketingUrl }}">{{ __('Marketing site') }}</a>
                <a class="link-secondary text-decoration-none"
                    href="mailto:support@skolariscloud.com">{{ __('Support') }}</a>
            </div>
        </div>
    </footer>

    {{-- Scripts pushed by child views (e.g., Payment Methods modal JS) --}}
    @stack('scripts')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
