<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0">
            <i class="bi bi-shield-lock me-2"></i>{{ __('Two-Factor Authentication') }}
        </h5>
    </div>
    <div class="card-body">
        @if(auth()->user()->hasTwoFactorEnabled())
            <!-- 2FA Enabled -->
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fw-bold">{{ __('Two-Factor Authentication is Active') }}</h6>
                    <p class="text-muted small mb-3">
                        {{ __('Your account is protected with two-factor authentication. You will need to enter a code from your authenticator app each time you sign in.') }}
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('tenant.user.two-factor.show') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-gear me-1"></i>{{ __('Manage 2FA') }}
                        </a>
                        <a href="{{ route('tenant.user.two-factor.recovery-codes') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-key me-1"></i>{{ __('Recovery Codes') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- 2FA Disabled -->
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-shield-x text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fw-bold">{{ __('Enable Two-Factor Authentication') }}</h6>
                    <p class="text-muted small mb-3">
                        {{ __('Add an extra layer of security to your account. You will need to enter a code from your phone in addition to your password when signing in.') }}
                    </p>
                    @if(enable_two_factor_auth())
                        <a href="{{ route('tenant.user.two-factor.show') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-shield-check me-1"></i>{{ __('Enable 2FA') }}
                        </a>
                    @else
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>{{ __('Two-factor authentication is not enabled by your administrator.') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
