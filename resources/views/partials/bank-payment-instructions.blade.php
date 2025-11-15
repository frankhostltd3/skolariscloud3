{{--
    Bank Payment Instructions Partial

    Usage:
    @include('partials.bank-payment-instructions')

    Or with custom title:
    @include('partials.bank-payment-instructions', ['title' => 'Custom Title'])

    This component will only display if bank transfer is enabled in Payment Settings
--}}

@php
    $bankDetails = bankPaymentInstructions();
    $displayTitle = $title ?? 'Bank Transfer Payment Instructions';
@endphp

@if ($bankDetails)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-bank me-2"></i>{{ $displayTitle }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @if (!empty($bankDetails['bank_name']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">Bank Name</small>
                            <strong>{{ $bankDetails['bank_name'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['account_name']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">Account Name / Beneficiary</small>
                            <strong>{{ $bankDetails['account_name'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['account_number']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">Account Number</small>
                            <strong class="text-primary">{{ $bankDetails['account_number'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['branch_name']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">Branch Name</small>
                            <strong>{{ $bankDetails['branch_name'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['branch_code']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">Branch Code / Sort Code</small>
                            <strong>{{ $bankDetails['branch_code'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['swift_code']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">SWIFT / BIC Code</small>
                            <strong>{{ $bankDetails['swift_code'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['iban']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">IBAN</small>
                            <strong>{{ $bankDetails['iban'] }}</strong>
                        </div>
                    </div>
                @endif

                @if (!empty($bankDetails['routing_number']))
                    <div class="col-md-6">
                        <div class="mb-0">
                            <small class="text-muted d-block">Routing Number / ABA</small>
                            <strong>{{ $bankDetails['routing_number'] }}</strong>
                        </div>
                    </div>
                @endif
            </div>

            @if (!empty($bankDetails['payment_instructions']))
                <div class="alert alert-info mt-3 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                        <div>
                            <strong>Instructions:</strong><br>
                            {{ $bankDetails['payment_instructions'] }}
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($bankDetails['additional_info']))
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted d-block mb-1">Additional Information</small>
                    <div style="white-space: pre-line;">{{ $bankDetails['additional_info'] }}</div>
                </div>
            @endif

            <div class="mt-3 pt-3 border-top">
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    Bank transfers typically take 1-3 business days to process. Please keep your payment receipt for
                    verification.
                </small>
            </div>
        </div>
    </div>
@endif
