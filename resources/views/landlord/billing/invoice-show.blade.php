@extends('landlord.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 fw-semibold mb-2">📄 {{ __('Invoice') }} #{{ $invoice->invoice_number }}</h1>
                    <p class="text-secondary mb-0">
                        {{ __('Issued') }}: {{ $invoice->created_at->format('M d, Y') }}
                        @if($invoice->due_date)
                            | {{ __('Due') }}: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                        @endif
                    </p>
                </div>
                <div>
                    @if($invoice->status === 'paid')
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ __('Paid') }}
                        </span>
                    @elseif($invoice->status === 'pending')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="bi bi-clock me-1"></i>
                            {{ __('Pending') }}
                        </span>
                    @elseif($invoice->status === 'overdue')
                        <span class="badge bg-danger fs-6 px-3 py-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            {{ __('Overdue') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Invoice Details -->
        <div class="col-lg-8">
            <!-- Invoice Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="fw-semibold mb-3">{{ __('Bill To') }}:</h5>
                            <p class="mb-1"><strong>{{ $invoice->tenant->name ?? $invoice->tenant_id }}</strong></p>
                            @if($invoice->tenant_email)
                                <p class="mb-1 text-secondary">{{ $invoice->tenant_email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="fw-semibold mb-3">{{ __('From') }}:</h5>
                            <p class="mb-1"><strong>{{ config('app.name') }}</strong></p>
                            <p class="mb-1 text-secondary">{{ config('mail.from.address') }}</p>
                        </div>
                    </div>

                    @if($invoice->description)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ $invoice->description }}
                        </div>
                    @endif

                    <!-- Line Items -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ $invoice->currency ?? 'USD' }} {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">{{ $invoice->currency ?? 'USD' }} {{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary">{{ __('No items') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">{{ __('Total') }}:</td>
                                    <td class="text-end fw-bold fs-5">
                                        {{ $invoice->currency ?? 'USD' }} {{ number_format($invoice->amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($transactions->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">💳 {{ __('Payment History') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Gateway') }}</th>
                                        <th>{{ __('Transaction ID') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ strtoupper($transaction->gateway) }}
                                                </span>
                                            </td>
                                            <td><small class="text-monospace">{{ Str::limit($transaction->transaction_id, 20) }}</small></td>
                                            <td>{{ $transaction->formatted_amount }}</td>
                                            <td>
                                                @if($transaction->status === 'completed')
                                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                                @elseif($transaction->status === 'pending')
                                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                                @elseif($transaction->status === 'failed')
                                                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Payment Actions -->
        <div class="col-lg-4">
            @if($invoice->status !== 'paid')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-semibold">💰 {{ __('Make Payment') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="fw-semibold mb-2">{{ __('Please fix the following:') }}</div>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($activeGateways->count() > 0)
                            <p class="text-secondary mb-3">{{ __('Choose a payment method to complete this invoice') }}</p>
                            
                            <form action="{{ route('landlord.invoices.pay', $invoice) }}" method="POST" id="paymentForm">
                                @csrf
                                
                                <!-- Gateway Selection -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">{{ __('Payment Gateway') }}</label>
                                    @foreach($activeGateways as $gateway)
                                        <div class="form-check mb-2 p-3 border rounded gateway-option" 
                                             style="cursor: pointer;"
                                             onclick="selectGateway('{{ $gateway['gateway'] }}')">
                                            <input class="form-check-input" type="radio" name="gateway" 
                                                   id="gateway_{{ $gateway['gateway'] }}" 
                                                   value="{{ $gateway['gateway'] }}" 
                                                   data-requires-email="{{ $gateway['requires_email'] ? 'true' : 'false' }}"
                                                   data-requires-phone="{{ $gateway['requires_phone'] ? 'true' : 'false' }}"
                                                   required>
                                            <label class="form-check-label w-100" for="gateway_{{ $gateway['gateway'] }}" style="cursor: pointer;">
                                                <div class="d-flex align-items-center">
                                                    <span class="fs-4 me-2">{{ $gateway['logo'] }}</span>
                                                    <strong>{{ $gateway['name'] }}</strong>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Payer Email (for PayPal, Flutterwave) -->
                                <div class="mb-3 d-none" id="emailField">
                                    <label for="payer_email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="payer_email" name="payer_email" 
                                           placeholder="your@email.com">
                                </div>

                                <!-- Payer Phone (for M-PESA, MTN, Airtel) -->
                                <div class="mb-3 d-none" id="phoneField">
                                    <label for="payer_phone" class="form-label">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="payer_phone" name="payer_phone" 
                                           placeholder="254712345678">
                                    <small class="form-text text-muted">{{ __('Include country code (e.g., 254 for Kenya)') }}</small>
                                </div>

                                <!-- Payer Name -->
                                <div class="mb-3">
                                    <label for="payer_name" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" id="payer_name" name="payer_name" 
                                           value="{{ $invoice->tenant->name ?? '' }}" 
                                           placeholder="{{ __('Your name or company') }}">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-credit-card me-2"></i>
                                        {{ __('Pay') }} {{ $invoice->currency ?? 'USD' }} {{ number_format($invoice->amount, 2) }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ __('No payment gateways are currently active. Please contact support.') }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-check-circle display-1 mb-3"></i>
                        <h4 class="fw-semibold">{{ __('Payment Received') }}</h4>
                        <p class="mb-0">{{ __('This invoice has been paid') }}</p>
                        @if($invoice->paid_at)
                            <small>{{ __('Paid on') }}: {{ \Carbon\Carbon::parse($invoice->paid_at)->format('M d, Y H:i') }}</small>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Invoice Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('landlord.billing.invoices') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            {{ __('Back to Invoices') }}
                        </a>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>
                            {{ __('Print Invoice') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gateway-option:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd !important;
}

.gateway-option:has(input:checked) {
    background-color: #e7f3ff;
    border-color: #0d6efd !important;
}

@media print {
    .btn, .card:has(form), nav, .sidebar {
        display: none !important;
    }
}
</style>

<script>
function selectGateway(gateway) {
    // Get the radio button
    const radio = document.getElementById(`gateway_${gateway}`);
    radio.checked = true;
    
    // Get requirements
    const requiresEmail = radio.dataset.requiresEmail === 'true';
    const requiresPhone = radio.dataset.requiresPhone === 'true';
    
    // Show/hide fields
    const emailField = document.getElementById('emailField');
    const phoneField = document.getElementById('phoneField');
    const emailInput = document.getElementById('payer_email');
    const phoneInput = document.getElementById('payer_phone');
    
    if (requiresEmail) {
        emailField.classList.remove('d-none');
        emailInput.required = true;
    } else {
        emailField.classList.add('d-none');
        emailInput.required = false;
    }
    
    if (requiresPhone) {
        phoneField.classList.remove('d-none');
        phoneInput.required = true;
    } else {
        phoneField.classList.add('d-none');
        phoneInput.required = false;
    }
}

// Initialize on page load if a gateway is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const checkedGateway = document.querySelector('input[name="gateway"]:checked');
    if (checkedGateway) {
        selectGateway(checkedGateway.value);
    }
});
</script>
@endsection
