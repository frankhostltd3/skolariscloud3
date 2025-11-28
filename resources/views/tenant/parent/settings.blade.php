@extends('layouts.dashboard-parent')

@section('title', __('Account Settings'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Account Settings') }}</h1>
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
    <form action="{{ route('tenant.profile.parent.updateSettings') }}" method="POST">
      @csrf
      @method('PUT')

      <!-- Notification Preferences Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Notification Preferences') }}</h5>
        </div>
        <div class="card-body">
          <p class="text-muted mb-4">{{ __('Choose how you want to receive notifications about your child\'s activities and school updates.') }}</p>

          @php
          $preferences = json_decode($user->notification_preferences ?? '{}', true);
          @endphp

          <!-- Delivery Methods -->
          <div class="row">
            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title">{{ __('Delivery Methods') }}</h6>

                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_notifications"
                        name="email_notifications" value="1"
                        {{ ($preferences['email_notifications'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="email_notifications">
                      <i class="bi bi-envelope text-primary me-2"></i>{{ __('Email Notifications') }}
                    </label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="sms_notifications"
                        name="sms_notifications" value="1"
                        {{ ($preferences['sms_notifications'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="sms_notifications">
                      <i class="bi bi-phone text-success me-2"></i>{{ __('SMS Notifications') }}
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title">{{ __('Notification Types') }}</h6>

                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="grade_notifications"
                        name="grade_notifications" value="1"
                        {{ ($preferences['grade_notifications'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="grade_notifications">
                      <i class="bi bi-graph-up text-info me-2"></i>{{ __('Grade Updates') }}
                    </label>
                  </div>

                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="attendance_notifications"
                        name="attendance_notifications" value="1"
                        {{ ($preferences['attendance_notifications'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="attendance_notifications">
                      <i class="bi bi-check-circle text-warning me-2"></i>{{ __('Attendance Updates') }}
                    </label>
                  </div>

                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="fee_notifications"
                        name="fee_notifications" value="1"
                        {{ ($preferences['fee_notifications'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="fee_notifications">
                      <i class="bi bi-cash text-danger me-2"></i>{{ __('Fee Reminders') }}
                    </label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="event_notifications"
                        name="event_notifications" value="1"
                        {{ ($preferences['event_notifications'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="event_notifications">
                      <i class="bi bi-calendar-event text-purple me-2"></i>{{ __('School Events') }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Privacy Settings Section -->
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Privacy Settings') }}</h5>
        </div>
        <div class="card-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>{{ __('Privacy Notice') }}:</strong> {{ __('Your information is kept secure and is only shared with school staff as necessary for your child\'s education and safety.') }}
          </div>

          <div class="card bg-light">
            <div class="card-body">
              <h6 class="card-title mb-3">{{ __('Data Sharing Preferences') }}</h6>

              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="share_contact_with_teachers"
                    name="share_contact_with_teachers" value="1" checked>
                <label class="form-check-label" for="share_contact_with_teachers">
                  {{ __('Allow teachers to contact me directly') }}
                </label>
              </div>

              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="share_progress_with_child"
                    name="share_progress_with_child" value="1" checked>
                <label class="form-check-label" for="share_progress_with_child">
                  {{ __('Allow my child to see their progress reports') }}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between">
  <a href="{{ route('tenant.profile.parent.index') }}" class="btn btn-secondary">{{ __('Back to Profile') }}</a>
        <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
      </div>
    </form>
  </div>

  <div class="col-md-4">
    <!-- Settings Help -->
    <div class="card shadow-sm">
      <div class="card-header">
        <h5 class="mb-0">{{ __('Settings Help') }}</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <h6 class="text-primary">{{ __('Email Notifications') }}</h6>
          <p class="small text-muted">{{ __('Receive updates via email to') }} {{ $user->email }}</p>
        </div>

        <div class="mb-3">
          <h6 class="text-success">{{ __('SMS Notifications') }}</h6>
          <p class="small text-muted">{{ __('Receive urgent updates via SMS to') }} {{ $user->phone ?: __('your phone number') }}</p>
        </div>

        <div class="mb-3">
          <h6 class="text-info">{{ __('Grade Updates') }}</h6>
          <p class="small text-muted">{{ __('Get notified when new grades are posted') }}</p>
        </div>

        <div class="mb-3">
          <h6 class="text-warning">{{ __('Attendance Updates') }}</h6>
          <p class="small text-muted">{{ __('Receive alerts about absences or late arrivals') }}</p>
        </div>

        <hr>

        <div class="text-center">
          <a href="{{ route('tenant.profile.parent.changePassword') }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-key me-1"></i>{{ __('Change Password') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle notification type dependencies
    const emailNotifications = document.getElementById('email_notifications');
    const smsNotifications = document.getElementById('sms_notifications');
    const notificationTypes = ['grade_notifications', 'attendance_notifications', 'fee_notifications', 'event_notifications'];

    function toggleNotificationTypes() {
        const hasDeliveryMethod = emailNotifications.checked || smsNotifications.checked;
        notificationTypes.forEach(typeId => {
            const element = document.getElementById(typeId);
            element.disabled = !hasDeliveryMethod;
            if (!hasDeliveryMethod) {
                element.checked = false;
            }
        });
    }

    emailNotifications.addEventListener('change', toggleNotificationTypes);
    smsNotifications.addEventListener('change', toggleNotificationTypes);

    // Initial check
    toggleNotificationTypes();
});
</script>
@endpush