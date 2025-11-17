@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

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

<form action="{{ route('tenant.profile.admin.update') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <!-- Profile Photo Section -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Profile Photo') }}</h5>
    </div>
    <div class="card-body text-center">
      @php $adminAvatar = $user->profile_photo_url; @endphp
      <div class="mb-3">
        @if($adminAvatar)
          <img src="{{ $adminAvatar }}"
               alt="{{ $user->name }}"
               class="img-fluid rounded-circle mb-3"
               style="width: 120px; height: 120px; object-fit: cover;"
               id="profilePreview">
        @else
          <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
               style="width: 120px; height: 120px;" id="profilePreview">
            <i class="fas fa-user fa-3x text-muted"></i>
          </div>
        @endif
      </div>

      <div class="mb-3">
        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror"
               id="profile_photo" name="profile_photo" accept="image/*">
        @error('profile_photo')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">{{ __('Max file size: 2MB. Allowed formats: JPG, PNG, GIF') }}</small>
      </div>
    </div>
  </div>

  <!-- Profile Information -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Profile Information') }}</h5>
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
        <div class="col-md-6">
          <label class="form-label">{{ __('Role') }}</label>
          <input type="text" class="form-control" value="{{ $user->getRoleNames()->first() ?? 'Admin' }}" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Member Since') }}</label>
          <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y') }}" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Last Updated') }}</label>
          <input type="text" class="form-control" value="{{ $user->updated_at->format('M d, Y H:i') }}" readonly>
        </div>
      </div>
    </div>
  </div>

  <!-- Account Security -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Account Security') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
          <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
          @error('current_password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="new_password" class="form-label">{{ __('New Password') }}</label>
          <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
          @error('new_password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="new_password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
          <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
        </div>
      </div>
      <div class="mt-3">
        <small class="text-muted">{{ __('Leave password fields empty if you don\'t want to change your password.') }}</small>
      </div>
    </div>
  </div>

  <!-- System Information -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('System Information') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('Account Status') }}</label>
          <input type="text" class="form-control" value="{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Two-Factor Authentication') }}</label>
          <input type="text" class="form-control" value="{{ $user->two_factor_secret ? 'Enabled' : 'Disabled' }}" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Last Login') }}</label>
          <input type="text" class="form-control" value="{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Login Attempts') }}</label>
          <input type="text" class="form-control" value="{{ $user->login_attempts ?? 0 }}" readonly>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
  </div>
</form>
@endsection

@section('scripts')
<script>
document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            e.target.value = '';
            alert('{{ __("Selected file is too large. Maximum allowed size is 2MB.") }}');
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            e.target.value = '';
            alert('{{ __("Invalid file type. Please select a JPG, PNG, or GIF image.") }}');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection