@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.parent._sidebar')
@endsection

@section('title', __('Payment Receipt'))

@section('content')
@php
    $payment = $payment->loadMissing('invoice');
    $invoice = $payment->invoice;
    $holdUntil = $holdUntil ?? null;
    $wardId = data_get($payment->meta, 'ward_id');
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ __('Payment receipt') }}</h2>
        <p class="text-muted mb-0">{{ __('Track escrow status and confirmation details for this transaction.') }}</p>
    </div>
    <a href="{{ route('tenant.parent.fees.index', ['student_id' => $wardId]) }}" class="btn btn-outline-parent">
        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to fees') }}
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="small text-white-75 mb-1">{{ __('Amount paid') }}</div>
                <div class="display-6 fw-bold">{{ format_money($payment->amount) }}</div>
                <div class="small text-white-75">{{ __('Submitted on :date', ['date' => optional($payment->paid_at)->format('M j, Y g:i A') ?? __('Unknown')]) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-semibold text-muted">{{ __('Reference') }}</div>
                    <span class="badge bg-primary">{{ $payment->reference }}</span>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="fw-semibold">{{ __('Payment method') }}</div>
                            <div class="text-muted">{{ strtoupper($payment->method) }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        @php
                            $status = ucfirst($payment->status);
                            $statusClass = $payment->status === 'confirmed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning');
                        @endphp
                        <div class="border rounded-3 p-3">
                            <div class="fw-semibold">{{ __('Status') }}</div>
                            <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">{{ $status }}</span>
                        </div>
                    </div>
                    @if($holdUntil)
                        <div class="col-12">
                            <div class="alert alert-info mb-0" role="alert">
                                <div class="fw-semibold mb-1">{{ __('Escrow hold in place') }}</div>
                                <p class="mb-0 small">{{ __('Funds will clear to the school by :date unless the payment is rejected. You will receive a confirmation once the status changes.', ['date' => $holdUntil->format('M j, Y g:i A')]) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>{{ __('Payment metadata') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted">{{ __('Created at') }}</th>
                        <td>{{ optional($payment->created_at)->format('M j, Y g:i A') ?? __('Unknown') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Updated at') }}</th>
                        <td>{{ optional($payment->updated_at)->format('M j, Y g:i A') ?? __('Unknown') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Notes') }}</th>
                        <td>{{ data_get($payment->meta, 'notes') ?? __('No notes supplied') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Fee reference') }}</th>
                        <td>{{ data_get($payment->meta, 'fee_id') ?? __('Not captured') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Captured via') }}</th>
                        <td>{{ data_get($payment->meta, 'source') ?? __('Parent portal') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($invoice)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>{{ __('Linked invoice') }}</h5>
            <a href="{{ route('tenant.parent.fees.invoices.show', $invoice) }}" class="btn btn-outline-parent btn-sm">
                <i class="fas fa-eye me-1"></i>{{ __('Open invoice') }}
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3">
                        <div class="fw-semibold text-muted">{{ __('Invoice number') }}</div>
                        <div>{{ __('Invoice #:number', ['number' => $invoice->id]) }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3">
                        <div class="fw-semibold text-muted">{{ __('Total amount') }}</div>
                        <div>{{ format_money($invoice->total_amount) }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3">
                        <div class="fw-semibold text-muted">{{ __('Status') }}</div>
                        <div>{{ ucfirst($invoice->status) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

