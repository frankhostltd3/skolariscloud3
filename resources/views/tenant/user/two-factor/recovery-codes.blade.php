@extends('tenant.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>{{ __('Recovery Codes') }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if(!isset($showOnly))
                        <div class="alert alert-success">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>{{ __('Two-Factor Authentication Enabled Successfully!') }}</strong>
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>{{ __('Save these recovery codes in a secure location.') }}</strong>
                        <p class="mb-0 small mt-2">
                            {{ __('These codes can be used to access your account if you lose access to your authenticator device. Each code can only be used once.') }}
                        </p>
                    </div>

                    <!-- Recovery Codes Display -->
                    <div class="card bg-light border-0 mt-4">
                        <div class="card-body">
                            <div class="row g-3" id="recoveryCodes">
                                @foreach($recoveryCodes as $code)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center bg-white p-2 rounded border">
                                            <code class="flex-grow-1 text-center">{{ $code }}</code>
                                            <button class="btn btn-sm btn-outline-secondary ms-2 copy-btn" 
                                                    data-code="{{ $code }}"
                                                    title="{{ __('Copy') }}">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-outline-primary" onclick="printCodes()">
                            <i class="bi bi-printer me-2"></i>{{ __('Print Codes') }}
                        </button>
                        <button class="btn btn-outline-secondary" onclick="downloadCodes()">
                            <i class="bi bi-download me-2"></i>{{ __('Download Codes') }}
                        </button>
                        <button class="btn btn-outline-info" onclick="copyAllCodes()">
                            <i class="bi bi-files me-2"></i>{{ __('Copy All') }}
                        </button>
                    </div>

                    @if(isset($showOnly))
                        <!-- Regenerate Option -->
                        <div class="mt-4 pt-4 border-top">
                            <h5>{{ __('Regenerate Recovery Codes') }}</h5>
                            <p class="text-muted small">
                                {{ __('If you have lost your recovery codes or believe they may have been compromised, you can generate new ones. This will invalidate all existing codes.') }}
                            </p>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#regenerateModal">
                                <i class="bi bi-arrow-repeat me-2"></i>{{ __('Regenerate Codes') }}
                            </button>
                        </div>
                    @else
                        <!-- Continue Button -->
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('tenant.user.two-factor.show') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-check-lg me-2"></i>{{ __('I have saved my recovery codes') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regenerate Modal -->
@if(isset($showOnly))
<div class="modal fade" id="regenerateModal" tabindex="-1" aria-labelledby="regenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tenant.user.two-factor.regenerate-recovery-codes') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="regenerateModalLabel">{{ __('Regenerate Recovery Codes') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('This will invalidate all your current recovery codes. Make sure to save the new codes.') }}
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
                    <button type="submit" class="btn btn-warning">{{ __('Regenerate Codes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Copy individual code
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code).then(() => {
                const icon = this.querySelector('i');
                icon.className = 'bi bi-check';
                setTimeout(() => {
                    icon.className = 'bi bi-clipboard';
                }, 2000);
            });
        });
    });

    // Copy all codes
    function copyAllCodes() {
        const codes = @json($recoveryCodes);
        const text = codes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('{{ __("All recovery codes copied to clipboard!") }}');
        });
    }

    // Download codes as text file
    function downloadCodes() {
        const codes = @json($recoveryCodes);
        const text = '{{ config("app.name") }} - Two-Factor Recovery Codes\n' +
                     'Generated: {{ now()->format("Y-m-d H:i:s") }}\n' +
                     '===========================================\n\n' +
                     codes.join('\n') +
                     '\n\n===========================================\n' +
                     'Keep these codes in a secure location.\n' +
                     'Each code can only be used once.';
        
        const blob = new Blob([text], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '2fa-recovery-codes.txt';
        a.click();
        URL.revokeObjectURL(url);
    }

    // Print codes
    function printCodes() {
        const codes = @json($recoveryCodes);
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Recovery Codes</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { color: #333; }
                    .code { font-family: monospace; font-size: 14px; padding: 10px; border: 1px solid #ddd; margin: 5px 0; }
                    .warning { background: #fff3cd; padding: 15px; border: 1px solid #ffc107; margin: 20px 0; }
                </style>
            </head>
            <body>
                <h1>{{ config("app.name") }} - Two-Factor Recovery Codes</h1>
                <p>Generated: {{ now()->format("Y-m-d H:i:s") }}</p>
                <div class="warning">
                    <strong>⚠️ Important:</strong> Keep these codes in a secure location. Each code can only be used once.
                </div>
                ${codes.map(code => `<div class="code">${code}</div>`).join('')}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endpush
@endsection
