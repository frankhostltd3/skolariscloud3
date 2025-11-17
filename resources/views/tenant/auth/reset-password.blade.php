@extends('tenant.layouts.guest')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-3">{{ __('Reset password') }}</h1>

          <form method="POST" action="{{ route('tenant.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">

            <div class="mb-3">
              <label for="email" class="form-label">{{ __('Email') }}</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', request('email')) }}" required autofocus>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">{{ __('New password') }}</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="{{ route('tenant.login') }}" class="small">{{ __('Back to login') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
