@extends('tenant.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>{{ __('Two-Factor Authentication') }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($twoFactorEnabled)
                        <!-- 2FA Enabled State -->
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>{{ __('Two-Factor Authentication is Enabled') }}</strong>
                                <p class="mb-0 small">{{ __('Your account is protected with two-factor authentication.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>{{ __('Manage Two-Factor Authentication') }}</h5>
                            <p class="text-muted">
                                {{ __('Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to sign in.') }}
                            </p>

                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title"><i class="bi bi-key me-2"></i>{{ __('Recovery Codes') }}</h6>
                                            <p class="card-text small text-muted">
                                                {{ __('View or regenerate your recovery codes.') }}
                                            </p>
                                            <a href="{{ route('tenant.user.two-factor.recovery-codes') }}" class="btn btn-sm btn-outline-primary">
                                                {{ __('View Codes') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border border-danger">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger"><i class="bi bi-shield-x me-2"></i>{{ __('Disable 2FA') }}</h6>
                                            <p class="card-text small text-muted">
                                                {{ __('Remove two-factor authentication from your account.') }}
                                            </p>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disableModal">
                                                {{ __('Disable') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- 2FA Disabled State -->
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>{{ __('Two-Factor Authentication is Not Enabled') }}</strong>
                                <p class="mb-0 small">{{ __('Protect your account with an extra layer of security.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>{{ __('Enable Two-Factor Authentication') }}</h5>
                            <p class="text-muted">
                                {{ __('Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to sign in.') }}
                            </p>

                            <div class="card bg-light border-0 mt-3">
                                <div class="card-body">
                                    <h6><i class="bi bi-info-circle me-2"></i>{{ __('How it works:') }}</h6>
                                    <ol class="small mb-0">
                                        <li>{{ __('Install an authenticator app (like Google Authenticator, Authy, or Microsoft Authenticator) on your phone') }}</li>
                                        <li>{{ __('Scan the QR code with your authenticator app') }}</li>
                                        <li>{{ __('Enter the 6-digit code from your app to confirm setup') }}</li>
                                        <li>{{ __('Save your recovery codes in a safe place') }}</li>
                                    </ol>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('tenant.user.two-factor.enable') }}" class="mt-4">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-check me-2"></i>{{ __('Enable Two-Factor Authentication') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disable 2FA Modal -->
<div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tenant.user.two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="disableModalLabel">{{ __('Disable Two-Factor Authentication') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('This will reduce the security of your account. Are you sure you want to disable two-factor authentication?') }}
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Confirm Your Password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required autofocus>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Disable 2FA') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
