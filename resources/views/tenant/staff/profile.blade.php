@extends('tenant.layouts.app')

@section('sidebar')
<div class="card shadow-sm">
	<div class="card-header fw-semibold">{{ __('Staff menu') }}</div>
	<div class="list-group list-group-flush">
		<a
			class="list-group-item list-group-item-action {{ request()->routeIs('tenant.staff') ? 'active' : '' }}"
			href="{{ route('tenant.staff') }}"
			@if(request()->routeIs('tenant.staff')) aria-current="page" @endif
		>
			<span class="bi bi-speedometer me-2"></span>{{ __('Overview') }}
		</a>

		<a
			class="list-group-item list-group-item-action {{ request()->routeIs('tenant.profile.staff.*') ? 'active' : '' }}"
			href="{{ route('tenant.profile.staff.index') }}"
			@if(request()->routeIs('tenant.profile.staff.*')) aria-current="page" @endif
		>
			<span class="bi bi-person me-2"></span>{{ __('My Profile') }}
		</a>

		@canany(['manage attendance','view attendance'])
			<a
				class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.attendance.*') ? 'active' : '' }}"
				href="{{ route('tenant.modules.attendance.index') }}"
				@if(request()->routeIs('tenant.modules.attendance.*')) aria-current="page" @endif
			>
				<span class="bi bi-clipboard-check me-2"></span>{{ __('Attendance tracker') }}
			</a>
		@endcanany

		@canany(['manage timetable','view timetable'])
			<a
				class="list-group-item list-group-item-action {{ request()->routeIs('tenant.academics.timetable.*') ? 'active' : '' }}"
				href="{{ route('tenant.academics.timetable.index') }}"
				@if(request()->routeIs('tenant.academics.timetable.*')) aria-current="page" @endif
			>
				<span class="bi bi-calendar-event me-2"></span>{{ __('Timetable') }}
			</a>
		@endcanany
	</div>
</div>
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

<form action="{{ route('tenant.profile.staff.update') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <!-- Personal Information -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Personal Information') }}</h5>
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
        <div class="col-md-6">
          <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }}</label>
          <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
          @error('date_of_birth')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Role') }}</label>
          <input type="text" class="form-control" value="{{ $user->getRoleNames()->first() ?? 'Staff' }}" readonly>
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

  <!-- Employment Information -->
  @if($employee)
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Employment Information') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="employee_number" class="form-label">{{ __('Employee Number') }}</label>
          <input type="text" class="form-control @error('employee_number') is-invalid @enderror" id="employee_number" name="employee_number" value="{{ old('employee_number', $employee->employee_number) }}">
          @error('employee_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="department" class="form-label">{{ __('Department') }}</label>
          <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department', $employee->department) }}">
          @error('department')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="position" class="form-label">{{ __('Position') }}</label>
          <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $employee->position) }}">
          @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Date Hired') }}</label>
          <input type="text" class="form-control" value="{{ $employee->created_at ? $employee->created_at->format('M d, Y') : 'N/A' }}" readonly>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Professional Information -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Professional Information') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="qualification" class="form-label">{{ __('Qualification') }}</label>
          <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" value="{{ old('qualification', $user->qualification) }}" placeholder="e.g., Master's in Education">
          @error('qualification')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="specialization" class="form-label">{{ __('Specialization') }}</label>
          <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization', $user->specialization) }}" placeholder="e.g., Mathematics, Science">
          @error('specialization')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Photo -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Profile Photo') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="text-center">
            @if($user->profile_photo)
              <img src="{{ Storage::disk('public')->url($user->profile_photo) }}" alt="Profile Photo" class="img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
            @else
              <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                <i class="bi bi-person-fill text-muted" style="font-size: 3rem;"></i>
              </div>
            @endif
          </div>
        </div>
        <div class="col-md-8">
          <label for="profile_photo" class="form-label">{{ __('Upload New Photo') }}</label>
          <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
          @error('profile_photo')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <div class="form-text">
            {{ __('Accepted formats: JPG, PNG, GIF. Maximum size: 2MB.') }}
          </div>
          @if($user->profile_photo)
            <div class="mt-2">
              <small class="text-muted">{{ __('Leave empty to keep current photo.') }}</small>
            </div>
          @endif
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

  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
  </div>
</form>
@endsection