@extends('tenant.layouts.guest')

@section('content')
    <div class="modern-auth-card">
        <div class="auth-card-header">
            <div class="auth-badge">
                <i class="bi bi-box-arrow-in-right me-2"></i>Welcome Back
            </div>
            <h1 class="auth-title">Sign In to Workspace</h1>
            <p class="auth-subtitle">Access your personalized workspace dashboard</p>
        </div>

        <div class="auth-card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.login', absolute: false) }}" class="auth-form">
                @csrf

                <div class="form-floating mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        placeholder="Email Address">
                    <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required autocomplete="current-password" placeholder="Password">
                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('tenant.forgot-password') }}" class="text-decoration-none fw-semibold forgot-link">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="btn btn-workspace-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>

                <div class="divider">
                    <span>New to the workspace?</span>
                </div>

                <a href="{{ route('tenant.register') }}" class="btn btn-workspace-secondary w-100">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </a>
            </form>
        </div>
    </div>

    <style>
        .modern-auth-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 480px;
            width: 100%;
        }

        .auth-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 3rem 2.5rem 2.5rem;
            text-align: center;
            color: white;
        }

        .auth-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
        }

        .auth-card-body {
            padding: 2.5rem;
        }

        .auth-form .form-floating>.form-control {
            height: 58px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .auth-form .form-floating>.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .auth-form .form-floating>label {
            padding: 1rem 1rem;
            color: #6b7280;
            font-weight: 500;
        }

        .btn-workspace-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-workspace-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-workspace-secondary {
            background: white;
            border: 2px solid #e5e7eb;
            padding: 1rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            transition: all 0.3s ease;
        }

        .btn-workspace-secondary:hover {
            border-color: #667eea;
            color: #667eea;
            background: #f9fafb;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            position: relative;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: #374151;
            font-size: 0.9rem;
        }

        .forgot-link {
            color: #667eea;
            font-size: 0.9rem;
        }

        .forgot-link:hover {
            color: #764ba2;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
        }
    </style>
@endsection
