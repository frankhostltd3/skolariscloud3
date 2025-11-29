@extends('tenant.layouts.app')

@section('title', 'Payment Status')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        {{-- Status Icon --}}
                        <div class="mb-4">
                            @switch($transaction->status)
                                @case('pending')
                                @case('processing')
                                    <div class="spinner-grow text-warning" style="width: 4rem; height: 4rem;" id="loadingSpinner">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="d-none" id="statusIcon">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                    </div>
                                @break

                                @case('completed')
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                @break

                                @case('failed')
                                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                                @break

                                @case('cancelled')
                                    <i class="bi bi-slash-circle text-secondary" style="font-size: 4rem;"></i>
                                @break

                                @case('expired')
                                    <i class="bi bi-clock-history text-dark" style="font-size: 4rem;"></i>
                                @break

                                @default
                                    <i class="bi bi-question-circle text-secondary" style="font-size: 4rem;"></i>
                            @endswitch
                        </div>

                        {{-- Status Message --}}
                        <h3 class="mb-3" id="statusTitle">
                            @switch($transaction->status)
                                @case('pending')
                                    Awaiting Approval
                                @break

                                @case('processing')
                                    Processing Payment
                                @break

                                @case('completed')
                                    Payment Successful!
                                @break

                                @case('failed')
                                    Payment Failed
                                @break

                                @case('cancelled')
                                    Payment Cancelled
                                @break

                                @case('expired')
                                    Payment Expired
                                @break

                                @default
                                    Unknown Status
                            @endswitch
                        </h3>

                        <p class="text-muted mb-4" id="statusMessage">
                            @switch($transaction->status)
                                @case('pending')
                                @case('processing')
                                    Please check your phone and approve the payment request.
                                    <br><small>A prompt should appear on {{ $transaction->phone_number }}</small>
                                @break

                                @case('completed')
                                    Your payment of {{ $transaction->formatted_amount }} has been received.
                                @break

                                @case('failed')
                                    {{ $transaction->failure_reason ?? 'The payment could not be processed.' }}
                                @break

                                @case('cancelled')
                                    This payment was cancelled.
                                @break

                                @case('expired')
                                    The payment request has expired. Please try again.
                                @break
                            @endswitch
                        </p>

                        {{-- Transaction Details --}}
                        <div class="bg-light rounded p-3 mb-4 text-start">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Transaction ID</small>
                                    <div class="fw-bold small">{{ $transaction->transaction_id }}</div>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted">Amount</small>
                                    <div class="fw-bold">{{ $transaction->formatted_amount }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Phone Number</small>
                                    <div>{{ $transaction->phone_number }}</div>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted">Status</small>
                                    <div>
                                        <span class="badge {{ $transaction->status_badge_class }}" id="statusBadge">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Date</small>
                                    <div>{{ $transaction->created_at->format('M d, Y h:i A') }}</div>
                                </div>
                                @if ($transaction->description)
                                    <div class="col-12">
                                        <small class="text-muted">Description</small>
                                        <div>{{ $transaction->description }}</div>
                                    </div>
                                @endif
                                @if ($transaction->processing_duration)
                                    <div class="col-12">
                                        <small class="text-muted">Processing Time</small>
                                        <div>{{ $transaction->processing_duration }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            @if ($transaction->isPending() || $transaction->isProcessing())
                                <button type="button" class="btn btn-outline-secondary" onclick="checkStatus()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh Status
                                </button>
                                <form method="POST"
                                    action="{{ route('tenant.payments.mobile-money.cancel', $transaction->transaction_id) }}"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to cancel this payment?')">
                                        <i class="bi bi-x-lg me-1"></i>Cancel
                                    </button>
                                </form>
                            @endif

                            @if ($transaction->canRetry())
                                <a href="{{ route('tenant.payments.mobile-money.create') }}?amount={{ $transaction->amount }}&phone={{ $transaction->phone_number }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i>Try Again
                                </a>
                            @endif

                            @if ($transaction->isCompleted())
                                <a href="{{ route('tenant.payments.mobile-money.history') }}" class="btn btn-primary">
                                    <i class="bi bi-list me-1"></i>View History
                                </a>
                            @endif

                            <a href="{{ route('tenant.dashboard') }}" class="btn btn-outline-primary">
                                <i class="bi bi-house me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Help Section --}}
                @if ($transaction->isPending() || $transaction->isProcessing())
                    <div class="card mt-4 border-0 bg-light">
                        <div class="card-body">
                            <h6><i class="bi bi-question-circle me-2"></i>Not received the prompt?</h6>
                            <ul class="mb-0 small">
                                <li>Make sure your phone is switched on and has network coverage</li>
                                <li>Check if the phone number {{ $transaction->phone_number }} is correct</li>
                                <li>Ensure you have sufficient balance for the transaction</li>
                                <li>The prompt may take up to 60 seconds to arrive</li>
                                <li>If no prompt arrives, try dialing *165# (MTN) or *185# (Airtel) and check pending
                                    approvals</li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let checkInterval;
            const transactionId = '{{ $transaction->transaction_id }}';
            const isPending = {{ $transaction->isPending() || $transaction->isProcessing() ? 'true' : 'false' }};

            function checkStatus() {
                fetch('/payments/mobile-money/check/' + transactionId, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_final) {
                            clearInterval(checkInterval);
                            // Reload to show final state
                            window.location.reload();
                        } else {
                            // Update status badge if changed
                            document.getElementById('statusBadge').textContent = data.transaction.status.charAt(0)
                                .toUpperCase() + data.transaction.status.slice(1);
                        }
                    })
                    .catch(err => console.error('Status check failed:', err));
            }

            // Auto-check status every 5 seconds if pending
            if (isPending) {
                checkInterval = setInterval(checkStatus, 5000);

                // Stop checking after 5 minutes
                setTimeout(function() {
                    clearInterval(checkInterval);
                }, 300000);
            }
        </script>
    @endpush
@endsection
