@extends('tenant.layouts.app')

@section('title', __('System Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('System Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.system.update') }}" method="POST">
                        @csrf

                        <!-- Maintenance Mode -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Maintenance Mode') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="maintenance_mode" class="form-label">{{ __('Maintenance Mode') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1"
                                           {{ old('maintenance_mode', setting('maintenance_mode', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        {{ __('Enable maintenance mode') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="maintenance_message" class="form-label">{{ __('Maintenance Message') }}</label>
                                <textarea class="form-control @error('maintenance_message') is-invalid @enderror"
                                          id="maintenance_message" name="maintenance_message" rows="3">{{ old('maintenance_message', setting('maintenance_message', 'The system is currently under maintenance. Please try again later.')) }}</textarea>
                                @error('maintenance_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Security Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="password_min_length" class="form-label">{{ __('Minimum Password Length') }}</label>
                                <input type="number" class="form-control @error('password_min_length') is-invalid @enderror"
                                       id="password_min_length" name="password_min_length" min="6" max="50"
                                       value="{{ old('password_min_length', setting('password_min_length', 8)) }}" required>
                                @error('password_min_length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_complexity" class="form-label">{{ __('Password Complexity Requirements') }}</label>
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_uppercase" name="require_uppercase" value="1"
                                               {{ old('require_uppercase', setting('require_uppercase', true)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_uppercase">{{ __('Require uppercase letters') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_lowercase" name="require_lowercase" value="1"
                                               {{ old('require_lowercase', setting('require_lowercase', true)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_lowercase">{{ __('Require lowercase letters') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_numbers" name="require_numbers" value="1"
                                               {{ old('require_numbers', setting('require_numbers', true)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_numbers">{{ __('Require numbers') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_symbols" name="require_symbols" value="1"
                                               {{ old('require_symbols', setting('require_symbols', false)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_symbols">{{ __('Require special characters') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="session_timeout" class="form-label">{{ __('Session Timeout (minutes)') }}</label>
                                <input type="number" class="form-control @error('session_timeout') is-invalid @enderror"
                                       id="session_timeout" name="session_timeout" min="5" max="1440"
                                       value="{{ old('session_timeout', setting('session_timeout', 120)) }}" required>
                                @error('session_timeout')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="max_login_attempts" class="form-label">{{ __('Maximum Login Attempts') }}</label>
                                <input type="number" class="form-control @error('max_login_attempts') is-invalid @enderror"
                                       id="max_login_attempts" name="max_login_attempts" min="1" max="10"
                                       value="{{ old('max_login_attempts', setting('max_login_attempts', 5)) }}" required>
                                @error('max_login_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- File Upload Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('File Upload Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="max_file_size" class="form-label">{{ __('Maximum File Size (MB)') }}</label>
                                <input type="number" class="form-control @error('max_file_size') is-invalid @enderror"
                                       id="max_file_size" name="max_file_size" min="1" max="100"
                                       value="{{ old('max_file_size', setting('max_file_size', 10)) }}" required>
                                @error('max_file_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="allowed_file_types" class="form-label">{{ __('Allowed File Types') }}</label>
                                <input type="text" class="form-control @error('allowed_file_types') is-invalid @enderror"
                                       id="allowed_file_types" name="allowed_file_types"
                                       value="{{ old('allowed_file_types', setting('allowed_file_types', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx')) }}"
                                       placeholder="Comma-separated extensions" required>
                                @error('allowed_file_types')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Backup Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Backup Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="auto_backup" class="form-label">{{ __('Automatic Backup') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" value="1"
                                           {{ old('auto_backup', setting('auto_backup', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_backup">
                                        {{ __('Enable automatic backups') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="backup_frequency" class="form-label">{{ __('Backup Frequency') }}</label>
                                <select class="form-select @error('backup_frequency') is-invalid @enderror" id="backup_frequency" name="backup_frequency">
                                    <option value="daily" {{ old('backup_frequency', setting('backup_frequency', 'weekly')) == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('backup_frequency', setting('backup_frequency', 'weekly')) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('backup_frequency', setting('backup_frequency', 'weekly')) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                @error('backup_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="backup_retention" class="form-label">{{ __('Backup Retention (days)') }}</label>
                                <input type="number" class="form-control @error('backup_retention') is-invalid @enderror"
                                       id="backup_retention" name="backup_retention" min="1" max="365"
                                       value="{{ old('backup_retention', setting('backup_retention', 30)) }}" required>
                                @error('backup_retention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Notification Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="email_notifications" class="form-label">{{ __('Email Notifications') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1"
                                           {{ old('email_notifications', setting('email_notifications', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        {{ __('Enable email notifications') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="sms_notifications" class="form-label">{{ __('SMS Notifications') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1"
                                           {{ old('sms_notifications', setting('sms_notifications', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_notifications">
                                        {{ __('Enable SMS notifications') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- API Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('API Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="api_rate_limit" class="form-label">{{ __('API Rate Limit (requests per minute)') }}</label>
                                <input type="number" class="form-control @error('api_rate_limit') is-invalid @enderror"
                                       id="api_rate_limit" name="api_rate_limit" min="10" max="1000"
                                       value="{{ old('api_rate_limit', setting('api_rate_limit', 60)) }}" required>
                                @error('api_rate_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="api_key_expiry" class="form-label">{{ __('API Key Expiry (days)') }}</label>
                                <input type="number" class="form-control @error('api_key_expiry') is-invalid @enderror"
                                       id="api_key_expiry" name="api_key_expiry" min="30" max="365"
                                       value="{{ old('api_key_expiry', setting('api_key_expiry', 365)) }}" required>
                                @error('api_key_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save System Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection