<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} · {{ __('Landlord Sign In') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --guest-gradient: radial-gradient(circle at top, rgba(13, 110, 253, 0.4), transparent 52%),
                radial-gradient(circle at 15% 85%, rgba(32, 201, 151, 0.25), transparent 45%),
                #050910;
        }

        body {
            font-family: 'Quicksand', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: var(--guest-gradient);
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 3rem 1rem;
        }

        .hero-copy {
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 1rem;
            border-radius: 999px;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: rgba(13, 110, 253, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .glass-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(12px);
        }

        .auth-footer {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-shell">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-center g-5">
                <div class="col-xl-5 col-lg-6 hero-copy text-center text-lg-start">
                    <span class="hero-pill text-white-50 mb-3">
                        <i class="bi bi-buildings"></i>
                        {{ __('Skolaris Landlord Suite') }}
                    </span>
                    <h1 class="display-6 fw-semibold text-white mb-3">
                        {{ __('Command every tenant from a single flight deck.') }}
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        {{ __('Provision schools, monitor billings, and keep infrastructure in lockstep across your education network.') }}
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <div class="d-flex align-items-center gap-3 text-white-50">
                            <div class="rounded-circle bg-white bg-opacity-10 p-3">
                                <i class="bi bi-shield-lock text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-white">{{ __('Zero-trust perimeter') }}</div>
                                <small>{{ __('Tenant-aware SSO + 2FA ready') }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-white-50">
                            <div class="rounded-circle bg-white bg-opacity-10 p-3">
                                <i class="bi bi-graph-up text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-white">{{ __('Live telemetry') }}</div>
                                <small>{{ __('MRR, usage, incident cues') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5 col-md-7">
                    <div class="card glass-card border-0">
                        <div class="card-body p-4 p-lg-5">
                            @yield('content')
                        </div>
                    </div>
                    <p class="text-center small auth-footer mt-4">
                        &copy; {{ now()->year }} {{ config('app.name') }} ·
                        {{ __('School network operations without borders') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    @stack('scripts')
</body>

</html>
