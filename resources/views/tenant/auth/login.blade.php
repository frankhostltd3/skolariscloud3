@extends('tenant.layouts.guest')

@section('content')
<div class="tenant-auth-card card border-0 shadow-lg overflow-hidden">
  <div class="tenant-auth-card__header text-center px-4 px-lg-5 py-5">
    <span class="tenant-auth-card__badge text-uppercase fw-semibold">{{ __('Welcome back') }}</span>
    <h1 class="tenant-auth-card__title mt-3 mb-2">{{ __('Sign in to your campus') }}</h1>
    <p class="text-muted small mb-0">{{ __('Enter your credentials to continue to your personalized workspace.') }}</p>
  </div>
  <div class="card-body p-4 p-lg-5">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('info'))
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form method="POST" action="{{ route('tenant.login', absolute: false) }}" class="d-grid gap-3">
      @csrf
      <div>
        <label class="form-label" for="email">{{ __('Email') }}</label>
        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div>
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label mb-0" for="password">{{ __('Password') }}</label>
          <a href="{{ route('tenant.forgot-password') }}" class="small link-offset-2">{{ __('Forgot password?') }}</a>
        </div>
        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
          <label class="form-check-label" for="remember">{{ __('Remember me') }}</label>
        </div>
      </div>
      <button class="btn btn-primary btn-lg w-100" type="submit">{{ __('Log in') }}</button>
      
      <div class="text-center">
        <span class="text-muted small">{{ __("Don't have an account?") }}</span>
        <a href="{{ route('tenant.register') }}" class="small fw-semibold link-offset-2">{{ __('Register here') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
