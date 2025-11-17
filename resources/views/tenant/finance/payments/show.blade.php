@extends('tenant.layouts.app')
@section('title', 'Payment Details')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Payment Receipt #{{ $payment->receipt_number }}</h1>
            <div>
                <a href="{{ route('tenant.finance.payments.receipt', $payment) }}" class="btn btn-success" target="_blank"><i
                        class="bi bi-printer me-1"></i> Print Receipt</a>
                <a href="{{ route('tenant.finance.payments.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Payment Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Receipt Number:</th>
                                <td>{{ $payment->receipt_number }}</td>
                            </tr>
                            <tr>
                                <th>Payment Date:</th>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td><strong>{{ formatMoney($payment->amount) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>{{ $payment->payment_method_label }}</td>
                            </tr>
                            <tr>
                                <th>Reference Number:</th>
                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Invoice Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Invoice Number:</th>
                                <td><a
                                        href="{{ route('tenant.finance.invoices.show', $payment->invoice) }}">{{ $payment->invoice->invoice_number }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th>Student:</th>
                                <td>{{ $payment->invoice->student->name }}</td>
                            </tr>
                            <tr>
                                <th>Fee Type:</th>
                                <td>{{ $payment->invoice->feeStructure->fee_name }}</td>
                            </tr>
                            <tr>
                                <th>Invoice Total:</th>
                                <td>{{ formatMoney($payment->invoice->total_amount) }}</td>
                            </tr>
                            <tr>
                                <th>Total Paid:</th>
                                <td>{{ formatMoney($payment->invoice->paid_amount) }}</td>
                            </tr>
                            <tr>
                                <th>Balance:</th>
                                <td><strong>{{ formatMoney($payment->invoice->balance) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if ($payment->notes)
                    <div class="alert alert-info"><strong>Notes:</strong> {{ $payment->notes }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
