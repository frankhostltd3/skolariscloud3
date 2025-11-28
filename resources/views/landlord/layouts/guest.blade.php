<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} · {{ __('Landlord Sign In') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --guest-bg: radial-gradient(circle at top, rgba(13, 110, 253, 0.25), transparent 55%),
                radial-gradient(circle at 20% 80%, rgba(102, 16, 242, 0.2), transparent 50%),
                #0b1526;
        }

        * {
            font-family: 'Quicksand', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: var(--guest-bg);
            min-height: 100vh;
            margin: 0;
            position: relative;
            color: #1f2a37;
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .glass-card {
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 55px rgba(15, 23, 42, 0.25);
            background: rgba(255, 255, 255, 0.97);
        }

        .auth-brand {
            color: #0d6efd;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .auth-footer {
            color: rgba(255, 255, 255, 0.8);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-xl-4">
                    <div class="text-center text-white mb-4">
                        <div class="auth-brand text-uppercase small">{{ __('Skolaris Cloud Landlord') }}</div>
                        <h1 class="text-white-50 h6 mb-0">{{ __('Central control for every tenant') }}</h1>
                    </div>

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
</body>

</html>
