@extends('tenant.layouts.app')
@section('title', 'Invoices')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Invoices</h1>
            <a href="{{ route('tenant.finance.invoices.create') }}" class="btn btn-primary"><i
                    class="bi bi-plus-circle me-1"></i> Create Invoice</a>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Invoices</h6>
                        <h3>{{ $stats['total_invoices'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Amount</h6>
                        <h3>{{ formatMoney($stats['total_amount']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Paid</h6>
                        <h3>{{ formatMoney($stats['paid_amount']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Outstanding</h6>
                        <h3>{{ formatMoney($stats['outstanding']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($invoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Student</th>
                                    <th>Fee</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->student->name }}</td>
                                        <td>{{ $invoice->feeStructure->fee_name }}</td>
                                        <td>{{ formatMoney($invoice->total_amount) }}</td>
                                        <td>{{ formatMoney($invoice->paid_amount) }}</td>
                                        <td>{{ formatMoney($invoice->balance) }}</td>
                                        <td><span
                                                class="badge {{ $invoice->status_badge_class }}">{{ ucfirst($invoice->status) }}</span>
                                        </td>
                                        <td><a href="{{ route('tenant.finance.invoices.show', $invoice) }}"
                                                class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $invoices->links() }}
                @else
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> No invoices found.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
