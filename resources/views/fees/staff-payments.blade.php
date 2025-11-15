@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="h3 fw-bold">Staff Salary & Payments</h1>
                <p class="text-muted">View your salary information and make any required payments</p>
            </div>

            {{-- Salary Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cash-stack me-2"></i>Current Month Salary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <small class="text-muted d-block">Gross Salary</small>
                            <h5 class="mb-0">{{ formatMoney(2500000) }}</h5>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <small class="text-muted d-block">Deductions</small>
                            <h5 class="mb-0 text-danger">{{ formatMoney(450000) }}</h5>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <small class="text-muted d-block">Net Pay</small>
                            <h5 class="mb-0 text-success">{{ formatMoney(2050000) }}</h5>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-success">Paid</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Outstanding Payments (if any staff fees like training, accommodation, etc.) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Other Payments</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        If you have any outstanding payments for staff accommodation, professional development courses,
                        or other services, they will appear here.
                    </p>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        You currently have no outstanding payments.
                    </div>

                    {{-- Example: If there were outstanding payments --}}
                    {{--
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Staff Accommodation - November</td>
                                    <td>{{ formatMoney(300000) }}</td>
                                    <td>Nov 30, 2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                            data-bs-target="#payment-options">
                                            Pay Now
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    --}}
                </div>
            </div>

            {{-- Bank Transfer Instructions (Always show for reference) --}}
            @if (bankPaymentInstructions())
                <div class="mb-4">
                    <h5 class="mb-3">Payment Information</h5>
                    <p class="text-muted">
                        If you need to make any payments to the school, use the bank details below:
                    </p>
                    @include('partials.bank-payment-instructions', [
                        'title' => 'School Bank Account Details',
                    ])
                </div>
            @endif

            {{-- Salary History --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Salary History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Gross Salary</th>
                                    <th>Deductions</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th>Slip</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>November 2025</td>
                                    <td>{{ formatMoney(2500000) }}</td>
                                    <td>{{ formatMoney(450000) }}</td>
                                    <td>{{ formatMoney(2050000) }}</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">Download</a></td>
                                </tr>
                                <tr>
                                    <td>October 2025</td>
                                    <td>{{ formatMoney(2500000) }}</td>
                                    <td>{{ formatMoney(450000) }}</td>
                                    <td>{{ formatMoney(2050000) }}</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">Download</a></td>
                                </tr>
                                <tr>
                                    <td>September 2025</td>
                                    <td>{{ formatMoney(2500000) }}</td>
                                    <td>{{ formatMoney(450000) }}</td>
                                    <td>{{ formatMoney(2050000) }}</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">Download</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
