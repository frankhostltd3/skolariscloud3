@extends('tenant.layouts.guest')

@section('title', 'Admin Registration')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <!-- Logo/Header -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Admin Registration</h2>
            <p class="text-muted">Create your administrator account</p>
        </div>

        <!-- Security Notice -->
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-shield-lock"></i>
            <strong>Security Notice:</strong> Admin accounts have full access to the system.
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.admin.register') }}">
            @csrf

            <!-- Hidden token if provided in URL -->
            @if(request('token'))
                <input type="hidden" name="token" value="{{ request('token') }}">
            @endif

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus
                       placeholder="Enter your full name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required
                       placeholder="admin@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       required
                       placeholder="Create a strong password">
                <small class="form-text text-muted d-block mt-1">Minimum 8 characters</small>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" 
                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       placeholder="Re-enter your password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    Create Admin Account
                </button>
            </div>
        </form>

        <!-- Login Link -->
        <div class="text-center mt-3 pt-3 border-top">
            <p class="text-muted mb-0">
                Already have an account?
                <a href="{{ route('tenant.login') }}" class="text-decoration-none fw-semibold">Login here</a>
            </p>
        </div>
    </div>
</div>
@endsection
