@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="h3 fw-bold">My Children's Fee Payments</h1>
                <p class="text-muted">Manage fee payments for your children</p>
            </div>

            {{-- Children List --}}
            @foreach ([['name' => 'John Doe', 'class' => 'Senior 4A', 'total' => 3500000, 'paid' => 2000000], ['name' => 'Jane Doe', 'class' => 'Senior 2B', 'total' => 3200000, 'paid' => 3200000]] as $child)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $child['name'] }}</h5>
                            <small>{{ $child['class'] }}</small>
                        </div>
                        @php
                            $balance = $child['total'] - $child['paid'];
                        @endphp
                        <span class="badge bg-{{ $balance > 0 ? 'warning' : 'success' }} fs-6">
                            {{ $balance > 0 ? 'Balance: ' . formatMoney($balance) : 'Paid in Full' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <small class="text-muted d-block">Total Fees</small>
                                <strong>{{ formatMoney($child['total']) }}</strong>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <small class="text-muted d-block">Amount Paid</small>
                                <strong class="text-success">{{ formatMoney($child['paid']) }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Outstanding</small>
                                <strong class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ formatMoney($balance) }}
                                </strong>
                            </div>
                        </div>

                        @if ($balance > 0)
                            <button class="btn btn-primary" data-bs-toggle="collapse"
                                data-bs-target="#payment-{{ $loop->index }}">
                                <i class="bi bi-credit-card me-2"></i>Make Payment
                            </button>
                        @endif
                    </div>

                    @if ($balance > 0)
                        <div class="collapse" id="payment-{{ $loop->index }}">
                            <div class="card-body border-top bg-light">
                                <h6 class="fw-semibold mb-3">Select Payment Method</h6>

                                @if (bankPaymentInstructions())
                                    <button class="btn btn-outline-primary mb-3" data-bs-toggle="collapse"
                                        data-bs-target="#bank-details-{{ $loop->index }}">
                                        <i class="bi bi-bank me-2"></i>View Bank Transfer Details
                                    </button>

                                    <div class="collapse" id="bank-details-{{ $loop->index }}">
                                        @include('partials.bank-payment-instructions', [
                                            'title' => 'Bank Transfer Details for ' . $child['name'],
                                        ])
                                    </div>
                                @endif

                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Important:</strong> When making payment, please include your child's student ID
                                    in the payment reference for faster processing.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Payment Instructions --}}
            @if (bankPaymentInstructions())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-question-circle me-2"></i>How to Pay School Fees
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Step 1: Make Payment</h6>
                                <p class="text-muted mb-0">
                                    Visit your bank or use mobile/internet banking to transfer funds to the school's
                                    account using the details provided above.
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Step 2: Keep Receipt</h6>
                                <p class="text-muted mb-0">
                                    Save your bank receipt or transaction reference number. You'll need this for
                                    verification.
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Step 3: Submit Proof</h6>
                                <p class="text-muted mb-0">
                                    Contact the school accounts office or upload your payment receipt through the portal.
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Step 4: Confirmation</h6>
                                <p class="text-muted mb-0">
                                    Your payment will be verified within 1-3 business days and your account updated.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
