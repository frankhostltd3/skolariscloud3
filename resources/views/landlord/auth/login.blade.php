@extends('landlord.layouts.guest')

@push('styles')
    <style>
        .auth-pill {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.15), rgba(102, 16, 242, 0.15));
            color: #0d6efd;
            border-radius: 999px;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .auth-highlight {
            border-radius: 0.9rem;
            background: #f8faff;
            border: 1px solid #edf1fb;
        }
    </style>
@endpush

@section('content')
    <div class="text-center mb-4">
        <span class="auth-pill px-4 py-2 small d-inline-flex align-items-center justify-content-center mb-3">
            <i class="bi bi-buildings me-2"></i>{{ __('Landlord access') }}
        </span>
        <h1 class="h3 fw-semibold mb-2">{{ __('Welcome back, Skolaris landlord') }}</h1>
        <p class="text-secondary mb-0">
            {{ __('Sign in to orchestrate tenants, billing, and growth across your school network.') }}</p>
    </div>

    <form action="{{ route('landlord.login.store', absolute: false) }}" method="post" class="d-grid gap-3 text-start">
        @csrf

        <div>
            <label for="email" class="form-label fw-semibold">{{ __('Email address') }}</label>
            <input type="email" id="email" name="email"
                class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}"
                required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label fw-semibold d-flex justify-content-between align-items-center">
                <span>{{ __('Password') }}</span>
                <a href="mailto:support@skolariscloud.com"
                    class="link-primary small">{{ __('Forgot? Contact support') }}</a>
            </label>
            <input type="password" id="password" name="password"
                class="form-control form-control-lg @error('password') is-invalid @enderror" required
                autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember"
                {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                {{ __('Keep me signed in on this device') }}
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">
            <span class="bi bi-arrow-right-circle me-2"></span>{{ __('Sign in to dashboard') }}
        </button>
    </form>

    <div class="mt-4 text-center small text-secondary">
        {{ __('Need a landlord account?') }}
        <a href="mailto:hello@skolariscloud.com" class="link-primary">{{ __('Talk to the platform team') }}</a>
    </div>

    <div class="auth-highlight mt-4 p-3">
        <div class="row g-3 text-muted small">
            <div class="col-6 d-flex align-items-center gap-2">
                <span class="badge text-bg-primary-subtle text-primary-emphasis rounded-circle p-2">
                    <i class="bi bi-shield-lock"></i>
                </span>
                <div>
                    <strong class="d-block text-dark">{{ __('Secure SSO') }}</strong>
                    {{ __('2FA-ready control tower') }}
                </div>
            </div>
            <div class="col-6 d-flex align-items-center gap-2">
                <span class="badge text-bg-success-subtle text-success-emphasis rounded-circle p-2">
                    <i class="bi bi-graph-up"></i>
                </span>
                <div>
                    <strong class="d-block text-dark">{{ __('Live analytics') }}</strong>
                    {{ __('Revenue + tenancy KPIs') }}
                </div>
            </div>
        </div>
    </div>
@endsection
