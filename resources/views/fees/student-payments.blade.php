@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="h3 fw-bold">My Fee Payments</h1>
                <p class="text-muted">View your outstanding fees and make payments</p>
            </div>

            {{-- Fee Summary Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <small class="text-muted d-block">Total Fees</small>
                            <h4 class="mb-0">{{ formatMoney(3500000) }}</h4>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <small class="text-muted d-block">Amount Paid</small>
                            <h4 class="mb-0 text-success">{{ formatMoney(2000000) }}</h4>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Outstanding Balance</small>
                            <h4 class="mb-0 text-danger">{{ formatMoney(1500000) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Options --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment Options</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Choose your preferred payment method below:</p>

                    <div class="row g-3 mb-4">
                        @if (bankPaymentInstructions())
                            <div class="col-md-6">
                                <button class="btn btn-outline-primary w-100 py-3" data-bs-toggle="collapse"
                                    data-bs-target="#bankTransferDetails">
                                    <i class="bi bi-bank fs-4 d-block mb-2"></i>
                                    <span class="fw-semibold">Bank Transfer</span><br>
                                    <small class="text-muted">Direct bank deposit</small>
                                </button>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <button class="btn btn-outline-primary w-100 py-3" disabled>
                                <i class="bi bi-credit-card fs-4 d-block mb-2"></i>
                                <span class="fw-semibold">Card Payment</span><br>
                                <small class="text-muted">Coming soon</small>
                            </button>
                        </div>

                        <div class="col-md-6">
                            <button class="btn btn-outline-primary w-100 py-3" disabled>
                                <i class="bi bi-phone fs-4 d-block mb-2"></i>
                                <span class="fw-semibold">Mobile Money</span><br>
                                <small class="text-muted">Coming soon</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Transfer Details (Collapsible) --}}
            @if (bankPaymentInstructions())
                <div class="collapse" id="bankTransferDetails">
                    @include('partials.bank-payment-instructions', ['title' => 'Pay via Bank Transfer'])

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">After Making Payment</h6>
                            <ol class="mb-0">
                                <li class="mb-2">Keep your bank receipt or transaction reference</li>
                                <li class="mb-2">Contact the accounts office or submit proof of payment</li>
                                <li class="mb-2">Payment will be verified within 1-3 business days</li>
                                <li>Your account will be updated once payment is confirmed</li>
                            </ol>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Transaction History --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Nov 10, 2025</td>
                                    <td>Term 1 Tuition Fee</td>
                                    <td>{{ formatMoney(2000000) }}</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <em>No more transactions</em>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
