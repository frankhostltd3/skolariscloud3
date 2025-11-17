@extends('tenant.layouts.guest')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-3">{{ __('Forgot your password?') }}</h1>
          <p class="text-secondary small mb-4">{{ __('Tell us your email and we will email you a password reset link.') }}</p>

          @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          <form method="POST" action="{{ route('tenant.password.email') }}">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">{{ __('Email') }}</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <a href="{{ route('tenant.login') }}" class="small">{{ __('Back to login') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Email Reset Link') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
