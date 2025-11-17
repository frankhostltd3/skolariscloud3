@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.parent._sidebar')
@endsection

@section('title', __('Invoice Detail'))

@section('content')
@php
    $invoice = $invoice->loadMissing(['payments']);
    $payments = $invoice->payments;
    $wardId = null;
    foreach ($payments as $payment) {
        $wardId = $wardId ?? data_get($payment->meta, 'ward_id');
    }
    $confirmedTotal = $payments->where('status', 'confirmed')->sum('amount');
    $currencyCode = $invoice->currency ?? (function_exists('currency_code') ? currency_code() : 'USD');
    $balanceAmount = max(0, (float) $invoice->total_amount - (float) $confirmedTotal);
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ __('Invoice overview') }}</h2>
        <p class="text-muted mb-0">{{ __('Review balances, payment allocations, and escrow notes for this invoice.') }}</p>
    </div>
    <a href="{{ route('tenant.parent.fees.index', ['student_id' => $wardId]) }}" class="btn btn-outline-parent">
        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to fees') }}
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="small text-white-75 mb-1">{{ __('Invoice number') }}</div>
                <div class="display-6 fw-bold">#{{ $invoice->id }}</div>
                <div class="small text-white-75">{{ __('Issued on :date', ['date' => optional($invoice->created_at)->format('M j, Y') ?? __('Unknown')]) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="fw-semibold text-muted">{{ __('Total amount') }}</div>
                <div class="display-6 fw-bold">{{ format_money($invoice->total_amount) }}</div>
                <div class="small text-muted">{{ __('Currency: :code', ['code' => strtoupper($currencyCode)]) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        @php
            $statusClass = $invoice->status === 'paid' ? 'success' : ($invoice->status === 'partial' ? 'warning' : 'secondary');
        @endphp
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="fw-semibold text-muted">{{ __('Status') }}</div>
                <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} fs-5">{{ ucfirst($invoice->status) }}</span>
                <div class="small text-muted mt-2">{{ __('Due date: :date', ['date' => optional($invoice->due_date)->format('M j, Y') ?? __('Not set')]) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>{{ __('Summary') }}</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3">
                    <div class="fw-semibold text-muted">{{ __('Amount confirmed') }}</div>
                    <div>{{ format_money($confirmedTotal) }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3">
                    <div class="fw-semibold text-muted">{{ __('Outstanding balance') }}</div>
                    <div>{{ format_money($invoice->balance ?? $balanceAmount) }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3">
                    <div class="fw-semibold text-muted">{{ __('Notes') }}</div>
                    <div>{{ $invoice->notes ?? __('No additional notes provided') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-receipt me-2 text-success"></i>{{ __('Payments applied') }}</h5>
        <span class="badge bg-light text-dark">{{ $payments->count() }}</span>
    </div>
    <div class="card-body">
        @if($payments->isEmpty())
            <div class="text-center py-4 text-muted">{{ __('No payments have been applied to this invoice yet.') }}</div>
        @else
            <div class="list-group list-group-flush">
                @foreach($payments as $payment)
                    @php
                        $statusClass = $payment->status === 'confirmed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning');
                        $holdUntil = data_get($payment->meta, 'hold_until');
                    @endphp
                    <a href="{{ route('tenant.parent.fees.payments.show', $payment) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ format_money($payment->amount) }} <span class="text-muted">{{ __('via') }} {{ strtoupper((string) $payment->method) }}</span></div>
                            <div class="small text-muted">{{ optional($payment->paid_at)->format('M j, Y g:i A') ?? __('Pending timestamp') }}</div>
                            @php
                                $holdLabel = null;
                                if ($holdUntil) {
                                    try {
                                        $holdLabel = \Illuminate\Support\Carbon::parse($holdUntil)->format('M j, Y g:i A');
                                    } catch (\Throwable $exception) {
                                        $holdLabel = null;
                                    }
                                }
                            @endphp
                            @if($holdLabel)
                                <div class="small text-muted">{{ __('Escrow clears by :date', ['date' => $holdLabel]) }}</div>
                            @endif
                        </div>
                        <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

