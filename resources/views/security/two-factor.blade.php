@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h4 fw-semibold mb-1">
                        <span class="bi bi-shield-lock me-2"></span>
                        {{ __('Two-Factor Authentication') }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ __('Add additional security to your account using two-factor authentication') }}</p>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-semibold">
                            <span class="bi bi-shield-check me-2"></span>
                            {{ __('Two-Factor Authentication Status') }}
                        </div>
                        <div class="card-body">
                            @if ($user->two_factor_confirmed_at)
                                <div class="alert alert-success mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="bi bi-check-circle-fill fs-4 me-3"></span>
                                        <div>
                                            <h6 class="mb-1">Two-Factor Authentication is Enabled</h6>
                                            <p class="mb-0 small">Your account is protected with two-factor authentication.
                                                Confirmed on
                                                {{ $user->two_factor_confirmed_at->format('F j, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">Recovery Codes</h6>
                                <p class="text-muted mb-3">Store these recovery codes in a secure password manager. They can
                                    be used to recover access to your account if your two-factor authentication device is
                                    lost.</p>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="showRecoveryCodes()">
                                        <span class="bi bi-eye me-1"></span> View Recovery Codes
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="regenerateRecoveryCodes()">
                                        <span class="bi bi-arrow-clockwise me-1"></span> Regenerate Recovery Codes
                                    </button>
                                </div>

                                <div id="recovery-codes-container" class="d-none">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row g-2" id="recovery-codes-list"></div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3 text-danger">Disable Two-Factor Authentication</h6>
                                <p class="text-muted mb-3">Once two-factor authentication is disabled, you will be able to
                                    login with just your email and password.</p>

                                <form action="{{ route('two-factor.destroy') }}" method="POST" id="disable-2fa-form">
                                    @csrf
                                    @method('DELETE')

                                    <div class="mb-3" style="max-width: 300px;">
                                        <label for="password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to disable two-factor authentication?')">
                                        <span class="bi bi-shield-slash me-2"></span>Disable Two-Factor Authentication
                                    </button>
                                </form>
                            @elseif ($user->two_factor_secret)
                                <div class="alert alert-warning mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="bi bi-exclamation-triangle-fill fs-4 me-3"></span>
                                        <div>
                                            <h6 class="mb-1">Two-Factor Authentication Pending Confirmation</h6>
                                            <p class="mb-0 small">Scan the QR code below with your authenticator app and
                                                enter the 6-digit code to complete setup.</p>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">Scan QR Code</h6>
                                <p class="text-muted mb-3">Scan this QR code using your phone's authenticator application
                                    (Google Authenticator, Authy, 1Password, etc.):</p>

                                <div class="mb-4 text-center">
                                    <div id="qr-code-display" class="d-inline-block p-3 bg-white border rounded">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">Verify Setup</h6>
                                <p class="text-muted mb-3">Enter the 6-digit code from your authenticator app to confirm
                                    setup:</p>

                                <form action="{{ route('two-factor.confirm') }}" method="POST">
                                    @csrf

                                    <div class="mb-3" style="max-width: 200px;">
                                        <label for="code" class="form-label">Authentication Code</label>
                                        <input type="text" class="form-control form-control-lg text-center"
                                            id="code" name="code" placeholder="000000" maxlength="6"
                                            pattern="[0-9]{6}" required autofocus>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <span class="bi bi-check-circle me-2"></span>Confirm & Enable
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="cancelSetup()">
                                        Cancel
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="bi bi-info-circle-fill fs-4 me-3"></span>
                                        <div>
                                            <h6 class="mb-1">Two-Factor Authentication is Not Enabled</h6>
                                            <p class="mb-0 small">Enable two-factor authentication to add an extra layer of
                                                security to your account.</p>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">How It Works</h6>
                                <ol class="mb-4">
                                    <li>Install an authenticator app on your phone (Google Authenticator, Authy, 1Password,
                                        etc.)</li>
                                    <li>Click "Enable Two-Factor Authentication" below</li>
                                    <li>Scan the QR code with your authenticator app</li>
                                    <li>Enter the 6-digit code from your app to confirm</li>
                                    <li>Save your recovery codes in a secure location</li>
                                </ol>

                                <form action="{{ route('two-factor.store') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <span class="bi bi-shield-plus me-2"></span>Enable Two-Factor Authentication
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Help Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-semibold">
                            <span class="bi bi-question-circle me-2 text-info"></span>
                            Help & Support
                        </div>
                        <div class="card-body">
                            <h6 class="fw-semibold mb-2">What is Two-Factor Authentication?</h6>
                            <p class="small text-muted mb-3">Two-factor authentication (2FA) adds an extra layer of
                                security to your account by requiring both your password and a verification code from your
                                phone.</p>

                            <h6 class="fw-semibold mb-2">Recommended Apps</h6>
                            <ul class="small text-muted">
                                <li><strong>Google Authenticator</strong> - iOS / Android</li>
                                <li><strong>Authy</strong> - iOS / Android / Desktop</li>
                                <li><strong>Microsoft Authenticator</strong> - iOS / Android</li>
                                <li><strong>1Password</strong> - iOS / Android / Desktop</li>
                            </ul>

                            <h6 class="fw-semibold mb-2">Lost Your Device?</h6>
                            <p class="small text-muted mb-0">Use one of your recovery codes to login, then you can disable
                                2FA and set it up again with your new device.</p>
                        </div>
                    </div>

                    @if (setting('enable_two_factor_auth', false))
                        <div class="alert alert-warning mt-3" role="alert">
                            <strong><span class="bi bi-exclamation-triangle me-1"></span> School Policy</strong>
                            <p class="small mb-0 mt-2">Two-factor authentication is required by your school. All users must
                                enable 2FA to access their accounts.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load QR code when pending confirmation
        @if ($user->two_factor_secret && !$user->two_factor_confirmed_at)
            document.addEventListener('DOMContentLoaded', function() {
                fetch("{{ route('two-factor.qr-code') }}", {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('qr-code-display').innerHTML = data.svg;
                    })
                    .catch(error => {
                        document.getElementById('qr-code-display').innerHTML =
                            '<div class="text-danger">Failed to load QR code. Please refresh the page.</div>';
                    });
            });
        @endif

        function showRecoveryCodes() {
            fetch("{{ route('two-factor.recovery-codes') }}", {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recovery-codes-container');
                    const list = document.getElementById('recovery-codes-list');

                    list.innerHTML = data.codes.map(code =>
                        `<div class="col-md-6 col-lg-12 col-xl-6"><code class="d-block p-2 bg-white">${code}</code></div>`
                    ).join('');

                    container.classList.remove('d-none');
                })
                .catch(error => {
                    alert('Failed to load recovery codes. Please try again.');
                });
        }

        function regenerateRecoveryCodes() {
            if (!confirm('Are you sure you want to regenerate recovery codes? Your old codes will no longer work.')) {
                return;
            }

            fetch("{{ route('two-factor.recovery-codes.regenerate') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success !== false) {
                        document.getElementById('recovery-codes-container').classList.add('d-none');
                        alert(
                            'Recovery codes have been regenerated. Click "View Recovery Codes" to see your new codes.');
                        window.location.reload();
                    } else {
                        alert('Failed to regenerate recovery codes: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Failed to regenerate recovery codes. Please try again.');
                });
        }

        function cancelSetup() {
            if (confirm('Are you sure you want to cancel two-factor authentication setup?')) {
                // Submit form to disable (same as clicking disable button)
                window.location.href = "{{ route('two-factor.destroy') }}";
            }
        }
    </script>
@endsection
