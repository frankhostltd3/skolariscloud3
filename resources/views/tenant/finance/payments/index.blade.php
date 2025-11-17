@extends('tenant.layouts.app')
@section('title', 'Payments')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Payments</h1>
            <a href="{{ route('tenant.finance.payments.create') }}" class="btn btn-primary"><i
                    class="bi bi-plus-circle me-1"></i> Record Payment</a>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Payments</h6>
                        <h3>{{ $stats['total_payments'] }}</h3>
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
                        <h6>Today</h6>
                        <h3>{{ formatMoney($stats['today_amount']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>This Month</h6>
                        <h3>{{ formatMoney($stats['this_month']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_number }}</td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ $payment->invoice->student->name }}</td>
                                        <td>{{ $payment->invoice->invoice_number }}</td>
                                        <td>{{ formatMoney($payment->amount) }}</td>
                                        <td>{{ $payment->payment_method_label }}</td>
                                        <td>
                                            <a href="{{ route('tenant.finance.payments.show', $payment) }}"
                                                class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('tenant.finance.payments.receipt', $payment) }}"
                                                class="btn btn-sm btn-success" target="_blank"><i
                                                    class="bi bi-printer"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $payments->links() }}
                @else
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> No payments found.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
