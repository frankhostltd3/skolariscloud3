@extends('tenant.layouts.app')
@section('title', 'Invoice Details')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Invoice #{{ $invoice->invoice_number }}</h1>
            <div class="d-flex gap-2">
                <!-- Send Invoice Dropdown -->
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-envelope me-1"></i> Send Invoice
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('tenant.finance.invoices.send-student', $invoice) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-person me-2"></i> Send to Student
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('tenant.finance.invoices.send-parent', $invoice) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-people me-2"></i> Send to Parent(s)
                                </button>
                            </form>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('tenant.finance.invoices.send-both', $invoice) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-send me-2"></i> Send to Both
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('tenant.finance.invoices.edit', $invoice) }}" class="btn btn-warning"><i
                        class="bi bi-pencil me-1"></i> Edit</a>
                <a href="{{ route('tenant.finance.invoices.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Student Information</h5>
                        <p><strong>Name:</strong> {{ $invoice->student->name }}<br>
                            <strong>Email:</strong> {{ $invoice->student->email }}
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>Invoice Details</h5>
                        <p><strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                            <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                            <strong>Status:</strong> <span
                                class="badge {{ $invoice->status_badge_class }}">{{ ucfirst($invoice->status) }}</span>
                        </p>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fee Description</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $invoice->feeStructure->fee_name }}</td>
                            <td>{{ ucfirst($invoice->feeStructure->fee_type) }}</td>
                            <td class="text-end">{{ formatMoney($invoice->total_amount) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-end">Total:</th>
                            <th class="text-end">{{ formatMoney($invoice->total_amount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-end">Paid:</th>
                            <th class="text-end">{{ formatMoney($invoice->paid_amount) }}</th>
                        </tr>
                        <tr class="table-warning">
                            <th colspan="2" class="text-end">Balance:</th>
                            <th class="text-end">{{ formatMoney($invoice->balance) }}</th>
                        </tr>
                    </tfoot>
                </table>
                @if ($invoice->notes)
                    <div class="alert alert-info mt-3"><strong>Notes:</strong> {{ $invoice->notes }}</div>
                @endif
                @if ($invoice->payments->count() > 0)
                    <h5 class="mt-4">Payment History</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->payments as $payment)
                                <tr>
                                    <td><a
                                            href="{{ route('tenant.finance.payments.show', $payment) }}">{{ $payment->receipt_number }}</a>
                                    </td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>{{ formatMoney($payment->amount) }}</td>
                                    <td>{{ $payment->payment_method_label }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
