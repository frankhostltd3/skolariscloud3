@extends('tenant.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-lock"></i> Password Management for {{ $user->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>User Account Information:</strong><br>
                            <strong>Email:</strong> {{ $user->email }}<br>
                            <strong>Role:</strong>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                            <br>
                            @if ($user->password_changed_at)
                                <strong>Last Password Change:</strong> {{ $user->password_changed_at->diffForHumans() }}
                            @endif
                        </div>

                        <h6 class="mb-3">
                            <i class="bi bi-key"></i> Change Password
                        </h6>

                        <form method="POST" action="{{ route('admin.users.password.reset', $user) }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="reset_by_admin" value="1">

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                        id="new_password" name="new_password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Password must be at least 8 characters with uppercase, lowercase, number, and special
                                    character.
                                </small>
                                @error('new_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password <span
                                        class="text-danger">*</span></label>
                                <input type="password"
                                    class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                    id="new_password_confirmation" name="new_password_confirmation" required>
                                @error('new_password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Password Reset <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="2"
                                    required placeholder="e.g., Password reset requested by employee, Security update, etc.">{{ old('reason') }}</textarea>
                                <small class="form-text text-muted">This will be logged in the security audit trail.</small>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="notify_user" name="notify_user"
                                    value="1" checked>
                                <label class="form-check-label" for="notify_user">
                                    Send password change notification email to {{ $user->email }}
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
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-key"></i> Reset Password
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endsection
