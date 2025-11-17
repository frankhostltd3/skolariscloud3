@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', __('Reset User Password'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h4 fw-semibold mb-1">{{ __('Reset User Password') }}</h1>
                    <p class="text-muted mb-0">{{ __('Set a new password for this user') }}</p>
                </div>
                <a href="{{ route('admin.users.password.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back to List') }}
                </a>
            </div>

            <!-- User Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>{{ __('User Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php $targetAvatar = $user->profile_photo_url; @endphp
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            @if($targetAvatar)
                                <img src="{{ $targetAvatar }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle"
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 100px; height: 100px;">
                                    <i class="bi bi-person fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 200px;">{{ __('Name') }}</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Role') }}</th>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-secondary">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Last Password Change') }}</th>
                                    <td>
                                        @if($user->password_changed_at)
                                            {{ $user->password_changed_at->format('M d, Y \a\t g:i A') }}
                                            <small class="text-muted">({{ $user->password_changed_at->diffForHumans() }})</small>
                                        @else
                                            <span class="text-muted">{{ __('Never') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Account Status') }}</th>
                                    <td>
                                        @if($user->deactivated_at)
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Alert -->
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <h5 class="alert-heading">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Important') }}
                </h5>
                <ul class="mb-0">
                    <li>{{ __('This action will immediately change the user\'s password') }}</li>
                    <li>{{ __('The user will be logged out of all active sessions') }}</li>
                    <li>{{ __('This action will be logged in the security audit log') }}</li>
                    <li>{{ __('Consider notifying the user about the password change') }}</li>
                </ul>
            </div>

            <!-- Reset Password Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>{{ __('New Password') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.password.reset', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label required">{{ __('New Password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password_icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    <small>
                                        <i class="bi bi-info-circle"></i> 
                                        {{ __('Password must be at least 8 characters, include uppercase, lowercase, number, and special character.') }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label required">{{ __('Confirm Password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Strength Indicator -->
                            <div class="col-12">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="passwordStrengthText"></small>
                            </div>

                            <div class="col-12">
                                <label for="reason" class="form-label required">{{ __('Reason for Password Reset') }}</label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" 
                                          id="reason" 
                                          name="reason" 
                                          rows="4" 
                                          required 
                                          placeholder="{{ __('Explain why you are resetting this user\'s password (required for audit trail)...') }}"></textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="notify_user" 
                                           name="notify_user" 
                                           value="1">
                                    <label class="form-check-label" for="notify_user">
                                        {{ __('Send email notification to user about password change') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.users.password.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check me-2"></i>{{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    let strength = 0;
    let message = '';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            strengthBar.style.width = '20%';
            strengthBar.className = 'progress-bar bg-danger';
            message = '{{ __("Weak password") }}';
            break;
        case 2:
            strengthBar.style.width = '40%';
            strengthBar.className = 'progress-bar bg-warning';
            message = '{{ __("Fair password") }}';
            break;
        case 3:
            strengthBar.style.width = '60%';
            strengthBar.className = 'progress-bar bg-info';
            message = '{{ __("Good password") }}';
            break;
        case 4:
            strengthBar.style.width = '80%';
            strengthBar.className = 'progress-bar bg-primary';
            message = '{{ __("Strong password") }}';
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.className = 'progress-bar bg-success';
            message = '{{ __("Very strong password") }}';
            break;
    }
    
    strengthText.textContent = message;
});

// Password confirmation match check
document.getElementById('password_confirmation').addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirmation = e.target.value;
    
    if (confirmation && password !== confirmation) {
        e.target.classList.add('is-invalid');
        if (!e.target.nextElementSibling || !e.target.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = '{{ __("Passwords do not match") }}';
            e.target.parentNode.appendChild(feedback);
        }
    } else {
        e.target.classList.remove('is-invalid');
        const feedback = e.target.parentNode.querySelector('.invalid-feedback');
        if (feedback) feedback.remove();
    }
});
</script>
@endpush
@endsection
