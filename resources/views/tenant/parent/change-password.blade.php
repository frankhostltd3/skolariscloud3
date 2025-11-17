@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.parent._sidebar')
@endsection

@section('title', __('Change Password'))

@section('styles')
<style>
    .card,
    .card-header,
    .card-body,
    .btn,
    .btn:focus,
    .btn:hover,
    .form-control,
    .form-select,
    .input-group-text,
    .alert,
    .progress,
    .progress-bar,
    .toast,
    .toast .btn-close,
    .d-flex > .btn,
    .badge {
        border-radius: 4px !important;
    }

    .input-group > .form-control,
    .input-group > .form-select,
    .input-group > .btn,
    .input-group .input-group-text {
        border-radius: 4px !important;
    }

    .alert ul,
    .card ul,
    .list-group-item,
    .dropdown-menu,
    .dropdown-item {
        border-radius: 4px !important;
    }

    .progress {
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h4 fw-semibold mb-0">{{ __('Change Password') }}</h1>
        <p class="text-muted mb-0">{{ __('Update your guardian account credentials securely.') }}</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Secure Password Update') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tenant.profile.parent.updatePassword') }}" id="passwordForm">
                    @csrf
                    @method('PUT')

                        <!-- Current Password -->
                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="bi bi-eye" id="currentPasswordIcon"></i>
                                </button>
                            </div>
                            @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="bi bi-eye" id="newPasswordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                {{ __('Password must be at least 8 characters long and contain a mix of letters, numbers, and symbols.') }}
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" name="password_confirmation" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Password Strength') }}</label>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="strengthText">{{ __('Enter a password to see strength') }}</small>
                        </div>

                        <!-- Security Tips -->
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="bi bi-shield text-primary me-2"></i>{{ __('Password Security Tips') }}</h6>
                            <ul class="mb-0 small">
                                <li>{{ __('Use a unique password that you don\'t use elsewhere') }}</li>
                                <li>{{ __('Include uppercase and lowercase letters, numbers, and symbols') }}</li>
                                <li>{{ __('Avoid using personal information like names or dates') }}</li>
                                <li>{{ __('Consider using a passphrase with multiple words') }}</li>
                            </ul>
                        </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tenant.profile.parent.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('Change Password') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Security Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">{{ __('Account Security') }}</h6>
                    <p class="small text-muted mb-0">{{ __('Last password change:') }} {{ auth()->user()?->updated_at?->format('M d, Y') ?? '—' }}</p>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-success">{{ __('Best Practices') }}</h6>
                    <ul class="small text-muted mb-0">
                        <li>{{ __('Change passwords regularly') }}</li>
                        <li>{{ __('Never share your password') }}</li>
                        <li>{{ __('Use two-factor authentication when available') }}</li>
                        <li>{{ __('Log out from shared computers') }}</li>
                    </ul>
                </div>

                <hr>

                <div class="text-center">
                    <a href="{{ route('tenant.profile.parent.settings') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear me-1"></i>{{ __('Account Settings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggles
        const toggles = [{
                button: 'toggleCurrentPassword',
                input: 'current_password',
                icon: 'currentPasswordIcon'
            },
            {
                button: 'toggleNewPassword',
                input: 'password',
                icon: 'newPasswordIcon'
            },
            {
                button: 'toggleConfirmPassword',
                input: 'password_confirmation',
                icon: 'confirmPasswordIcon'
            }
        ];

        toggles.forEach(toggle => {
            document.getElementById(toggle.button).addEventListener('click', function() {
                const input = document.getElementById(toggle.input);
                const icon = document.getElementById(toggle.icon);

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'bi bi-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'bi bi-eye';
                }
            });
        });

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('strengthText');
        const submitBtn = document.getElementById('submitBtn');

        function checkPasswordStrength(password) {
            let strength = 0;
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                numbers: /\d/.test(password),
                symbols: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            strength = Object.values(checks).filter(Boolean).length;

            let color, text, percent;
            switch (strength) {
                case 0:
                case 1:
                    color = 'bg-danger';
                    text = '{{ __("Very Weak") }}';
                    percent = 20;
                    break;
                case 2:
                    color = 'bg-warning';
                    text = '{{ __("Weak") }}';
                    percent = 40;
                    break;
                case 3:
                    color = 'bg-info';
                    text = '{{ __("Fair") }}';
                    percent = 60;
                    break;
                case 4:
                    color = 'bg-primary';
                    text = '{{ __("Good") }}';
                    percent = 80;
                    break;
                case 5:
                    color = 'bg-success';
                    text = '{{ __("Strong") }}';
                    percent = 100;
                    break;
            }

            strengthBar.className = `progress-bar ${color}`;
            strengthBar.style.width = `${percent}%`;
            strengthText.textContent = password ? text : '{{ __("Enter a password to see strength") }}';

            return strength >= 3;
        }

        function checkPasswordsMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (confirm && password !== confirm) {
                confirmInput.classList.add('is-invalid');
                return false;
            } else {
                confirmInput.classList.remove('is-invalid');
                return true;
            }
        }

        function updateSubmitButton() {
            const isStrong = checkPasswordStrength(passwordInput.value);
            const isMatching = checkPasswordsMatch();
            const hasCurrentPassword = document.getElementById('current_password').value.length > 0;

            submitBtn.disabled = !(isStrong && isMatching && hasCurrentPassword);
        }

        passwordInput.addEventListener('input', updateSubmitButton);
        confirmInput.addEventListener('input', updateSubmitButton);
        document.getElementById('current_password').addEventListener('input', updateSubmitButton);

        // Initial check
        updateSubmitButton();
    });
</script>
@endsection
