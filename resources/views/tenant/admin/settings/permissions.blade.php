@extends('tenant.layouts.app')

@section('title', __('Permissions Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Permissions Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.permissions.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Role-Based Access Control -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Role-Based Access Control') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="enable_rbac" class="form-label">{{ __('Enable Role-Based Access Control') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_rbac" name="enable_rbac" value="1"
                                           {{ old('enable_rbac', setting('enable_rbac', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_rbac">
                                        {{ __('Restrict access based on user roles') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="default_role" class="form-label">{{ __('Default Role for New Users') }}</label>
                                <select class="form-select @error('default_role') is-invalid @enderror" id="default_role" name="default_role">
                                    <option value="student" {{ old('default_role', setting('default_role', 'student')) == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="parent" {{ old('default_role', setting('default_role', 'student')) == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="staff" {{ old('default_role', setting('default_role', 'student')) == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('default_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permission Categories -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Permission Categories') }}</h5>
                                <div class="accordion" id="permissionsAccordion">
                                    <!-- Student Permissions -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#studentPermissions">
                                                {{ __('Student Permissions') }}
                                            </button>
                                        </h2>
                                        <div id="studentPermissions" class="accordion-collapse collapse show" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="student_view_profile" name="permissions[student][]" value="view_profile"
                                                                   {{ in_array('view_profile', old('permissions.student', setting('permissions.student', ['view_profile', 'view_grades', 'view_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="student_view_profile">{{ __('View own profile') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="student_view_grades" name="permissions[student][]" value="view_grades"
                                                                   {{ in_array('view_grades', old('permissions.student', setting('permissions.student', ['view_profile', 'view_grades', 'view_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="student_view_grades">{{ __('View grades') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="student_view_attendance" name="permissions[student][]" value="view_attendance"
                                                                   {{ in_array('view_attendance', old('permissions.student', setting('permissions.student', ['view_profile', 'view_grades', 'view_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="student_view_attendance">{{ __('View attendance') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="student_download_reports" name="permissions[student][]" value="download_reports"
                                                                   {{ in_array('download_reports', old('permissions.student', setting('permissions.student', ['view_profile', 'view_grades', 'view_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="student_download_reports">{{ __('Download reports') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="student_view_timetable" name="permissions[student][]" value="view_timetable"
                                                                   {{ in_array('view_timetable', old('permissions.student', setting('permissions.student', ['view_profile', 'view_grades', 'view_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="student_view_timetable">{{ __('View timetable') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Parent Permissions -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#parentPermissions">
                                                {{ __('Parent Permissions') }}
                                            </button>
                                        </h2>
                                        <div id="parentPermissions" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="parent_view_children" name="permissions[parent][]" value="view_children"
                                                                   {{ in_array('view_children', old('permissions.parent', setting('permissions.parent', ['view_children', 'view_grades', 'view_attendance', 'pay_fees']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="parent_view_children">{{ __('View children profiles') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="parent_view_grades" name="permissions[parent][]" value="view_grades"
                                                                   {{ in_array('view_grades', old('permissions.parent', setting('permissions.parent', ['view_children', 'view_grades', 'view_attendance', 'pay_fees']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="parent_view_grades">{{ __('View children grades') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="parent_view_attendance" name="permissions[parent][]" value="view_attendance"
                                                                   {{ in_array('view_attendance', old('permissions.parent', setting('permissions.parent', ['view_children', 'view_grades', 'view_attendance', 'pay_fees']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="parent_view_attendance">{{ __('View children attendance') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="parent_pay_fees" name="permissions[parent][]" value="pay_fees"
                                                                   {{ in_array('pay_fees', old('permissions.parent', setting('permissions.parent', ['view_children', 'view_grades', 'view_attendance', 'pay_fees']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="parent_pay_fees">{{ __('Pay school fees') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="parent_communicate" name="permissions[parent][]" value="communicate"
                                                                   {{ in_array('communicate', old('permissions.parent', setting('permissions.parent', ['view_children', 'view_grades', 'view_attendance', 'pay_fees']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="parent_communicate">{{ __('Communicate with teachers') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Staff Permissions -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#staffPermissions">
                                                {{ __('Staff Permissions') }}
                                            </button>
                                        </h2>
                                        <div id="staffPermissions" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="staff_manage_students" name="permissions[staff][]" value="manage_students"
                                                                   {{ in_array('manage_students', old('permissions.staff', setting('permissions.staff', ['manage_students', 'view_reports', 'manage_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="staff_manage_students">{{ __('Manage students') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="staff_manage_grades" name="permissions[staff][]" value="manage_grades"
                                                                   {{ in_array('manage_grades', old('permissions.staff', setting('permissions.staff', ['manage_students', 'view_reports', 'manage_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="staff_manage_grades">{{ __('Manage grades') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="staff_manage_attendance" name="permissions[staff][]" value="manage_attendance"
                                                                   {{ in_array('manage_attendance', old('permissions.staff', setting('permissions.staff', ['manage_students', 'view_reports', 'manage_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="staff_manage_attendance">{{ __('Manage attendance') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="staff_view_reports" name="permissions[staff][]" value="view_reports"
                                                                   {{ in_array('view_reports', old('permissions.staff', setting('permissions.staff', ['manage_students', 'view_reports', 'manage_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="staff_view_reports">{{ __('View reports') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="staff_manage_timetable" name="permissions[staff][]" value="manage_timetable"
                                                                   {{ in_array('manage_timetable', old('permissions.staff', setting('permissions.staff', ['manage_students', 'view_reports', 'manage_attendance']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="staff_manage_timetable">{{ __('Manage timetable') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Admin Permissions -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adminPermissions">
                                                {{ __('Admin Permissions') }}
                                            </button>
                                        </h2>
                                        <div id="adminPermissions" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="admin_manage_users" name="permissions[admin][]" value="manage_users"
                                                                   {{ in_array('manage_users', old('permissions.admin', setting('permissions.admin', ['manage_users', 'manage_settings', 'manage_finances', 'view_all_reports']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="admin_manage_users">{{ __('Manage all users') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="admin_manage_settings" name="permissions[admin][]" value="manage_settings"
                                                                   {{ in_array('manage_settings', old('permissions.admin', setting('permissions.admin', ['manage_users', 'manage_settings', 'manage_finances', 'view_all_reports']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="admin_manage_settings">{{ __('Manage system settings') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="admin_manage_finances" name="permissions[admin][]" value="manage_finances"
                                                                   {{ in_array('manage_finances', old('permissions.admin', setting('permissions.admin', ['manage_users', 'manage_settings', 'manage_finances', 'view_all_reports']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="admin_manage_finances">{{ __('Manage finances') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="admin_view_all_reports" name="permissions[admin][]" value="view_all_reports"
                                                                   {{ in_array('view_all_reports', old('permissions.admin', setting('permissions.admin', ['manage_users', 'manage_settings', 'manage_finances', 'view_all_reports']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="admin_view_all_reports">{{ __('View all reports') }}</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="admin_system_backup" name="permissions[admin][]" value="system_backup"
                                                                   {{ in_array('system_backup', old('permissions.admin', setting('permissions.admin', ['manage_users', 'manage_settings', 'manage_finances', 'view_all_reports']))) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="admin_system_backup">{{ __('System backup and restore') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permission Inheritance -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Permission Inheritance') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="inherit_parent_permissions" class="form-label">{{ __('Inherit Parent Permissions') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="inherit_parent_permissions" name="inherit_parent_permissions" value="1"
                                           {{ old('inherit_parent_permissions', setting('inherit_parent_permissions', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inherit_parent_permissions">
                                        {{ __('Child roles inherit parent role permissions') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save Permissions Settings') }}
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