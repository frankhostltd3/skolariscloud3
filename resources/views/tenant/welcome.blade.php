<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $school->name }} - Workspace</title>

    <!-- Quicksand Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            font-family: 'Quicksand', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .hero-section {
            padding: 6rem 0 4rem;
            background: transparent;
        }

        .school-logo {
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 400;
            margin-bottom: 1rem;
        }

        .workspace-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }

        .hero-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 700px;
            margin: 0 auto 3rem;
            line-height: 1.8;
        }

        .action-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-workspace {
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-workspace-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-workspace-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
            color: #764ba2;
        }

        .btn-workspace-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid white;
            backdrop-filter: blur(10px);
        }

        .btn-workspace-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }

        .features-section {
            padding: 5rem 0;
            background: white;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
        }

        .feature-icon.icon-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .feature-icon.icon-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .feature-icon.icon-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .feature-icon.icon-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .feature-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
            text-align: center;
        }

        .feature-text {
            font-size: 1rem;
            color: #6b7280;
            line-height: 1.7;
            text-align: center;
        }

        .info-section {
            padding: 5rem 0;
            background: #f9fafb;
        }

        .info-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .info-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .info-item {
            display: flex;
            align-items: start;
            padding: 1.25rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #f9fafb;
            transform: translateX(5px);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            margin-right: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .info-content h6 {
            font-size: 1rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .info-content p {
            font-size: 1rem;
            color: #6b7280;
            margin: 0;
        }

        .info-content a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .info-content a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        footer {
            padding: 2.5rem 0;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: rgba(255, 255, 255, 0.8);
        }

        footer p {
            margin: 0;
            font-size: 0.95rem;
        }

        footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .btn-workspace {
                padding: 0.875rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto text-center">
                    @if ($school->logo_url)
                        <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" class="school-logo">
                    @endif

                    <div class="workspace-badge">
                        <i class="bi bi-briefcase me-2"></i>Your Workspace
                    </div>

                    <h1 class="hero-title">{{ $school->name }}</h1>

                    @if ($school->motto)
                        <p class="hero-subtitle">"{{ $school->motto }}"</p>
                    @endif

                    <p class="hero-description">
                        Welcome to your digital workspace. Manage academics, track attendance, communicate with your
                        team, and drive educational excellence—all in one powerful platform.
                    </p>

                    <div class="action-buttons">
                        @guest
                            <a href="{{ route('tenant.login') }}" class="btn btn-workspace btn-workspace-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Access Workspace
                            </a>
                            <a href="{{ route('tenant.register') }}" class="btn btn-workspace btn-workspace-secondary">
                                <i class="bi bi-person-plus me-2"></i>Join Workspace
                            </a>
                        @else
                            <a href="{{ route('tenant.dashboard') }}" class="btn btn-workspace btn-workspace-primary">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Workspace Capabilities</h2>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon icon-primary">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <h3 class="feature-title">Academic Management</h3>
                        <p class="feature-text">Complete curriculum planning, class scheduling, and student performance
                            tracking in one centralized system.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon icon-success">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3 class="feature-title">Smart Attendance</h3>
                        <p class="feature-text">Multi-method attendance tracking with real-time reporting, analytics,
                            and automated notifications.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon icon-info">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="feature-title">Performance Analytics</h3>
                        <p class="feature-text">Comprehensive grade management with visual analytics, progress tracking,
                            and detailed reporting.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon icon-warning">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="feature-title">Team Collaboration</h3>
                        <p class="feature-text">Seamless communication between teachers, students, parents, and
                            administrators.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="info-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="info-card">
                        <h2 class="info-title">Workspace Information</h2>
                        <div class="row g-3">
                            @if ($school->email)
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="bi bi-envelope-fill"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6>Email Address</h6>
                                            <p>{{ $school->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($school->phone)
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="bi bi-telephone-fill"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6>Phone Number</h6>
                                            <p>{{ $school->phone }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($school->address)
                                <div class="col-12">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6>Physical Address</h6>
                                            <p>{{ $school->address }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($school->website)
                                <div class="col-12">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="bi bi-globe"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6>Website</h6>
                                            <p><a href="{{ $school->website }}"
                                                    target="_blank">{{ $school->website }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p>&copy; {{ date('Y') }} {{ $school->name }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p>Powered by <a href="#">Skolaris Cloud</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
