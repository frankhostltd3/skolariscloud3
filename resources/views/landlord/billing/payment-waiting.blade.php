@extends('landlord.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <!-- Loading Animation -->
                    <div class="mb-4">
                        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <h2 class="fw-bold mb-3">📱 {{ __('Payment Pending') }}</h2>
                    
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-phone me-2"></i>
                        <strong>{{ __('Check your phone!') }}</strong>
                        <p class="mb-0 mt-2">
                            {{ __('An M-PESA payment request has been sent to your phone.') }}<br>
                            {{ __('Enter your M-PESA PIN to complete the payment.') }}
                        </p>
                    </div>

                    <!-- Transaction Details -->
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row">
                            <div class="col-6 text-start">
                                <small class="text-secondary">{{ __('Amount') }}</small>
                                <p class="mb-0 fw-bold">{{ $transaction->formatted_amount }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-secondary">{{ __('Transaction ID') }}</small>
                                <p class="mb-0"><small class="font-monospace">{{ Str::limit($transaction->transaction_id, 15) }}</small></p>
                            </div>
                        </div>
                    </div>

                    <div id="statusMessage" class="mb-3">
                        <p class="text-secondary">{{ __('Waiting for payment confirmation...') }}</p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: 100%" 
                             id="progressBar"></div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" id="checkStatusBtn" onclick="checkPaymentStatus()">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            {{ __('Check Status') }}
                        </button>
                        <a href="{{ route('landlord.billing.invoices.show', $transaction->related_id) }}" 
                           class="btn btn-outline-secondary">
                            {{ __('Return to Invoice') }}
                        </a>
                    </div>

                    <p class="text-secondary small mt-4 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ __('This page will automatically check payment status every 5 seconds') }}
                    </p>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">{{ __('Not receiving the prompt?') }}</h6>
                    <ul class="list-unstyled mb-0 small text-secondary">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            {{ __('Make sure your phone has network coverage') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            {{ __('Check if your M-PESA PIN is not blocked') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            {{ __('Wait a few moments and check your phone again') }}
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-2"></i>
                            {{ __('You can cancel and try again if the prompt doesn\'t appear') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let checkInterval;
let attempts = 0;
const maxAttempts = 24; // 2 minutes (5 seconds * 24)

async function checkPaymentStatus() {
    const statusMessage = document.getElementById('statusMessage');
    const checkBtn = document.getElementById('checkStatusBtn');
    
    checkBtn.disabled = true;
    checkBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("Checking...") }}';
    
    try {
        const response = await fetch('{{ route('landlord.api.payment.status', $transaction->id) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.status === 'completed') {
            statusMessage.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ __("Payment successful! Redirecting...") }}</div>';
            setTimeout(() => {
                window.location.href = '{{ route("landlord.billing.invoices.show", $transaction->related_id) }}';
            }, 2000);
        } else if (data.status === 'failed') {
            statusMessage.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>{{ __("Payment failed. Please try again.") }}</div>';
            clearInterval(checkInterval);
        } else {
            statusMessage.innerHTML = '<p class="text-secondary">{{ __("Still waiting for payment confirmation...") }}</p>';
        }
    } catch (error) {
        console.error('Error checking status:', error);
        statusMessage.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>{{ __("Could not check status. Please refresh the page.") }}</div>';
    } finally {
        checkBtn.disabled = false;
        checkBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>{{ __("Check Status") }}';
    }
}

// Auto-check every 5 seconds
checkInterval = setInterval(() => {
    attempts++;
    if (attempts >= maxAttempts) {
        clearInterval(checkInterval);
        document.getElementById('statusMessage').innerHTML = 
            '<div class="alert alert-warning"><i class="bi bi-clock me-2"></i>{{ __("Payment confirmation is taking longer than expected. Please check your phone or try again.") }}</div>';
        return;
    }
    checkPaymentStatus();
}, 5000);

// Initial check after 3 seconds
setTimeout(checkPaymentStatus, 3000);
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.spinner-border {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>
@endsection
