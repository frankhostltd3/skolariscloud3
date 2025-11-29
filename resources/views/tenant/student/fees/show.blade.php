@extends('layouts.tenant.student')

@section('title', 'Invoice Details - ' . $invoice->invoice_number)

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>{{ $invoice->invoice_number }}
                </h4>
                <p class="text-muted mb-0">{{ __('Invoice Details & Payment History') }}</p>
            </div>
            <a href="{{ route('tenant.student.fees.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Fees') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Invoice Details -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Invoice Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Invoice Number:</strong>
                                <p>{{ $invoice->invoice_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>
                                    <span
                                        class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'partial' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Issue Date:</strong>
                                <p>{{ $invoice->issue_date->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Due Date:</strong>
                                <p>{{ $invoice->due_date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Description:</strong>
                                <p>{{ $invoice->feeStructure->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total Amount:</strong>
                                <p class="h5">{{ format_money($invoice->total_amount) }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Paid Amount:</strong>
                                <p class="h5 text-success">{{ format_money($invoice->paid_amount) }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Balance Due:</strong>
                                <p class="h5 text-danger">{{ format_money($invoice->balance) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoice->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->receipt_number }}</td>
                                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td>{{ format_money($payment->amount) }}</td>
                                            <td>
                                                <span class="badge badge-success">Paid</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No payments recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        @if ($invoice->balance > 0)
                            <a href="{{ route('tenant.student.fees.pay', $invoice) }}"
                                class="btn btn-success btn-block mb-3">
                                <i class="fas fa-credit-card"></i> Pay Now
                            </a>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> This invoice is fully paid.
                            </div>
                        @endif

                        <a href="#" class="btn btn-info btn-block mb-3" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
