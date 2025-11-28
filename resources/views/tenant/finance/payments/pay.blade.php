@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i>Make a Payment</h4>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm" action="{{ url('/fees/pay') }}" method="POST">
                            @csrf

                            <!-- Hidden Fields -->
                            <input type="hidden" name="redirect_url" value="{{ url('/fees/callback') }}">

                            <!-- Student/Payer Details -->
                            <h5 class="mb-3 text-muted">Payer Details</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ auth()->user()->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ auth()->user()->email ?? '' }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="+256..." required>
                            </div>

                            <hr class="my-4">

                            <!-- Payment Details -->
                            <h5 class="mb-3 text-muted">Payment Details</h5>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ currentCurrency()->symbol ?? '$' }}</span>
                                    <input type="number" class="form-control" id="amount" name="amount" min="1"
                                        step="0.01" required>
                                    <input type="hidden" name="currency" value="{{ currentCurrency()->code ?? 'USD' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Payment For</label>
                                <select class="form-select" id="description" name="description" required>
                                    <option value="">Select Purpose...</option>
                                    <option value="Tuition Fees">Tuition Fees</option>
                                    <option value="Registration Fees">Registration Fees</option>
                                    <option value="Library Fine">Library Fine</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Select Payment Method</label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check card-radio">
                                            <input class="form-check-input" type="radio" name="gateway"
                                                id="gateway_flutterwave" value="flutterwave" checked>
                                            <label class="form-check-label card p-3 text-center h-100"
                                                for="gateway_flutterwave">
                                                <i class="bi bi-phone fs-2 text-warning"></i>
                                                <span class="d-block mt-2">Mobile Money / Card</span>
                                                <small class="text-muted">(Flutterwave)</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check card-radio">
                                            <input class="form-check-input" type="radio" name="gateway"
                                                id="gateway_stripe" value="stripe">
                                            <label class="form-check-label card p-3 text-center h-100" for="gateway_stripe">
                                                <i class="bi bi-credit-card-2-front fs-2 text-primary"></i>
                                                <span class="d-block mt-2">Credit Card</span>
                                                <small class="text-muted">(Stripe)</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check card-radio">
                                            <input class="form-check-input" type="radio" name="gateway"
                                                id="gateway_paypal" value="paypal">
                                            <label class="form-check-label card p-3 text-center h-100"
                                                for="gateway_paypal">
                                                <i class="bi bi-paypal fs-2 text-info"></i>
                                                <span class="d-block mt-2">PayPal</span>
                                                <small class="text-muted">(International)</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="payButton">
                                    <i class="bi bi-lock-fill me-2"></i>Pay Now
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
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = document.getElementById('payButton');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success' && result.data && result.data.link) {
                            // Redirect to payment gateway
                            window.location.href = result.data.link;
                        } else if (result.link) {
                            // Some gateways might return link directly
                            window.location.href = result.link;
                        } else {
                            alert('Error initiating payment: ' + (result.message || 'Unknown error'));
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .card-radio .form-check-input {
                display: none;
            }

            .card-radio .card {
                cursor: pointer;
                transition: all 0.2s;
                border: 2px solid transparent;
            }

            .card-radio .form-check-input:checked+.card {
                border-color: var(--bs-primary);
                background-color: rgba(var(--bs-primary-rgb), 0.05);
            }

            .card-radio .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
            }
        </style>
    @endpush
@endsection
