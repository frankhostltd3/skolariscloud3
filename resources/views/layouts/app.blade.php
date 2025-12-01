<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SMATCAMPUS') }} - Smart School Management System</title>

    <!-- Google Fonts - Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #06b6d4;
            --accent-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }

        * {
            font-family: 'Quicksand', sans-serif;
        }

        body {
            overflow-x: hidden;
            background-color: var(--light-color);
        }

        .hero-section {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            min-height: 80vh;
        }

        .hero-section .badge {
            letter-spacing: 0.04em;
            border-radius: 999px;
            padding: 0.5rem 1.25rem;
        }

        .hero-section .display-4 {
            line-height: 1.15;
        }

        .hero-visual {
            position: relative;
        }

        .hero-visual .info-chip {
            border-radius: 0.85rem;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.15);
            min-width: 180px;
        }

        .hero-visual .info-chip strong {
            font-size: 0.95rem;
        }

        .hero-visual .info-chip small {
            font-size: 0.8rem;
        }

        .dashboard-mockup {
            max-width: 500px;
            margin: 0 auto;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-mockup .card {
            transition: transform 0.2s ease;
        }

        .dashboard-mockup .card:hover {
            transform: translateY(-2px);
        }

        /* Global Card Styles */
        .card {
            border-radius: 6px !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Global Border Radius */
        .rounded,
        .rounded-1,
        .rounded-2,
        .rounded-3 {
            border-radius: 6px !important;
        }

        .badge {
            border-radius: 6px !important;
        }

        .btn {
            border-radius: 6px !important;
        }

        .stats-card {
            background: white;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .card {
            border-radius: 1.25rem;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12);
        }

        .icon-circle,
        .avatar-circle {
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .icon-circle i {
            font-size: 1.5rem;
        }

        .stats-block h2 {
            letter-spacing: -0.5px;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
        }

        footer a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: white;
        }

        footer h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) {
            .hero-section .display-4 {
                font-size: 2.25rem;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .btn-primary,
            .btn-outline-primary {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .hero-visual {
                max-width: 420px;
            }

            .hero-visual .info-chip {
                min-width: 150px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-mortarboard-fill"></i> SMATCAMPUS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#faq">FAQ</a>
                    </li>
                    @if (function_exists('tenant') &&
                            tenant() &&
                            function_exists('setting') &&
                            setting('bookstore_enabled') &&
                            Route::has('tenant.bookstore.index'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tenant.bookstore.index') }}">Bookstore</a>
                        </li>
                    @endif
                    @guest
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-primary" href="{{ url('/register') }}">Register Your School</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-primary" href="{{ url('/login') }}">Login</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary" href="#contact">Contact Sales</a>
                        </li>
                    @else
                        @php($navUser = auth()->user())
                        @if ($navUser && $navUser->hasUserType(\App\Enums\UserType::ADMIN))
                            @if (Route::has('settings.index'))
                                <li class="nav-item ms-lg-3">
                                    <a class="btn btn-outline-primary" href="{{ route('settings.index') }}">Settings</a>
                                </li>
                            @elseif(Route::has('tenant.settings.admin.general'))
                                <li class="nav-item ms-lg-3">
                                    <a class="btn btn-outline-primary"
                                        href="{{ route('tenant.settings.admin.general') }}">Settings</a>
                                </li>
                            @endif

                            @if (Route::has('settings.payments.edit'))
                                <li class="nav-item ms-2">
                                    <a class="btn btn-outline-primary" href="{{ route('settings.payments.edit') }}">Payment
                                        Settings</a>
                                </li>
                            @elseif(Route::has('tenant.settings.admin.finance'))
                                <li class="nav-item ms-2">
                                    <a class="btn btn-outline-primary"
                                        href="{{ route('tenant.settings.admin.finance') }}">Payment
                                        Settings</a>
                                </li>
                            @endif

                            <li class="nav-item ms-2">
                                <a class="btn btn-outline-primary"
                                    href="{{ Route::has('dashboard') ? route('dashboard') : (Route::has('tenant.dashboard') ? route('tenant.dashboard') : url('/dashboard')) }}">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item ms-lg-3">
                                <a class="btn btn-outline-primary"
                                    href="{{ Route::has('dashboard') ? route('dashboard') : (Route::has('tenant.dashboard') ? route('tenant.dashboard') : url('/dashboard')) }}">Dashboard</a>
                            </li>
                        @endif
                        @php($logoutRouteName = Route::has('tenant.logout') ? 'tenant.logout' : (Route::has('logout') ? 'logout' : null))
                        @if ($logoutRouteName)
                            <li class="nav-item ms-2">
                                <form method="POST" action="{{ route($logoutRouteName) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        {{ __('Logout') }}
                                    </button>
                                </form>
                            </li>
                        @endif
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if (session('status') || session('workspace_url'))
            <div class="container pt-4">
                @if (session('status'))
                    <div class="alert alert-success shadow-sm" role="alert">
                        {{ session('status') }}
                        @if (session('workspace_url'))
                            <div class="mt-2 mb-0">
                                Access your workspace at
                                <a href="{{ session('workspace_url') }}" class="fw-semibold" target="_blank"
                                    rel="noopener">
                                    {{ session('workspace_url') }}
                                </a>.
                            </div>
                        @endif
                    </div>
                @elseif (session('workspace_url'))
                    <div class="alert alert-info shadow-sm" role="alert">
                        Access your workspace at
                        <a href="{{ session('workspace_url') }}" class="fw-semibold" target="_blank" rel="noopener">
                            {{ session('workspace_url') }}
                        </a>.
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="bi bi-mortarboard-fill"></i> SMATCAMPUS</h5>
                    <p class="text-muted">Empowering schools with intelligent management solutions. Transform your
                        institution with our comprehensive platform.</p>
                    <div class="mt-3">
                        <a href="#" class="me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Product</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features">Features</a></li>
                        <li class="mb-2"><a href="#pricing">Pricing</a></li>
                        <li class="mb-2"><a href="#">Demo</a></li>
                        <li class="mb-2"><a href="#">Updates</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Company</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">About Us</a></li>
                        <li class="mb-2"><a href="#">Careers</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Help Center</a></li>
                        <li class="mb-2"><a href="#">Documentation</a></li>
                        <li class="mb-2"><a href="#">API Reference</a></li>
                        <li class="mb-2"><a href="#">Community</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#">Terms of Service</a></li>
                        <li class="mb-2"><a href="#">Cookie Policy</a></li>
                        <li class="mb-2"><a href="#">GDPR</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} SMATCAMPUS. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="text-muted mb-0">Made with <i class="bi bi-heart-fill text-danger"></i> for Education
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
