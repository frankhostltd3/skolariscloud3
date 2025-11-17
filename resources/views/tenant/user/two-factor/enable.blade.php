@extends('tenant.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-qr-code me-2"></i>{{ __('Enable Two-Factor Authentication') }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="alert alert-info text-start">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.)') }}
                        </div>
                        
                        <!-- QR Code -->
                        <div class="my-4">
                            {!! $qrCode !!}
                        </div>

                        <!-- Manual Entry -->
                        <div class="mt-4">
                            <p class="small text-muted mb-2">{{ __('Or enter this code manually:') }}</p>
                            <div class="alert alert-light">
                                <code class="fs-6">{{ $secret }}</code>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Form -->
                    <form method="POST" action="{{ route('tenant.user.two-factor.confirm') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">{{ __('Enter the 6-digit code from your authenticator app') }}</label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   maxlength="6" 
                                   pattern="[0-9]{6}"
                                   inputmode="numeric"
                                   placeholder="000000"
                                   required 
                                   autofocus>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                {{ __('The code is 6 digits and changes every 30 seconds.') }}
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>{{ __('Verify and Enable') }}
                            </button>
                            <a href="{{ route('tenant.user.two-factor.show') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-3 border-0 bg-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-question-circle me-2"></i>{{ __('Need help?') }}</h6>
                    <p class="card-text small mb-2">{{ __('Recommended authenticator apps:') }}</p>
                    <ul class="small mb-0">
                        <li>Google Authenticator (iOS/Android)</li>
                        <li>Microsoft Authenticator (iOS/Android)</li>
                        <li>Authy (iOS/Android/Desktop)</li>
                        <li>1Password (Premium)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit when 6 digits are entered
    document.getElementById('code').addEventListener('input', function(e) {
        if (this.value.length === 6) {
            // Optional: auto-submit after a short delay
            // setTimeout(() => this.form.submit(), 500);
        }
    });
</script>
@endpush
@endsection
