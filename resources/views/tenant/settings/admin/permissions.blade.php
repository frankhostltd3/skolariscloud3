@extends('tenant.layouts.app')

@section('title', 'Permissions & Access Control')

@section('content')
    <style>
        .role-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        .permission-card {
            transition: all 0.3s;
        }

        .permission-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">
                <span class="bi bi-shield-lock me-2"></span>
                {{ __('Permissions & Access Control') }}
            </h1>
            <p class="text-muted mb-0">{{ __('Manage user roles, permissions, and security settings across the system') }}
            </p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#syncRegistryModal">
                <i class="bi bi-arrow-repeat me-2"></i>Sync Registry
            </button>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkAssignRoleModal">
                <i class="bi bi-people-fill me-2"></i>Bulk Assign
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="bi bi-plus-circle me-2"></i>Create Role
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Roles & Permissions Management -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people-fill me-2 text-primary"></i>
                        User Roles Management
                    </h5>
                    <span class="badge bg-info">{{ $roles->count() }} Roles</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Role Name</th>
                                    <th>Users</th>
                                    <th>Permissions</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-shield-fill me-2 text-primary"></i>
                                                <div>
                                                    <div class="fw-bold">{{ ucfirst($role->name) }}</div>
                                                    <small class="text-muted">{{ $role->guard_name }} guard</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $role->users_count }} users</span>
                                        </td>
                                        <td>
                                            @php
                                                $permCount = $role->permissions()->count();
                                            @endphp
                                            <span class="badge {{ $permCount > 0 ? 'bg-success' : 'bg-warning' }}">
                                                {{ $permCount }} permissions
                                            </span>
                                        </td>
                                        <td>
                                            @if (in_array($role->name, ['super-admin', 'admin', 'teacher', 'student', 'parent']))
                                                <span class="badge bg-primary">System Role</span>
                                            @else
                                                <span class="badge bg-info">Custom Role</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary"
                                                    onclick="editRolePermissions('{{ $role->id }}', '{{ $role->name }}')"
                                                    data-bs-toggle="modal" data-bs-target="#editPermissionsModal">
                                                    <i class="bi bi-key"></i> Permissions
                                                </button>
                                                @if (!in_array($role->name, ['super-admin', 'admin']))
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="confirmDeleteRole('{{ $role->id }}', '{{ $role->name }}')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">No roles found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Access Control Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lock-fill me-2 text-warning"></i>
                        Access Control & Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ url('/settings/admin/permissions') }}" method="POST">
                        @csrf

                        <!-- Default Role Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-person-badge me-2"></i>Default Role Assignment
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Default Student Role</label>
                                <select name="default_student_role"
                                    class="form-select @error('default_student_role') is-invalid @enderror">
                                    <option value="student"
                                        {{ ($settings['default_student_role'] ?? 'student') == 'student' ? 'selected' : '' }}>
                                        Student</option>
                                    <option value="prefect"
                                        {{ ($settings['default_student_role'] ?? 'student') == 'prefect' ? 'selected' : '' }}>
                                        Prefect</option>
                                    <option value="monitor"
                                        {{ ($settings['default_student_role'] ?? 'student') == 'monitor' ? 'selected' : '' }}>
                                        Class Monitor</option>
                                </select>
                                <small class="text-muted">Role automatically assigned to new students</small>
                                @error('default_student_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Default Teacher Role</label>
                                <select name="default_teacher_role"
                                    class="form-select @error('default_teacher_role') is-invalid @enderror">
                                    <option value="teacher"
                                        {{ ($settings['default_teacher_role'] ?? 'teacher') == 'teacher' ? 'selected' : '' }}>
                                        Teacher</option>
                                    <option value="hod"
                                        {{ ($settings['default_teacher_role'] ?? 'teacher') == 'hod' ? 'selected' : '' }}>
                                        Head of Department</option>
                                    <option value="deputy"
                                        {{ ($settings['default_teacher_role'] ?? 'teacher') == 'deputy' ? 'selected' : '' }}>
                                        Deputy Principal</option>
                                </select>
                                <small class="text-muted">Role automatically assigned to new teachers</small>
                                @error('default_teacher_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Login & Authentication Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-door-open me-2"></i>Login & Authentication Settings
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="allow_student_login" name="allow_student_login" value="1"
                                            {{ $settings['allow_student_login'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="allow_student_login">
                                            Allow Student Login
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Students can access their portal</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="allow_parent_login" name="allow_parent_login" value="1"
                                            {{ $settings['allow_parent_login'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="allow_parent_login">
                                            Allow Parent Login
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Parents can view student information</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="allow_teacher_login" name="allow_teacher_login" value="1"
                                            {{ $settings['allow_teacher_login'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="allow_teacher_login">
                                            Allow Teacher Login
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Teachers can access teaching portal</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="require_email_verification" name="require_email_verification"
                                            value="1"
                                            {{ $settings['require_email_verification'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="require_email_verification">
                                            Require Email Verification
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Users must verify email before login</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="allow_password_reset" name="allow_password_reset" value="1"
                                            {{ $settings['allow_password_reset'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="allow_password_reset">
                                            Allow Password Reset
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Users can reset forgotten passwords</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="enable_two_factor" name="enable_two_factor" value="1"
                                            {{ $settings['enable_two_factor'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="enable_two_factor">
                                            Enable Two-Factor Authentication
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Enhanced security with 2FA</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="allow_registration" name="allow_registration" value="1"
                                            {{ $settings['allow_registration'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="allow_registration">
                                            Allow Self Registration
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Users can create accounts themselves</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="require_strong_password" name="require_strong_password" value="1"
                                            {{ $settings['require_strong_password'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="require_strong_password">
                                            Require Strong Passwords
                                        </label>
                                    </div>
                                    <small class="text-muted ms-4">Enforce password complexity rules</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Password & Security Policy -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-shield-check me-2"></i>Password & Security Policy
                                </h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Minimum Password Length</label>
                                <input type="number" name="min_password_length"
                                    class="form-control @error('min_password_length') is-invalid @enderror"
                                    value="{{ old('min_password_length', $settings['min_password_length'] ?? '10') }}"
                                    min="6" max="32">
                                <small class="text-muted">Recommended: 10-12 characters</small>
                                @error('min_password_length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Password Expiry (Days)</label>
                                <input type="number" name="password_expiry_days"
                                    class="form-control @error('password_expiry_days') is-invalid @enderror"
                                    value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? '90') }}"
                                    min="0" max="365">
                                <small class="text-muted">0 = Never expires</small>
                                @error('password_expiry_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Max Login Attempts</label>
                                <input type="number" name="max_login_attempts"
                                    class="form-control @error('max_login_attempts') is-invalid @enderror"
                                    value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? '5') }}"
                                    min="1" max="20">
                                <small class="text-muted">Before account lockout</small>
                                @error('max_login_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Session Management -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-clock-history me-2"></i>Session Management
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Session Timeout (Minutes)</label>
                                <input type="number" name="session_timeout"
                                    class="form-control @error('session_timeout') is-invalid @enderror"
                                    value="{{ old('session_timeout', $settings['session_timeout'] ?? '60') }}"
                                    min="5" max="480">
                                <small class="text-muted">Auto-logout after inactivity</small>
                                @error('session_timeout')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Remember Me Duration (Days)</label>
                                <input type="number" name="remember_me_days"
                                    class="form-control @error('remember_me_days') is-invalid @enderror"
                                    value="{{ old('remember_me_days', $settings['remember_me_days'] ?? '30') }}"
                                    min="1" max="365">
                                <small class="text-muted">Keep users logged in</small>
                                @error('remember_me_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- IP Restrictions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-globe me-2"></i>IP Access Restrictions
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="restrict_by_ip"
                                        name="restrict_by_ip" value="1"
                                        {{ $settings['restrict_by_ip'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="restrict_by_ip">
                                        Enable IP Restrictions
                                    </label>
                                </div>
                                <small class="text-muted">Limit access to specific IP addresses</small>
                            </div>
                            <div class="col-md-6 mb-3" id="ip_whitelist_section"
                                style="display: {{ $settings['restrict_by_ip'] ?? false ? 'block' : 'none' }}">
                                <label class="form-label fw-semibold">Allowed IP Addresses</label>
                                <textarea name="allowed_ips" rows="4" class="form-control @error('allowed_ips') is-invalid @enderror"
                                    placeholder="One IP per line&#10;Example:&#10;192.168.1.100&#10;10.0.0.0/24&#10;203.0.113.*">{{ old('allowed_ips', $settings['allowed_ips'] ?? '') }}</textarea>
                                <small class="text-muted">Supports CIDR notation and wildcards</small>
                                @error('allowed_ips')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Role-Based Feature Access -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-toggles me-2"></i>Role-Based Feature Access
                                </h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="teacher_manage_students" name="teacher_manage_students" value="1"
                                        {{ $settings['teacher_manage_students'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="teacher_manage_students">
                                        Teachers Can Manage Students
                                    </label>
                                </div>
                                <small class="text-muted">Add, edit, delete students</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="teacher_manage_classes" name="teacher_manage_classes" value="1"
                                        {{ $settings['teacher_manage_classes'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="teacher_manage_classes">
                                        Teachers Can Manage Classes
                                    </label>
                                </div>
                                <small class="text-muted">Create and modify classes</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="student_view_reports" name="student_view_reports" value="1"
                                        {{ $settings['student_view_reports'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="student_view_reports">
                                        Students Can View Reports
                                    </label>
                                </div>
                                <small class="text-muted">Access their own reports</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Reset Changes
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save All Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Permission Groups Overview -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-grid-3x3-gap me-2 text-success"></i>
                        System Permissions Overview
                    </h5>
                </div>
                <div class="card-body">
                    @if ($permissionGroups->count() > 0)
                        <div class="row g-3">
                            @foreach ($permissionGroups as $groupName => $permissions)
                                <div class="col-md-4">
                                    <div class="card permission-card h-100 border">
                                        <div class="card-body">
                                            <h6 class="text-primary mb-3">
                                                <i class="bi bi-folder2-open me-2"></i>{{ $groupName }}
                                            </h6>
                                            <div class="small">
                                                @foreach ($permissions as $permission)
                                                    <div class="mb-1">
                                                        <i class="bi bi-check-circle text-success me-1"></i>
                                                        {{ str_replace('_', ' ', ucfirst($permission->name)) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-muted">{{ count($permissions) }} permissions</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No permissions defined yet. Run the permissions seeder to populate
                                system permissions.</p>
                            <p class="text-muted"><code>php artisan db:seed --class=PermissionsSeeder</code></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Current Status Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2 text-info"></i>
                        Security Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Student Login</small>
                            <span
                                class="badge {{ $settings['allow_student_login'] ?? true ? 'bg-success' : 'bg-danger' }}">
                                {{ $settings['allow_student_login'] ?? true ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Email Verification</small>
                            <span
                                class="badge {{ $settings['require_email_verification'] ?? false ? 'bg-warning' : 'bg-secondary' }}">
                                {{ $settings['require_email_verification'] ?? false ? 'Required' : 'Optional' }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Two-Factor Auth</small>
                            <span
                                class="badge {{ $settings['enable_two_factor'] ?? false ? 'bg-success' : 'bg-secondary' }}">
                                {{ $settings['enable_two_factor'] ?? false ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Strong Passwords</small>
                            <span
                                class="badge {{ $settings['require_strong_password'] ?? true ? 'bg-success' : 'bg-danger' }}">
                                {{ $settings['require_strong_password'] ?? true ? 'Required' : 'Optional' }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Min Password Length</small>
                        <div class="fw-bold">{{ $settings['min_password_length'] ?? '8' }} characters</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Session Timeout</small>
                        <div class="fw-bold">{{ $settings['session_timeout'] ?? '60' }} minutes</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Password Expiry</small>
                        <div class="fw-bold">
                            {{ ($settings['password_expiry_days'] ?? 90) > 0 ? $settings['password_expiry_days'] . ' days' : 'Never' }}
                        </div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">IP Restrictions</small>
                        <div class="fw-bold">{{ $settings['restrict_by_ip'] ?? false ? 'Enabled' : 'Disabled' }}</div>
                    </div>
                </div>
            </div>

            <!-- Security Recommendations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>
                        Security Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    @if (!($settings['enable_two_factor'] ?? false))
                        <div class="alert alert-warning alert-sm mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small><strong>Enable 2FA</strong> for admin accounts for enhanced security</small>
                        </div>
                    @endif

                    @if (($settings['min_password_length'] ?? 8) < 10)
                        <div class="alert alert-warning alert-sm mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small><strong>Increase minimum password length</strong> to 10+ characters</small>
                        </div>
                    @endif

                    @if (($settings['password_expiry_days'] ?? 90) == 0 || ($settings['password_expiry_days'] ?? 90) > 90)
                        <div class="alert alert-info alert-sm mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <small><strong>Consider password expiry</strong> of 90 days for better security</small>
                        </div>
                    @endif

                    <div class="alert alert-success alert-sm mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        <small><strong>Tip:</strong> Review permissions quarterly and remove unused roles</small>
                    </div>
                </div>
            </div>

            <!-- Help & Documentation -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-question-circle me-2 text-info"></i>
                        Help & Documentation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="small fw-bold">User Roles</h6>
                        <p class="small text-muted mb-0">Roles define what users can see and do. Assign users to roles,
                            then assign permissions to roles.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="small fw-bold">Permissions</h6>
                        <p class="small text-muted mb-0">Granular controls for specific actions. Best practice: assign
                            permissions to roles, not individual users.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="small fw-bold">Access Control</h6>
                        <p class="small text-muted mb-0">Configure login requirements, session timeouts, and IP
                            restrictions for enhanced security.</p>
                    </div>
                    <div class="mb-0">
                        <h6 class="small fw-bold">Best Practices</h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Use principle of least privilege</li>
                            <li>Regular permission audits</li>
                            <li>Enable 2FA for admins</li>
                            <li>Monitor failed login attempts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/settings/admin/roles') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Create New Role
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., librarian"
                                required>
                            <small class="text-muted">Lowercase, no spaces (use hyphens or underscores)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="display_name" class="form-control" placeholder="e.g., Librarian"
                                required>
                            <small class="text-muted">Human-readable name shown to users</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="What can this role do?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Permissions Modal -->
    <div class="modal fade" id="editPermissionsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editPermissionsForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-key me-2"></i>Edit Role Permissions: <span id="editRoleName"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Select the permissions this role should have. Changes apply to all users with this role.
                        </div>
                        <div id="permissionsCheckboxes"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sync Registry Modal -->
    <div class="modal fade" id="syncRegistryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/settings/admin/permissions/sync-registry') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-arrow-repeat me-2"></i>Sync User Registry
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">This will synchronize user roles with the latest registry data. Proceed with
                            caution.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Role to Sync</label>
                            <select name="sync_role" class="form-select @error('sync_role') is-invalid @enderror"
                                required>
                                <option value="">-- Select a role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('sync_role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="notify_sync_complete"
                                    name="notify_sync_complete" value="1" checked>
                                <label class="form-check-label fw-semibold" for="notify_sync_complete">
                                    Notify me when sync is complete
                                </label>
                            </div>
                            <small class="text-muted">Receive an email notification after the sync process</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-repeat me-2"></i>Start Sync
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Assign Role Modal -->
    <div class="modal fade" id="bulkAssignRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/settings/admin/roles/bulk-assign') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-people-fill me-2"></i>Bulk Assign Roles
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Assign selected role to multiple users at once.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Role</label>
                            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">-- Select a role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">User Emails (one per line)</label>
                            <textarea name="user_emails" rows="4" class="form-control @error('user_emails') is-invalid @enderror"
                                placeholder="user@example.com&#10;anotheruser@example.com"></textarea>
                            @error('user_emails')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="notify_bulk_assign_complete" name="notify_bulk_assign_complete" value="1"
                                    checked>
                                <label class="form-check-label fw-semibold" for="notify_bulk_assign_complete">
                                    Notify me when assignment is complete
                                </label>
                            </div>
                            <small class="text-muted">Receive an email notification after the bulk assignment</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-people-fill me-2"></i>Assign Roles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle IP restrictions visibility
        document.getElementById('restrict_by_ip')?.addEventListener('change', function() {
            document.getElementById('ip_whitelist_section').style.display = this.checked ? 'block' : 'none';
        });

        // Edit role permissions
        function editRolePermissions(roleId, roleName) {
            document.getElementById('editRoleName').textContent = roleName;
            document.getElementById('editPermissionsForm').action = `/settings/admin/roles/${roleId}/permissions`;

            // Load role permissions via AJAX
            fetch(`/settings/admin/roles/${roleId}/permissions`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('permissionsCheckboxes');
                    container.innerHTML = '';

                    @foreach ($permissionGroups as $groupName => $permissions)
                        const group{{ $loop->index }} = document.createElement('div');
                        group{{ $loop->index }}.className = 'mb-4';
                        group{{ $loop->index }}.innerHTML = `
                <h6 class="text-primary border-bottom pb-2">{{ $groupName }}</h6>
                <div class="row">
                    @foreach ($permissions as $permission)
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                   value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                   ${data.permissions.includes('{{ $permission->name }}') ? 'checked' : ''}>
                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                {{ str_replace('_', ' ', ucfirst($permission->name)) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            `;
                        container.appendChild(group{{ $loop->index }});
                    @endforeach
                })
                .catch(error => {
                    console.error('Error loading permissions:', error);
                    alert('Failed to load permissions. Please try again.');
                });
        }

        // Confirm delete role
        function confirmDeleteRole(roleId, roleName) {
            if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/settings/admin/roles/${roleId}`;
                form.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
    </script>
@endsection

