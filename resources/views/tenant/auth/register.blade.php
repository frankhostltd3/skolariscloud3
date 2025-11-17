@extends('tenant.layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="modern-auth-card">
        <div class="auth-card-header">
            <div class="auth-badge">
                <i class="bi bi-person-plus-fill me-2"></i>Join Workspace
            </div>
            <h1 class="auth-title">Create Your Account</h1>
            <p class="auth-subtitle">Get started with your professional workspace</p>
        </div>

        <div class="auth-card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.register') }}" class="auth-form">
                @csrf

                <div class="form-floating mb-3">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required autofocus placeholder="Full Name">
                    <label for="name"><i class="bi bi-person me-2"></i>Full Name</label>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" required placeholder="Email Address">
                    <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">Choose your role...</option>
                        <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff / Teacher</option>
                        <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                        <option value="Parent" {{ old('role') == 'Parent' ? 'selected' : '' }}>Parent / Guardian</option>
                    </select>
                    <label for="role"><i class="bi bi-briefcase me-2"></i>I am registering as</label>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required placeholder="Password">
                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    <small class="form-text text-muted d-block mt-2 ms-2">Minimum 8 characters required</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                        id="password_confirmation" name="password_confirmation" required placeholder="Confirm Password">
                    <label for="password_confirmation"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" class="text-decoration-none fw-semibold">Terms of Service</a> and
                        <a href="#" class="text-decoration-none fw-semibold">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-workspace-primary w-100 mb-3">
                    <i class="bi bi-person-check me-2"></i>Create Account
                </button>

                <div class="divider">
                    <span>Already have an account?</span>
                </div>

                <a href="{{ route('tenant.login') }}" class="btn btn-workspace-secondary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In Instead
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
            max-width: 520px;
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

        .auth-form .form-floating>.form-control,
        .auth-form .form-floating>.form-select {
            height: 58px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .auth-form .form-floating>.form-control:focus,
        .auth-form .form-floating>.form-select:focus {
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

        .form-check-label a {
            color: #667eea;
        }

        .form-check-label a:hover {
            color: #764ba2;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
        }
    </style>
@endsection
