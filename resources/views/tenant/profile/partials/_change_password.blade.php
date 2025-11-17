<!-- Change Password Section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="bi bi-shield-lock me-2"></i>{{ __('Change Password') }}
        </h5>
    </div>
    <div class="card-body">
        @if(session('password_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('password_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('password_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle me-2"></i>{{ session('password_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ $changePasswordRoute }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-12">
                    <label for="current_password" class="form-label required">{{ __('Current Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                            <i class="bi bi-eye" id="current_password_icon"></i>
                        </button>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

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
                            {{ __('Password must be at least 8 characters long, include uppercase, lowercase, number, and special character.') }}
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label required">{{ __('Confirm New Password') }}</label>
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
            </div>

            <!-- Password Strength Indicator -->
            <div class="mt-3">
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted" id="passwordStrengthText"></small>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('tenant.forgot-password') }}" class="text-decoration-none">
                    <i class="bi bi-question-circle me-1"></i>{{ __('Forgot your password?') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-check me-2"></i>{{ __('Update Password') }}
                </button>
            </div>
        </form>
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
document.getElementById('password')?.addEventListener('input', function(e) {
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
document.getElementById('password_confirmation')?.addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirmation = e.target.value;
    
    if (confirmation && password !== confirmation) {
        e.target.classList.add('is-invalid');
        if (!e.target.nextElementSibling || !e.target.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
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
