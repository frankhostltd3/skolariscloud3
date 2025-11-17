@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">{{ __('Edit Employee') }}</h1>
    @if($employee->user)
      <span class="badge bg-success">
        <i class="bi bi-person-check"></i> Has User Account
      </span>
    @else
      <div>
        <span class="badge bg-warning text-dark me-2">
          <i class="bi bi-person-x"></i> No User Account
        </span>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
          <i class="bi bi-person-plus"></i> Create User Account
        </button>
      </div>
    @endif
  </div>

  @if(!$employee->user)
    <!-- Create User Account Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createUserModalLabel">
              <i class="bi bi-person-plus"></i> Create User Account for {{ $employee->full_name }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="{{ route('tenant.modules.human_resources.employees.create-user-account', $employee) }}">
            @csrf
            <div class="modal-body">
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                This will create a user account that allows <strong>{{ $employee->full_name }}</strong> to login to the system.
              </div>

              <div class="mb-3">
                <label class="form-label">Employee</label>
                <input type="text" class="form-control" value="{{ $employee->full_name }} ({{ $employee->employee_number }})" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="{{ $employee->email }}" disabled>
              </div>

              <div class="mb-3">
                <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required>
                  <option value="Staff" {{ $employee->is_teacher ? '' : 'selected' }}>Staff</option>
                  <option value="Teacher" {{ $employee->is_teacher ? 'selected' : '' }}>Teacher</option>
                  <option value="Admin">Admin</option>
                </select>
                <small class="form-text text-muted">
                  @if($employee->is_teacher)
                    This employee is marked as a teacher, so "Teacher" role is pre-selected.
                  @else
                    Default role is "Staff". Change if needed.
                  @endif
                </small>
              </div>

              <div class="mb-3">
                <label class="form-label">Default Password</label>
                <input type="text" class="form-control" value="Welcome123!" disabled>
                <small class="form-text text-muted">User will be required to change this password after first login (expires in 7 days).</small>
              </div>

              <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Note:</strong>
                <ul class="mb-0 mt-2 small">
                  <li>Default password: <strong>Welcome123!</strong></li>
                  <li>Password expires in 7 days</li>
                  <li>User will need to change password after first login</li>
                  <li>Login email: <strong>{{ $employee->email }}</strong></li>
                </ul>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Create User Account
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <!-- Nav Tabs -->
  <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="employee-info-tab" data-bs-toggle="tab" data-bs-target="#employee-info" type="button" role="tab">
        <i class="bi bi-person-badge"></i> Employee Information
      </button>
    </li>
    @if($employee->user)
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
          <i class="bi bi-key"></i> Password Management
        </button>
      </li>
    @endif
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="employeeTabsContent">
    <!-- Employee Information Tab -->
    <div class="tab-pane fade show active" id="employee-info" role="tabpanel">
      <form method="POST" action="{{ route('tenant.modules.human_resources.employees.update', $employee) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0 small">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
      </div>
      <div class="col-md-6">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="national_id" class="form-label">National ID</label>
        <input type="text" class="form-control" id="national_id" name="national_id" value="{{ old('national_id', $employee->national_id) }}">
      </div>
      <div class="col-md-4">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" id="gender" name="gender">
          <option value="">Select</option>
          <option value="male" {{ old('gender', $employee->gender)==='male' ? 'selected' : '' }}>Male</option>
          <option value="female" {{ old('gender', $employee->gender)==='female' ? 'selected' : '' }}>Female</option>
          <option value="other" {{ old('gender', $employee->gender)==='other' ? 'selected' : '' }}>Other</option>
        </select>
      </div>
      <div class="col-md-4">
        <label for="passport_photo" class="form-label">Passport Photo</label>
        <input type="file" class="form-control" id="passport_photo" name="passport_photo" accept="image/*">
        <div class="form-text">Max 130MB, 256x256px. Leave empty to keep current.</div>
        @if($employee->photo_path)
          <div class="mt-2">
            <img src="{{ asset('storage/'.$employee->photo_path) }}" alt="Current Photo" class="rounded border" style="width:64px;height:64px;object-fit:cover;">
          </div>
        @endif
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Employee Number</label>
      <input type="text" class="form-control" value="{{ $employee->employee_number ?? 'Will be auto-generated' }}" disabled>
      <div class="form-text">Employee number is auto-generated and cannot be changed.</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}">
      </div>
      <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="employee_type" class="form-label">Employee Type</label>
        <select class="form-select" id="employee_type" name="employee_type" required>
          <option value="">Select Type</option>
          <option value="full_time" {{ old('employee_type', $employee->employee_type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
          <option value="part_time" {{ old('employee_type', $employee->employee_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
          <option value="intern" {{ old('employee_type', $employee->employee_type) === 'intern' ? 'selected' : '' }}>Intern</option>
          <option value="contract" {{ old('employee_type', $employee->employee_type) === 'contract' ? 'selected' : '' }}>Contract</option>
        </select>
      </div>
      <div class="col-md-4">
        <label for="salary_scale_id" class="form-label">Salary Scale</label>
        <select class="form-select" id="salary_scale_id" name="salary_scale_id">
          <option value="">Select Salary Scale</option>
          @foreach($salary_scales ?? [] as $scale)
            <option value="{{ $scale->id }}" {{ old('salary_scale_id', $employee->salary_scale_id) == $scale->id ? 'selected' : '' }}>{{ $scale->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label for="department_id" class="form-label">Department</label>
        <select class="form-select" id="department_id" name="department_id" required>
          <option value="">Select Department</option>
          @foreach($departments ?? [] as $department)
            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="position_id" class="form-label">Position</label>
        <select class="form-select" id="position_id" name="position_id" required>
          <option value="">Select Position</option>
          @foreach($positions ?? [] as $position)
            <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>{{ $position->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label for="employment_status" class="form-label">Employment Status</label>
        <select class="form-select" id="employment_status" name="employment_status">
          <option value="">Select Status</option>
          <option value="active" {{ old('employment_status', $employee->employment_status) === 'active' ? 'selected' : '' }}>Active</option>
          <option value="probation" {{ old('employment_status', $employee->employment_status) === 'probation' ? 'selected' : '' }}>Probation</option>
          <option value="on_leave" {{ old('employment_status', $employee->employment_status) === 'on_leave' ? 'selected' : '' }}>On Leave</option>
          <option value="inactive" {{ old('employment_status', $employee->employment_status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="suspended" {{ old('employment_status', $employee->employment_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
          <option value="terminated" {{ old('employment_status', $employee->employment_status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="hire_date" class="form-label">Hire Date</label>
        <input type="date" class="form-control" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}">
      </div>
      <div class="col-md-6">
        <label for="birth_date" class="form-label">Birth Date</label>
        <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $employee->birth_date?->format('Y-m-d')) }}">
      </div>
    </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="submitText">Update Employee</span>
        </button>
        <a href="{{ route('tenant.modules.human_resources.employees.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
    <!-- End Employee Information Tab -->

    <!-- Password Management Tab -->
    @if($employee->user)
      <div class="tab-pane fade" id="password" role="tabpanel">
        <div class="row">
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                  <i class="bi bi-shield-lock"></i> Password Management for {{ $employee->full_name }}
                </h5>
              </div>
              <div class="card-body">
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i>
                  <strong>User Account Information:</strong><br>
                  <strong>Email:</strong> {{ $employee->user->email }}<br>
                  <strong>Role:</strong> 
                  @foreach($employee->user->roles as $role)
                    <span class="badge bg-primary">{{ $role->name }}</span>
                  @endforeach
                  <br>
                  @if($employee->user->password_changed_at)
                    <strong>Last Password Change:</strong> {{ $employee->user->password_changed_at->diffForHumans() }}
                  @endif
                </div>

                <h6 class="mb-3">
                  <i class="bi bi-key"></i> Change Password
                </h6>
                
                <form method="POST" action="{{ route('admin.users.password.reset', $employee->user) }}">
                  @csrf
                  @method('PUT')
                  
                  <input type="hidden" name="reset_by_admin" value="1">
                  
                  <div class="mb-3">
                    <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input 
                        type="password" 
                        class="form-control @error('new_password') is-invalid @enderror" 
                        id="new_password" 
                        name="new_password" 
                        required
                        minlength="8">
                      <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                      </button>
                    </div>
                    <small class="form-text text-muted">
                      Password must be at least 8 characters with uppercase, lowercase, number, and special character.
                    </small>
                    @error('new_password')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    
                    <!-- Password Strength Meter -->
                    <div class="mt-2">
                      <div class="progress" style="height: 5px;">
                        <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                      </div>
                      <small id="passwordStrengthText" class="text-muted"></small>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input 
                      type="password" 
                      class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                      id="new_password_confirmation" 
                      name="new_password_confirmation" 
                      required>
                    @error('new_password_confirmation')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Password Reset <span class="text-danger">*</span></label>
                    <textarea 
                      class="form-control @error('reason') is-invalid @enderror" 
                      id="reason" 
                      name="reason" 
                      rows="2" 
                      required 
                      placeholder="e.g., Password reset requested by employee, Security update, etc.">{{ old('reason') }}</textarea>
                    <small class="form-text text-muted">This will be logged in the security audit trail.</small>
                    @error('reason')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="notify_user" name="notify_user" value="1" checked>
                    <label class="form-check-label" for="notify_user">
                      Send password change notification email to {{ $employee->user->email }}
                    </label>
                  </div>

                  <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Warning:</strong> Changing this password will:
                    <ul class="mb-0 mt-2">
                      <li>Immediately update the user's password</li>
                      <li>Invalidate all existing sessions (user will need to re-login)</li>
                      <li>Log this action in the security audit trail</li>
                      <li>Set password expiry to 90 days from now</li>
                    </ul>
                  </div>

                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning" id="resetPasswordBtn">
                      <i class="bi bi-key"></i> Reset Password
                    </button>
                    <a href="{{ route('admin.users.password.show', $employee->user) }}" class="btn btn-outline-secondary" target="_blank">
                      <i class="bi bi-box-arrow-up-right"></i> Open Full Password Management
                    </a>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="bi bi-shield-check"></i> Security Tips
                </h6>
              </div>
              <div class="card-body">
                <h6 class="text-primary">Password Requirements:</h6>
                <ul class="small">
                  <li>Minimum 8 characters</li>
                  <li>At least one uppercase letter</li>
                  <li>At least one lowercase letter</li>
                  <li>At least one number</li>
                  <li>At least one special character</li>
                </ul>
                
                <h6 class="text-primary mt-3">Best Practices:</h6>
                <ul class="small">
                  <li>Use unique passwords</li>
                  <li>Avoid personal information</li>
                  <li>Change passwords regularly</li>
                  <li>Don't share passwords</li>
                  <li>Enable two-factor authentication when available</li>
                </ul>

                <div class="alert alert-info small mt-3">
                  <i class="bi bi-lightbulb"></i>
                  <strong>Tip:</strong> You can also manage this user's password from the main Password Management interface.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    <!-- End Password Management Tab -->
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Employee form submission handler
  const submitBtn = document.getElementById('submitBtn');
  if (submitBtn) {
    submitBtn.addEventListener('click', function() {
      const btn = this;
      const spinner = btn.querySelector('.spinner-border');
      const text = btn.querySelector('#submitText');

      // Disable button and show loading
      btn.disabled = true;
      spinner.classList.remove('d-none');
      text.textContent = 'Updating...';

      // Re-enable after 10 seconds as fallback
      setTimeout(function() {
        btn.disabled = false;
        spinner.classList.add('d-none');
        text.textContent = 'Update Employee';
      }, 10000);
    });
  }

  // Password toggle functionality
  const togglePasswordBtn = document.getElementById('togglePassword');
  if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener('click', function() {
      const passwordInput = document.getElementById('new_password');
      const icon = document.getElementById('togglePasswordIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  }

  // Password strength meter
  const passwordInput = document.getElementById('new_password');
  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      const password = this.value;
      const strengthBar = document.getElementById('passwordStrength');
      const strengthText = document.getElementById('passwordStrengthText');
      
      let strength = 0;
      let strengthLabel = '';
      
      // Check password criteria
      if (password.length >= 8) strength++;
      if (password.length >= 12) strength++;
      if (/[a-z]/.test(password)) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[^a-zA-Z0-9]/.test(password)) strength++;
      
      // Calculate percentage and label
      const percentage = (strength / 6) * 100;
      
      if (strength === 0 || password.length === 0) {
        strengthLabel = '';
        strengthBar.className = 'progress-bar';
      } else if (strength <= 2) {
        strengthLabel = 'Weak';
        strengthBar.className = 'progress-bar bg-danger';
      } else if (strength <= 3) {
        strengthLabel = 'Fair';
        strengthBar.className = 'progress-bar bg-warning';
      } else if (strength <= 4) {
        strengthLabel = 'Good';
        strengthBar.className = 'progress-bar bg-info';
      } else if (strength <= 5) {
        strengthLabel = 'Strong';
        strengthBar.className = 'progress-bar bg-success';
      } else {
        strengthLabel = 'Very Strong';
        strengthBar.className = 'progress-bar bg-success';
      }
      
      strengthBar.style.width = percentage + '%';
      strengthText.textContent = strengthLabel;
      strengthText.className = 'text-muted';
      
      if (strength <= 2 && password.length > 0) {
        strengthText.className = 'text-danger';
      } else if (strength <= 3) {
        strengthText.className = 'text-warning';
      } else if (strength >= 4) {
        strengthText.className = 'text-success';
      }
    });
  }

  // Password confirmation match validation
  const confirmPasswordInput = document.getElementById('new_password_confirmation');
  if (confirmPasswordInput && passwordInput) {
    confirmPasswordInput.addEventListener('input', function() {
      if (this.value !== passwordInput.value) {
        this.classList.add('is-invalid');
      } else {
        this.classList.remove('is-invalid');
      }
    });
  }

  // Reset password form submission
  const resetPasswordBtn = document.getElementById('resetPasswordBtn');
  if (resetPasswordBtn) {
    resetPasswordBtn.closest('form').addEventListener('submit', function(e) {
      const password = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('new_password_confirmation').value;
      const reason = document.getElementById('reason').value;
      
      if (!password || !confirmPassword || !reason) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
      }
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
      
      if (!confirm('Are you sure you want to reset this user\'s password? This action will be logged.')) {
        e.preventDefault();
        return false;
      }
      
      // Disable button to prevent double submission
      resetPasswordBtn.disabled = true;
      resetPasswordBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Resetting...';
    });
  }
});
</script>
@endsection