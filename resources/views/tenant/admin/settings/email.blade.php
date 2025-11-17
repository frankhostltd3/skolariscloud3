@extends('tenant.layouts.app')

@section('title', __('Email Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Email Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.email.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- SMTP Configuration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('SMTP Configuration') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="mail_driver" class="form-label">{{ __('Mail Driver') }}</label>
                                <select class="form-select @error('mail_driver') is-invalid @enderror" id="mail_driver" name="mail_driver">
                                    <option value="smtp" {{ old('mail_driver', setting('mail_driver', 'smtp')) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="mailgun" {{ old('mail_driver', setting('mail_driver', 'smtp')) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ old('mail_driver', setting('mail_driver', 'smtp')) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="postmark" {{ old('mail_driver', setting('mail_driver', 'smtp')) == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                </select>
                                @error('mail_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_host" class="form-label">{{ __('SMTP Host') }}</label>
                                <input type="text" class="form-control @error('mail_host') is-invalid @enderror"
                                       id="mail_host" name="mail_host"
                                       value="{{ old('mail_host', setting('mail_host', 'smtp.gmail.com')) }}" required>
                                @error('mail_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_port" class="form-label">{{ __('SMTP Port') }}</label>
                                <input type="number" class="form-control @error('mail_port') is-invalid @enderror"
                                       id="mail_port" name="mail_port"
                                       value="{{ old('mail_port', setting('mail_port', 587)) }}" required>
                                @error('mail_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_encryption" class="form-label">{{ __('Encryption') }}</label>
                                <select class="form-select @error('mail_encryption') is-invalid @enderror" id="mail_encryption" name="mail_encryption">
                                    <option value="tls" {{ old('mail_encryption', setting('mail_encryption', 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('mail_encryption', setting('mail_encryption', 'tls')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ old('mail_encryption', setting('mail_encryption', 'tls')) == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('mail_encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_username" class="form-label">{{ __('SMTP Username') }}</label>
                                <input type="text" class="form-control @error('mail_username') is-invalid @enderror"
                                       id="mail_username" name="mail_username"
                                       value="{{ old('mail_username', setting('mail_username', '')) }}" required>
                                @error('mail_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_password" class="form-label">{{ __('SMTP Password') }}</label>
                                <input type="password" class="form-control @error('mail_password') is-invalid @enderror"
                                       id="mail_password" name="mail_password"
                                       value="{{ old('mail_password', setting('mail_password', '')) }}" required>
                                @error('mail_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Addresses -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Email Addresses') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="mail_from_address" class="form-label">{{ __('From Email Address') }}</label>
                                <input type="email" class="form-control @error('mail_from_address') is-invalid @enderror"
                                       id="mail_from_address" name="mail_from_address"
                                       value="{{ old('mail_from_address', setting('mail_from_address', 'noreply@skolariscloud.com')) }}" required>
                                @error('mail_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_from_name" class="form-label">{{ __('From Name') }}</label>
                                <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror"
                                       id="mail_from_name" name="mail_from_name"
                                       value="{{ old('mail_from_name', setting('mail_from_name', 'SkolarisCloud')) }}" required>
                                @error('mail_from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Templates -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Email Templates') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="welcome_email_subject" class="form-label">{{ __('Welcome Email Subject') }}</label>
                                <input type="text" class="form-control @error('welcome_email_subject') is-invalid @enderror"
                                       id="welcome_email_subject" name="welcome_email_subject"
                                       value="{{ old('welcome_email_subject', setting('welcome_email_subject', 'Welcome to SkolarisCloud')) }}">
                                @error('welcome_email_subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_reset_subject" class="form-label">{{ __('Password Reset Subject') }}</label>
                                <input type="text" class="form-control @error('password_reset_subject') is-invalid @enderror"
                                       id="password_reset_subject" name="password_reset_subject"
                                       value="{{ old('password_reset_subject', setting('password_reset_subject', 'Reset Your Password')) }}">
                                @error('password_reset_subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Notifications -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Email Notifications') }}</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_new_registration" name="email_notifications[]" value="new_registration"
                                                   {{ in_array('new_registration', old('email_notifications', setting('email_notifications', ['new_registration', 'fee_reminder', 'exam_results']))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_new_registration">{{ __('New user registration') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_fee_reminder" name="email_notifications[]" value="fee_reminder"
                                                   {{ in_array('fee_reminder', old('email_notifications', setting('email_notifications', ['new_registration', 'fee_reminder', 'exam_results']))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_fee_reminder">{{ __('Fee payment reminders') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_exam_results" name="email_notifications[]" value="exam_results"
                                                   {{ in_array('exam_results', old('email_notifications', setting('email_notifications', ['new_registration', 'fee_reminder', 'exam_results']))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_exam_results">{{ __('Exam results publication') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_attendance_alert" name="email_notifications[]" value="attendance_alert"
                                                   {{ in_array('attendance_alert', old('email_notifications', setting('email_notifications', ['new_registration', 'fee_reminder', 'exam_results']))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_attendance_alert">{{ __('Attendance alerts') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_system_alerts" name="email_notifications[]" value="system_alerts"
                                                   {{ in_array('system_alerts', old('email_notifications', setting('email_notifications', ['new_registration', 'fee_reminder', 'exam_results']))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_system_alerts">{{ __('System alerts and notifications') }}</label>
                                        </div>
                                    </div>
                                </div>
                                @error('email_notifications')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save Email Settings') }}
                                </button>
                                <a href="{{ route('tenant.settings.admin.test-email') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-envelope"></i> {{ __('Test Email Configuration') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection