@extends('landlord.layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <h1 class="h4 fw-semibold mb-3">{{ __('Create tenant') }}</h1>
    <p class="text-secondary mb-4">{{ __('Provision a new tenant to Skolaris.') }}</p>

  <form action="{{ route('landlord.tenants.store') }}" method="post" class="row g-3">
      @csrf

      <div class="col-md-6">
        <label for="school_name" class="form-label fw-semibold">{{ __('School Name') }}</label>
        <input
          type="text"
          id="school_name"
          name="school_name"
          class="form-control @error('school_name') is-invalid @enderror"
          value="{{ old('school_name') }}"
          required
          placeholder="{{ __('e.g., Springfield Elementary School') }}"
        >
        @error('school_name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label for="domain" class="form-label fw-semibold">{{ __('Domain') }}</label>
        <div class="input-group">
          <input
            type="text"
            id="domain"
            name="domain"
            class="form-control @error('domain') is-invalid @enderror"
            value="{{ old('domain') }}"
            required
            placeholder="{{ __('e.g., springfield-school') }}"
          >
          <span class="input-group-text">.localhost</span>
        </div>
        <div class="form-text">{{ __('This will create springfield-school.localhost') }}</div>
        @error('domain')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12">
        <label for="admin_email" class="form-label fw-semibold">{{ __('Admin Email') }}</label>
        <input
          type="email"
          id="admin_email"
          name="admin_email"
          class="form-control @error('admin_email') is-invalid @enderror"
          value="{{ old('admin_email') }}"
          required
          placeholder="{{ __('admin@school.edu') }}"
        >
        @error('admin_email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label for="contact_email" class="form-label fw-semibold">{{ __('Contact Email (billing/support)') }}</label>
        <input
          type="email"
          id="contact_email"
          name="contact_email"
          class="form-control @error('contact_email') is-invalid @enderror"
          value="{{ old('contact_email') }}"
          placeholder="{{ __('contact@school.edu') }}"
        >
        @error('contact_email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label for="phones" class="form-label fw-semibold">{{ __('Phone numbers (comma-separated)') }}</label>
        <input
          type="text"
          id="phones"
          name="phones"
          class="form-control @error('phones') is-invalid @enderror"
          value="{{ old('phones') }}"
          placeholder="{{ __('e.g., +254700000000, +254711111111') }}"
        >
        @error('phones')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label for="admin_name" class="form-label fw-semibold">{{ __('Admin Name') }}</label>
        <input
          type="text"
          id="admin_name"
          name="admin_name"
          class="form-control @error('admin_name') is-invalid @enderror"
          value="{{ old('admin_name') }}"
          required
          placeholder="{{ __('John Doe') }}"
        >
        @error('admin_name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label for="admin_password" class="form-label fw-semibold">{{ __('Admin Password') }}</label>
        <input
          type="password"
          id="admin_password"
          name="admin_password"
          class="form-control @error('admin_password') is-invalid @enderror"
          required
          placeholder="{{ __('Minimum 8 characters') }}"
        >
        @error('admin_password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label for="admin_password_confirmation" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
        <input
          type="password"
          id="admin_password_confirmation"
          name="admin_password_confirmation"
          class="form-control"
          required
          placeholder="{{ __('Re-enter password') }}"
        >
      </div>

      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="seed_sample_data" name="seed_sample_data" {{ old('seed_sample_data') ? 'checked' : '' }}>
          <label class="form-check-label" for="seed_sample_data">
            {{ __('Seed with sample data (classes, subjects, users)') }}
          </label>
        </div>
      </div>

      <div class="col-12">
        <button type="submit" class="btn btn-primary">
          <span class="bi bi-plus-circle me-2"></span>{{ __('Create Tenant') }}
        </button>
        <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Cancel') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
