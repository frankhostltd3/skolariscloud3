@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Admin Settings') }}</h1>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<form action="{{ route('tenant.settings.admin.update') }}" method="POST">
  @csrf
  @method('PUT')

  <!-- Profile Settings -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Profile Settings') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="name" class="form-label">{{ __('Full Name') }}</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="email" class="form-label">{{ __('Email Address') }}</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
          <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
          @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
  </div>

  <!-- System Preferences -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('System Preferences') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="language" class="form-label">{{ __('Language') }}</label>
          <select class="form-select @error('language') is-invalid @enderror" id="language" name="language">
            <option value="en" {{ old('language', $user->language ?? 'en') == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
            <option value="sw" {{ old('language', $user->language ?? 'en') == 'sw' ? 'selected' : '' }}>{{ __('Swahili') }}</option>
          </select>
          @error('language')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="theme" class="form-label">{{ __('Theme') }}</label>
          <select class="form-select @error('theme') is-invalid @enderror" id="theme" name="theme">
            <option value="light" {{ old('theme', $user->theme ?? 'light') == 'light' ? 'selected' : '' }}>{{ __('Light') }}</option>
            <option value="dark" {{ old('theme', $user->theme ?? 'light') == 'dark' ? 'selected' : '' }}>{{ __('Dark') }}</option>
          </select>
          @error('theme')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="dashboard_layout" class="form-label">{{ __('Dashboard Layout') }}</label>
          <select class="form-select @error('dashboard_layout') is-invalid @enderror" id="dashboard_layout" name="dashboard_layout">
            <option value="compact" {{ old('dashboard_layout', $user->dashboard_layout ?? 'detailed') == 'compact' ? 'selected' : '' }}>{{ __('Compact') }}</option>
            <option value="detailed" {{ old('dashboard_layout', $user->dashboard_layout ?? 'detailed') == 'detailed' ? 'selected' : '' }}>{{ __('Detailed') }}</option>
          </select>
          @error('dashboard_layout')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="auto_save" name="auto_save" value="1" {{ old('auto_save', $user->auto_save ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="auto_save">
              {{ __('Auto-save forms') }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Notification Preferences -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Notification Preferences') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="notifications_email" name="notifications_email" value="1" {{ old('notifications_email', $user->notifications_email ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="notifications_email">
              {{ __('Email Notifications') }}
            </label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="notifications_sms" name="notifications_sms" value="1" {{ old('notifications_sms', $user->notifications_sms ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="notifications_sms">
              {{ __('SMS Notifications') }}
            </label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="notifications_system" name="notifications_system" value="1" {{ old('notifications_system', $user->notifications_system ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="notifications_system">
              {{ __('System Notifications') }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
  </div>
</form>
@endsection