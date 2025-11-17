@extends('tenant.layouts.guest')

@section('title', 'Register')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <!-- Logo/Header -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Create Account</h2>
            <p class="text-muted">Join our learning community</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.register') }}">
            @csrf

            <!-- Full Name -->
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
                       placeholder="Enter your email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Role Selection -->
            <div class="mb-3">
                <label for="role" class="form-label">I am registering as</label>
                <select class="form-select @error('role') is-invalid @enderror" 
                        id="role" 
                        name="role" 
                        required>
                    <option value="">-- Select Your Role --</option>
                    <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff / Teacher</option>
                    <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                    <option value="Parent" {{ old('role') == 'Parent' ? 'selected' : '' }}>Parent / Guardian</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted d-block mt-1">Select the role that applies to you</small>
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

            <!-- Terms Agreement -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" required>
                <label class="form-check-label" for="terms">
                    I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and 
                    <a href="#" class="text-decoration-none">Privacy Policy</a>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    Create Account
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
