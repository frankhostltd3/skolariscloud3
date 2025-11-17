@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.parent._sidebar')
@endsection

@section('title', __('My Profile'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('My Profile') }}</h1>
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

<div class="row">
  <div class="col-md-8">
    <form action="{{ route('tenant.profile.parent.update') }}" method="POST">
      @csrf
      @method('PUT')

      <!-- Personal Information Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Personal Information') }}</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
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
            <div class="col-md-6">
              <label for="gender" class="form-label">{{ __('Gender') }}</label>
              <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                <option value="">{{ __('Select Gender') }}</option>
                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
              </select>
              @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label for="address" class="form-label">{{ __('Address') }}</label>
              <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Emergency Contact Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Emergency Contact') }}</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="emergency_contact_name" class="form-label">{{ __('Emergency Contact Name') }}</label>
              <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}">
              @error('emergency_contact_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="emergency_contact_phone" class="form-label">{{ __('Emergency Contact Phone') }}</label>
              <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}">
              @error('emergency_contact_phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Children Information -->
      @if($children && $children->count() > 0)
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h5 class="mb-0">{{ __('My Children') }}</h5>
        </div>
        <div class="card-body">
          <div class="row">
            @foreach($children as $child)
            <div class="col-md-6 mb-3">
              <div class="card border">
                <div class="card-body">
                  <h6 class="card-title">{{ $child->full_name ?? $child->name }}</h6>
                  <p class="card-text mb-1">
                    <strong>{{ __('Student Number') }}:</strong> {{ $child->student_number ?? 'N/A' }}
                  </p>
                  <p class="card-text mb-1">
                    <strong>{{ __('Class') }}:</strong> {{ optional($child->class)->name ?? 'N/A' }}
                  </p>
                  <p class="card-text mb-1">
                    <strong>{{ __('Stream') }}:</strong> {{ optional($child->stream)->name ?? 'N/A' }}
                  </p>
                  <p class="card-text mb-0">
                    <strong>{{ __('Enrollment Date') }}:</strong> {{ $child->created_at ? $child->created_at->format('M d, Y') : 'N/A' }}
                  </p>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-lock"></i>
        <div>
          <strong>{{ __('Password changes happen from the security page.') }}</strong>
          <div class="small">{{ __('Use the Change Password button in Quick Actions to update your credentials securely.') }}</div>
        </div>
      </div>

      <div class="d-flex justify-content-end mb-4">
        <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
      </div>
    </form>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm mb-4">
      <div class="card-header">
        <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
              <a href="{{ route('tenant.profile.parent.settings') }}" class="btn btn-outline-primary">
            <i class="bi bi-gear me-2"></i>{{ __('Account Settings') }}
          </a>
              <a href="{{ route('tenant.profile.parent.changePassword') }}" class="btn btn-outline-warning">
            <i class="bi bi-key me-2"></i>{{ __('Change Password') }}
          </a>
              @can('view attendance')
                <a href="{{ route('tenant.parent.attendance.index') }}" class="btn btn-outline-success">
                  <i class="bi bi-clipboard-check me-2"></i>{{ __('Attendance') }}
                </a>
              @endcan
              @can('view fees')
                <a href="{{ route('tenant.parent.fees.index') }}" class="btn btn-outline-success">
                  <i class="bi bi-cash-stack me-2"></i>{{ __('Fees & Payments') }}
                </a>
              @endcan
        </div>
      </div>
    </div>

    <!-- Account Information -->
    <div class="card shadow-sm">
      <div class="card-header">
        <h5 class="mb-0">{{ __('Account Information') }}</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">{{ __('Account Type') }}</label>
          <p class="mb-1">{{ __('Parent') }}</p>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">{{ __('Member Since') }}</label>
          <p class="mb-1">{{ $user->created_at->format('M Y') }}</p>
        </div>
        <div class="mb-0">
          <label class="form-label fw-semibold">{{ __('Last Updated') }}</label>
          <p class="mb-0">{{ $user->updated_at->format('M d, Y') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
