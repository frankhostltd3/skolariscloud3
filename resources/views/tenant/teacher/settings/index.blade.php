@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Settings</h1>
            <p class="text-muted mb-0">Manage your preferences and security settings</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Notification Preferences -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bell me-2"></i>Notification Preferences
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.teacher.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="email_notifications" 
                                       name="email_notifications" 
                                       value="1"
                                       {{ $prefs['email_notifications'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    <strong>Email Notifications</strong>
                                    <p class="text-muted small mb-0">Receive notifications via email about important updates, assignments, and announcements.</p>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="sms_notifications" 
                                       name="sms_notifications" 
                                       value="1"
                                       {{ $prefs['sms_notifications'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_notifications">
                                    <strong>SMS Notifications</strong>
                                    <p class="text-muted small mb-0">Receive text messages for urgent updates and reminders.</p>
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.teacher.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-shield-check me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Name</label>
                            <p class="fw-medium mb-0">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="fw-medium mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Phone</label>
                            <p class="fw-medium mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Role</label>
                            <p class="mb-0">
                                <span class="badge bg-primary">Teacher</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Account Created</label>
                            <p class="fw-medium mb-0">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Last Updated</label>
                            <p class="fw-medium mb-0">
                                <i class="bi bi-clock me-1"></i>
                                {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Want to update your profile information?</p>
                            <p class="text-muted small mb-0">Edit your name, phone, address, and other details.</p>
                        </div>
                        <a href="{{ route('tenant.teacher.profile.show') }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning bg-opacity-10 border-warning">
                    <h6 class="mb-0 text-warning">
                        <i class="bi bi-shield-exclamation me-2"></i>Security Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li class="mb-2">Use a strong, unique password that includes uppercase, lowercase, numbers, and special characters.</li>
                        <li class="mb-2">Never share your password with anyone, including administrators.</li>
                        <li class="mb-2">Change your password regularly (at least every 90 days).</li>
                        <li class="mb-2">Log out when using shared or public computers.</li>
                        <li class="mb-0">Report any suspicious activity or unauthorized access immediately.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

