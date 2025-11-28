@extends('tenant.layouts.app')

@section('content')
    <style>
        .spinning {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">
                <span class="bi bi-server me-2"></span>
                {{ __('System Settings') }}
            </h1>
            <p class="text-muted mb-0">{{ __('Configure system performance, security, and maintenance settings') }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- System Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <span class="bi bi-info-circle me-2"></span>
                    {{ __('System Information') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.system.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="system_info">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="account_status"
                                        name="account_status" value="1"
                                        {{ ($settings['account_status'] ?? 'verified') === 'verified' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="account_status">
                                        <span class="badge bg-success" id="account-status-badge">
                                            {{ ($settings['account_status'] ?? 'verified') === 'verified' ? 'Verified' : 'Unverified' }}
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted">Toggle between Verified and Unverified account status</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Two-Factor Authentication</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_two_factor_auth"
                                        name="enable_two_factor_auth" value="1"
                                        {{ $settings['enable_two_factor_auth'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_two_factor_auth">
                                        <span
                                            class="badge bg-{{ $settings['enable_two_factor_auth'] ?? false ? 'success' : 'secondary' }}"
                                            id="two-factor-badge">
                                            {{ $settings['enable_two_factor_auth'] ?? false ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted">Enable or disable two-factor authentication system-wide</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-info">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save System Information') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Performance -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <span class="bi bi-speedometer me-2"></span>
                    {{ __('System Performance') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.system.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="performance">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cache_driver" class="form-label">Cache Driver</label>
                                <select class="form-select @error('cache_driver') is-invalid @enderror" id="cache_driver"
                                    name="cache_driver">
                                    <option value="file"
                                        {{ ($settings['cache_driver'] ?? 'file') == 'file' ? 'selected' : '' }}>File
                                    </option>
                                    <option value="redis"
                                        {{ ($settings['cache_driver'] ?? 'file') == 'redis' ? 'selected' : '' }}>Redis
                                    </option>
                                    <option value="memcached"
                                        {{ ($settings['cache_driver'] ?? 'file') == 'memcached' ? 'selected' : '' }}>
                                        Memcached</option>
                                    <option value="database"
                                        {{ ($settings['cache_driver'] ?? 'file') == 'database' ? 'selected' : '' }}>
                                        Database</option>
                                </select>
                                @error('cache_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="session_driver" class="form-label">Session Driver</label>
                                <select class="form-select @error('session_driver') is-invalid @enderror"
                                    id="session_driver" name="session_driver">
                                    <option value="file"
                                        {{ ($settings['session_driver'] ?? 'file') == 'file' ? 'selected' : '' }}>File
                                    </option>
                                    <option value="database"
                                        {{ ($settings['session_driver'] ?? 'file') == 'database' ? 'selected' : '' }}>
                                        Database</option>
                                    <option value="redis"
                                        {{ ($settings['session_driver'] ?? 'file') == 'redis' ? 'selected' : '' }}>Redis
                                    </option>
                                    <option value="cookie"
                                        {{ ($settings['session_driver'] ?? 'file') == 'cookie' ? 'selected' : '' }}>Cookie
                                    </option>
                                </select>
                                @error('session_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="session_lifetime" class="form-label">Session Lifetime (minutes)</label>
                                <input type="number" class="form-control @error('session_lifetime') is-invalid @enderror"
                                    id="session_lifetime" name="session_lifetime"
                                    value="{{ old('session_lifetime', $settings['session_lifetime'] ?? '120') }}"
                                    min="1" max="1440">
                                @error('session_lifetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_file_upload" class="form-label">Max File Upload Size (MB)</label>
                                <input type="number" class="form-control @error('max_file_upload') is-invalid @enderror"
                                    id="max_file_upload" name="max_file_upload"
                                    value="{{ old('max_file_upload', $settings['max_file_upload'] ?? '10') }}"
                                    min="1" max="256">
                                @error('max_file_upload')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pagination_limit" class="form-label">Default Pagination Limit</label>
                                <select class="form-select @error('pagination_limit') is-invalid @enderror"
                                    id="pagination_limit" name="pagination_limit">
                                    <option value="10"
                                        {{ ($settings['pagination_limit'] ?? '15') == '10' ? 'selected' : '' }}>10</option>
                                    <option value="15"
                                        {{ ($settings['pagination_limit'] ?? '15') == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25"
                                        {{ ($settings['pagination_limit'] ?? '15') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50"
                                        {{ ($settings['pagination_limit'] ?? '15') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100"
                                        {{ ($settings['pagination_limit'] ?? '15') == '100' ? 'selected' : '' }}>100
                                    </option>
                                </select>
                                @error('pagination_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Performance Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-shield-check me-2 text-warning"></span>
                        Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.system.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="security">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password_min_length" class="form-label">Minimum Password Length</label>
                                <input type="number"
                                    class="form-control @error('password_min_length') is-invalid @enderror"
                                    id="password_min_length" name="password_min_length"
                                    value="{{ old('password_min_length', $settings['password_min_length'] ?? '8') }}"
                                    min="6" max="20">
                                @error('password_min_length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                <input type="number"
                                    class="form-control @error('max_login_attempts') is-invalid @enderror"
                                    id="max_login_attempts" name="max_login_attempts"
                                    value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? '5') }}"
                                    min="1" max="20">
                                @error('max_login_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of failed login attempts before account lockout
                                    (1-20)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lockout_duration" class="form-label">Lockout Duration</label>
                                <select class="form-select @error('lockout_duration') is-invalid @enderror"
                                    id="lockout_duration" name="lockout_duration">
                                    <option value="1"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '1' ? 'selected' : '' }}>
                                        1 minute</option>
                                    <option value="5"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '5' ? 'selected' : '' }}>
                                        5 minutes</option>
                                    <option value="10"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '10' ? 'selected' : '' }}>
                                        10 minutes</option>
                                    <option value="15"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '15' ? 'selected' : '' }}>
                                        15 minutes</option>
                                    <option value="30"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '30' ? 'selected' : '' }}>
                                        30 minutes</option>
                                    <option value="45"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '45' ? 'selected' : '' }}>
                                        45 minutes</option>
                                    <option value="60"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == '60' ? 'selected' : '' }}>
                                        1 hour</option>
                                    <option value="forever"
                                        {{ old('lockout_duration', $settings['lockout_duration'] ?? '15') == 'forever' ? 'selected' : '' }}>
                                        Forever (Permanent Ban)</option>
                                </select>
                                @error('lockout_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How long users are locked out after exceeding max login
                                    attempts</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="force_https" name="force_https"
                                        value="1" {{ $settings['force_https'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="force_https">
                                        Force HTTPS connections
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enable_two_factor"
                                        name="enable_two_factor" value="1"
                                        {{ $settings['enable_two_factor'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_two_factor">
                                        Enable Two-Factor Authentication
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Security Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Backup & Maintenance -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-database me-2 text-danger"></span>
                        Backup & Maintenance
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.system.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="maintenance">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="auto_backup" class="form-label">Automatic Backup</label>
                                <select class="form-select @error('auto_backup') is-invalid @enderror" id="auto_backup"
                                    name="auto_backup">
                                    <option value="disabled"
                                        {{ ($settings['auto_backup'] ?? 'disabled') == 'disabled' ? 'selected' : '' }}>
                                        Disabled</option>
                                    <option value="daily"
                                        {{ ($settings['auto_backup'] ?? 'disabled') == 'daily' ? 'selected' : '' }}>Daily
                                        (2:00 AM)</option>
                                    <option value="weekly"
                                        {{ ($settings['auto_backup'] ?? 'disabled') == 'weekly' ? 'selected' : '' }}>Weekly
                                        (Sundays 3:00 AM)</option>
                                    <option value="monthly"
                                        {{ ($settings['auto_backup'] ?? 'disabled') == 'monthly' ? 'selected' : '' }}>
                                        Monthly (1st day 4:00 AM)</option>
                                </select>
                                @error('auto_backup')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Automated database backups (requires cron job setup)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="backup_retention" class="form-label">Backup Retention (days)</label>
                                <input type="number" class="form-control @error('backup_retention') is-invalid @enderror"
                                    id="backup_retention" name="backup_retention"
                                    value="{{ old('backup_retention', $settings['backup_retention'] ?? '30') }}"
                                    min="1" max="365">
                                @error('backup_retention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How long to keep backup files before automatic cleanup (1-365
                                    days)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="log_level" class="form-label">Log Level</label>
                                <select class="form-select @error('log_level') is-invalid @enderror" id="log_level"
                                    name="log_level">
                                    <option value="emergency"
                                        {{ ($settings['log_level'] ?? 'error') == 'emergency' ? 'selected' : '' }}>
                                        Emergency (system unusable)</option>
                                    <option value="alert"
                                        {{ ($settings['log_level'] ?? 'error') == 'alert' ? 'selected' : '' }}>Alert
                                        (immediate action required)</option>
                                    <option value="critical"
                                        {{ ($settings['log_level'] ?? 'error') == 'critical' ? 'selected' : '' }}>Critical
                                        (critical conditions)</option>
                                    <option value="error"
                                        {{ ($settings['log_level'] ?? 'error') == 'error' ? 'selected' : '' }}>Error (error
                                        conditions) - Recommended</option>
                                    <option value="warning"
                                        {{ ($settings['log_level'] ?? 'error') == 'warning' ? 'selected' : '' }}>Warning
                                        (warning conditions)</option>
                                    <option value="notice"
                                        {{ ($settings['log_level'] ?? 'error') == 'notice' ? 'selected' : '' }}>Notice
                                        (normal but significant)</option>
                                    <option value="info"
                                        {{ ($settings['log_level'] ?? 'error') == 'info' ? 'selected' : '' }}>Info
                                        (informational messages)</option>
                                    <option value="debug"
                                        {{ ($settings['log_level'] ?? 'error') == 'debug' ? 'selected' : '' }}>Debug
                                        (detailed debugging - verbose)</option>
                                </select>
                                @error('log_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum severity level for logging (lower = fewer logs)</small>
                            </div>
                        </div>

                        <!-- Manual Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Manual Actions</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-warning" onclick="clearCache()">
                                        <span class="bi bi-trash me-2"></span>{{ __('Clear Cache') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-danger">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Maintenance Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Approval Settings -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-person-check me-2 text-success"></span>
                        User Approval Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.system.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="user_approval">

                        <!-- Approval Mode -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Approval Mode</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="user_approval_mode"
                                    id="approval_manual" value="manual"
                                    {{ ($settings['user_approval_mode'] ?? 'manual') === 'manual' ? 'checked' : '' }}
                                    onchange="toggleRoleSettings()">
                                <label class="form-check-label" for="approval_manual">
                                    <strong>Manual Approval</strong>
                                    <small class="text-muted d-block">Admin must approve each user registration</small>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="user_approval_mode"
                                    id="approval_email_verification" value="email_verification"
                                    {{ ($settings['user_approval_mode'] ?? 'manual') === 'email_verification' ? 'checked' : '' }}
                                    onchange="toggleRoleSettings()">
                                <label class="form-check-label" for="approval_email_verification">
                                    <strong>Email Verification</strong>
                                    <small class="text-muted d-block">Users are approved after verifying their email
                                        address</small>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="user_approval_mode"
                                    id="approval_otp" value="otp_approval"
                                    {{ ($settings['user_approval_mode'] ?? 'manual') === 'otp_approval' ? 'checked' : '' }}
                                    onchange="toggleRoleSettings()">
                                <label class="form-check-label" for="approval_otp">
                                    <strong>OTP Approval</strong>
                                    <small class="text-muted d-block">Users must verify a One-Time Password sent to their
                                        email/phone</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_approval_mode"
                                    id="approval_automatic" value="automatic"
                                    {{ ($settings['user_approval_mode'] ?? 'manual') === 'automatic' ? 'checked' : '' }}
                                    onchange="toggleRoleSettings()">
                                <label class="form-check-label" for="approval_automatic">
                                    <strong>Automatic Approval</strong>
                                    <small class="text-muted d-block">All users are approved immediately upon
                                        registration</small>
                                </label>
                            </div>
                        </div>

                        <!-- Role-Specific Auto-Approval (Manual Mode Only) -->
                        <div id="role_specific_settings"
                            style="display: {{ ($settings['user_approval_mode'] ?? 'manual') === 'manual' ? 'block' : 'none' }}">
                            <div class="border-top pt-3 mb-3">
                                <label class="form-label fw-semibold">Role-Specific Auto-Approval</label>
                                <small class="text-muted d-block mb-2">Auto-approve specific user roles without manual
                                    review</small>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="auto_approve_teachers"
                                        name="auto_approve_teachers" value="1"
                                        {{ $settings['auto_approve_teachers'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_approve_teachers">
                                        Auto-approve Teachers
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="auto_approve_students"
                                        name="auto_approve_students" value="1"
                                        {{ $settings['auto_approve_students'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_approve_students">
                                        Auto-approve Students
                                    </label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_approve_parents"
                                        name="auto_approve_parents" value="1"
                                        {{ $settings['auto_approve_parents'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_approve_parents">
                                        Auto-approve Parents
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="border-top pt-3">
                            <label class="form-label fw-semibold">Notifications</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="send_approval_notifications"
                                    name="send_approval_notifications" value="1"
                                    {{ $settings['send_approval_notifications'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_approval_notifications">
                                    Send email notifications when users are approved or rejected
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Approval Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-info-circle me-2 text-info"></span>
                        System Settings Help
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary">
                        <h6>System Information</h6>
                        <p class="mb-0">Configure account verification status and two-factor authentication settings.</p>
                    </div>

                    <div class="alert alert-info">
                        <h6>Performance Settings</h6>
                        <p class="mb-0">Configure caching, sessions, and file upload limits to optimize system
                            performance.</p>
                    </div>

                    <div class="alert alert-warning">
                        <h6>Security Settings</h6>
                        <p class="mb-0">Configure password policies, login security, and access controls.</p>
                    </div>

                    <div class="alert alert-danger">
                        <h6>Backup & Maintenance</h6>
                        <p class="mb-0">Set up automatic backups and configure system maintenance options.</p>
                    </div>

                    <div class="alert alert-success">
                        <h6>User Approval Settings</h6>
                        <p class="mb-0">Control how new users are approved: manually by admin, after email verification,
                            or automatically.</p>
                    </div>
                </div>
            </div>

            <!-- Current Settings Summary -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-bar-chart me-2 text-primary"></span>
                        Current Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Account Status</small>
                        <div class="fw-bold">
                            <span
                                class="badge bg-{{ ($settings['account_status'] ?? 'verified') === 'verified' ? 'success' : 'warning' }}">
                                {{ ucfirst($settings['account_status'] ?? 'verified') }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Two-Factor Auth</small>
                        <div class="fw-bold">
                            <span
                                class="badge bg-{{ $settings['enable_two_factor_auth'] ?? false ? 'success' : 'secondary' }}">
                                {{ $settings['enable_two_factor_auth'] ?? false ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Cache Driver</small>
                        <div class="fw-bold">{{ ucfirst($settings['cache_driver'] ?? 'File') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Password Min Length</small>
                        <div class="fw-bold">{{ $settings['password_min_length'] ?? '8' }} characters</div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Auto Backup</small>
                        <div class="fw-bold">{{ ucfirst($settings['auto_backup'] ?? 'Disabled') }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-lightning me-2 text-primary"></span>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.settings.admin.general') }}" class="btn btn-outline-primary">
                            <span class="bi bi-gear me-2"></span>{{ __('General Settings') }}
                        </a>
                        <a href="{{ route('tenant.settings.admin.academic') }}" class="btn btn-outline-success">
                            <span class="bi bi-mortarboard me-2"></span>{{ __('Academic Settings') }}
                        </a>
                        <a href="{{ route('tenant.settings.admin.mail') }}" class="btn btn-outline-info">
                            <span class="bi bi-envelope me-2"></span>{{ __('Mail Settings') }}
                        </a>
                        <a href="{{ route('tenant.settings.admin.messaging') }}" class="btn btn-outline-warning">
                            <span class="bi bi-chat-dots me-2"></span>{{ __('Messaging') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle Account Status Switch
        document.getElementById('account_status').addEventListener('change', function() {
            const badge = document.getElementById('account-status-badge');

            // Update badge
            if (this.checked) {
                badge.className = 'badge bg-success';
                badge.textContent = 'Verified';
            } else {
                badge.className = 'badge bg-warning';
                badge.textContent = 'Unverified';
            }
        });

        // Handle Two-Factor Authentication Switch
        document.getElementById('enable_two_factor_auth').addEventListener('change', function() {
            const badge = document.getElementById('two-factor-badge');

            // Update badge
            if (this.checked) {
                badge.className = 'badge bg-success';
                badge.textContent = 'Enabled';
            } else {
                badge.className = 'badge bg-secondary';
                badge.textContent = 'Disabled';
            }
        });

        function toggleRoleSettings() {
            const manualMode = document.getElementById('approval_manual').checked;
            const roleSettings = document.getElementById('role_specific_settings');

            if (manualMode) {
                roleSettings.style.display = 'block';
            } else {
                roleSettings.style.display = 'none';
            }
        }

        function clearCache() {
            if (!confirm('Are you sure you want to clear the cache?')) return;

            const button = event.target.closest('button');
            const originalText = button.innerHTML;

            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<span class="bi bi-arrow-repeat spinning me-2"></span>Clearing...';

            // Make AJAX request
            fetch('{{ route('tenant.settings.admin.system.clear-cache') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cache cleared successfully!');
                    } else {
                        alert('Failed to clear cache: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while clearing cache.');
                })
                .finally(() => {
                    // Re-enable button and restore original text
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        }
    </script>
@endsection
