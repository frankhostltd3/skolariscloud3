@extends('tenant.layouts.app')

@section('title', 'Make Payment')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-phone me-2"></i>Mobile Money Payment
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($gateways->isEmpty())
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle me-2"></i>
                                No mobile money payment methods are currently available.
                                @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                                    <div class="mt-2">
                                        <a href="{{ route('tenant.settings.mobile-money.index') }}"
                                            class="btn btn-sm btn-outline-dark">
                                            <i class="bi bi-gear me-1"></i> Configure Gateways
                                        </a>
                                    </div>
                                @else
                                    Please contact the administrator.
                                @endif
                            </div>
                        @else
                            <form method="POST" action="{{ route('tenant.payments.mobile-money.store') }}"
                                id="paymentForm">
                                @csrf

                                {{-- Invoice Info (if applicable) --}}
                                @if ($invoice)
                                    <div class="alert alert-info mb-4">
                                        <h6 class="mb-2">
                                            <i class="bi bi-receipt me-2"></i>Paying for Invoice
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Invoice:</strong> {{ $invoice->invoice_number }}<br>
                                                <strong>Fee:</strong> {{ $invoice->feeStructure->name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Total:</strong> {{ formatMoney($invoice->total_amount) }}<br>
                                                <strong>Balance Due:</strong> <span
                                                    class="text-danger">{{ formatMoney($invoice->balance) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                @endif

                                {{-- Payment Method Selection --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-wallet2 me-1"></i>Select Payment Method
                                    </label>
                                    <div class="row g-3">
                                        @foreach ($gateways as $gateway)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check payment-option">
                                                    <input class="form-check-input visually-hidden" type="radio"
                                                        name="gateway_id" id="gateway{{ $gateway->id }}"
                                                        value="{{ $gateway->id }}"
                                                        {{ $gateway->is_default || $loop->first ? 'checked' : '' }}
                                                        required>
                                                    <label class="form-check-label w-100" for="gateway{{ $gateway->id }}">
                                                        <div
                                                            class="card payment-card h-100 {{ $gateway->is_default || $loop->first ? 'selected' : '' }}">
                                                            <div class="card-body text-center p-3">
                                                                <div class="gateway-icon mb-2">
                                                                    @switch($gateway->provider)
                                                                        @case('mtn_momo')
                                                                            <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 50px; height: 50px;">
                                                                                <span class="fw-bold text-dark">MTN</span>
                                                                            </div>
                                                                        @break

                                                                        @case('mpesa')
                                                                            <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 50px; height: 50px;">
                                                                                <span class="fw-bold text-white fs-6">M</span>
                                                                            </div>
                                                                        @break

                                                                        @case('airtel_money')
                                                                            <div class="rounded-circle bg-danger d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 50px; height: 50px;">
                                                                                <span class="fw-bold text-white fs-6">A</span>
                                                                            </div>
                                                                        @break

                                                                        @case('flutterwave')
                                                                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 50px; height: 50px;">
                                                                                <i class="bi bi-credit-card text-white"></i>
                                                                            </div>
                                                                        @break

                                                                        @default
                                                                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 50px; height: 50px;">
                                                                                <i class="bi bi-phone text-white"></i>
                                                                            </div>
                                                                    @endswitch
                                                                </div>
                                                                <h6 class="mb-1">{{ $gateway->name }}</h6>
                                                                @if ($gateway->is_default)
                                                                    <span class="badge bg-success small">Recommended</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('gateway_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Amount --}}
                                <div class="mb-4">
                                    <label for="amount" class="form-label fw-bold">
                                        <i class="bi bi-cash me-1"></i>Amount
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">{{ currentCurrency()->symbol ?? 'UGX' }}</span>
                                        <input type="number" step="0.01" min="1"
                                            class="form-control @error('amount') is-invalid @enderror" id="amount"
                                            name="amount" value="{{ old('amount', $defaultAmount) }}"
                                            placeholder="Enter amount" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone Number --}}
                                <div class="mb-4">
                                    <label for="phone_number" class="form-label fw-bold">
                                        <i class="bi bi-telephone me-1"></i>Mobile Money Phone Number
                                    </label>
                                    <input type="tel"
                                        class="form-control form-control-lg @error('phone_number') is-invalid @enderror"
                                        id="phone_number" name="phone_number"
                                        value="{{ old('phone_number', $defaultPhone) }}"
                                        placeholder="e.g., 0772123456 or +256772123456" required>
                                    <div class="form-text">Enter the mobile money registered phone number</div>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description (optional) --}}
                                <div class="mb-4">
                                    <label for="description" class="form-label">
                                        <i class="bi bi-card-text me-1"></i>Payment Description (Optional)
                                    </label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" value="{{ old('description') }}"
                                        placeholder="e.g., School Fees Term 1">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Submit --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="bi bi-send me-2"></i>Send Payment Request
                                    </button>
                                </div>
                            </form>

                            {{-- Instructions --}}
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6><i class="bi bi-info-circle me-2"></i>How It Works</h6>
                                <ol class="mb-0 ps-3">
                                    <li>Select your mobile money provider</li>
                                    <li>Enter the amount and your mobile money phone number</li>
                                    <li>Click "Send Payment Request"</li>
                                    <li>You will receive a prompt on your phone to confirm the payment</li>
                                    <li>Enter your mobile money PIN to complete the payment</li>
                                </ol>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #dee2e6;
        }

        .payment-card:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
        }

        .payment-card.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }

        .payment-option .form-check-input:checked+.form-check-label .payment-card {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle payment method selection styling
                document.querySelectorAll('input[name="gateway_id"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        document.querySelectorAll('.payment-card').forEach(function(card) {
                            card.classList.remove('selected');
                        });
                        this.closest('.payment-option').querySelector('.payment-card').classList.add(
                            'selected');
                    });
                });

                // Form submission handling
                document.getElementById('paymentForm').addEventListener('submit', function(e) {
                    var btn = document.getElementById('submitBtn');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                });
            });
        </script>
    @endpush
@endsection
