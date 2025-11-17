@extends('landlord.layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h4 fw-semibold mb-4">{{ __('Your profile') }}</h1>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form action="{{ route('landlord.profile.update', absolute: false) }}" method="post" class="row g-3">
                @csrf
                @method('put')

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" for="name">{{ __('Full name') }}</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" for="email">{{ __('Email address') }}</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <hr class="my-4">
                    <h2 class="h6 text-secondary mb-3">{{ __('Change password') }}</h2>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="password">{{ __('New password') }}</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="password_confirmation">{{ __('Confirm new password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <span class="bi bi-check-circle me-1"></span>{{ __('Save changes') }}
                        </button>
                        <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
