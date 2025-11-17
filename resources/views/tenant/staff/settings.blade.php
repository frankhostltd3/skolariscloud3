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
			class="list-group-item list-group-item-action {{ request()->routeIs('tenant.settings.staff.*') ? 'active' : '' }}"
			href="{{ route('tenant.settings.staff.index') }}"
			@if(request()->routeIs('tenant.settings.staff.*')) aria-current="page" @endif
		>
			<span class="bi bi-gear me-2"></span>{{ __('Settings') }}
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
  <h1 class="h4 fw-semibold mb-0">{{ __('Staff Settings') }}</h1>
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

<form action="{{ route('tenant.settings.staff.update') }}" method="POST">
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
      </div>
    </div>
  </div>

  <!-- Work Preferences -->
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Work Preferences') }}</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="work_schedule_visible" name="work_schedule_visible" value="1" {{ old('work_schedule_visible', $user->work_schedule_visible ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="work_schedule_visible">
              {{ __('Show work schedule to students') }}
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="leave_reminders" name="leave_reminders" value="1" {{ old('leave_reminders', $user->leave_reminders ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="leave_reminders">
              {{ __('Leave request reminders') }}
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="task_notifications" name="task_notifications" value="1" {{ old('task_notifications', $user->task_notifications ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="task_notifications">
              {{ __('Task assignment notifications') }}
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