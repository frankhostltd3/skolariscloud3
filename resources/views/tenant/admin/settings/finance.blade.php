@extends('tenant.layouts.app')

@section('title', __('Finance Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Finance Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.finance.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Currency Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Currency Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="default_currency" class="form-label">{{ __('Default Currency') }}</label>
                                <select class="form-select @error('default_currency') is-invalid @enderror" id="default_currency" name="default_currency" required>
                                    @foreach($currencies ?? [] as $currency)
                                        <option value="{{ $currency->code }}" {{ old('default_currency', setting('default_currency', 'USD')) == $currency->code ? 'selected' : '' }}>
                                            {{ $currency->code }} - {{ $currency->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('default_currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="currency_symbol_position" class="form-label">{{ __('Currency Symbol Position') }}</label>
                                <select class="form-select @error('currency_symbol_position') is-invalid @enderror" id="currency_symbol_position" name="currency_symbol_position">
                                    <option value="before" {{ old('currency_symbol_position', setting('currency_symbol_position', 'before')) == 'before' ? 'selected' : '' }}>Before amount ($100)</option>
                                    <option value="after" {{ old('currency_symbol_position', setting('currency_symbol_position', 'before')) == 'after' ? 'selected' : '' }}>After amount (100$)</option>
                                </select>
                                @error('currency_symbol_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="decimal_places" class="form-label">{{ __('Decimal Places') }}</label>
                                <select class="form-select @error('decimal_places') is-invalid @enderror" id="decimal_places" name="decimal_places">
                                    <option value="0" {{ old('decimal_places', setting('decimal_places', 2)) == 0 ? 'selected' : '' }}>0</option>
                                    <option value="2" {{ old('decimal_places', setting('decimal_places', 2)) == 2 ? 'selected' : '' }}>2</option>
                                    <option value="3" {{ old('decimal_places', setting('decimal_places', 2)) == 3 ? 'selected' : '' }}>3</option>
                                </select>
                                @error('decimal_places')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fee Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Fee Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="fee_collection_method" class="form-label">{{ __('Fee Collection Method') }}</label>
                                <select class="form-select @error('fee_collection_method') is-invalid @enderror" id="fee_collection_method" name="fee_collection_method">
                                    <option value="termly" {{ old('fee_collection_method', setting('fee_collection_method', 'termly')) == 'termly' ? 'selected' : '' }}>Termly</option>
                                    <option value="monthly" {{ old('fee_collection_method', setting('fee_collection_method', 'termly')) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="annually" {{ old('fee_collection_method', setting('fee_collection_method', 'termly')) == 'annually' ? 'selected' : '' }}>Annually</option>
                                </select>
                                @error('fee_collection_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="late_fee_percentage" class="form-label">{{ __('Late Fee Percentage (%)') }}</label>
                                <input type="number" class="form-control @error('late_fee_percentage') is-invalid @enderror"
                                       id="late_fee_percentage" name="late_fee_percentage" min="0" max="100" step="0.01"
                                       value="{{ old('late_fee_percentage', setting('late_fee_percentage', 5)) }}" required>
                                @error('late_fee_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="late_fee_grace_period" class="form-label">{{ __('Late Fee Grace Period (days)') }}</label>
                                <input type="number" class="form-control @error('late_fee_grace_period') is-invalid @enderror"
                                       id="late_fee_grace_period" name="late_fee_grace_period" min="0"
                                       value="{{ old('late_fee_grace_period', setting('late_fee_grace_period', 7)) }}" required>
                                @error('late_fee_grace_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="fee_reminder_days" class="form-label">{{ __('Fee Reminder (days before due date)') }}</label>
                                <input type="number" class="form-control @error('fee_reminder_days') is-invalid @enderror"
                                       id="fee_reminder_days" name="fee_reminder_days" min="1"
                                       value="{{ old('fee_reminder_days', setting('fee_reminder_days', 3)) }}" required>
                                @error('fee_reminder_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Payment Methods') }}</h5>
                                <div id="payment-methods-container">
                                    @if(setting('payment_methods'))
                                        @foreach(json_decode(setting('payment_methods'), true) as $index => $method)
                                            <div class="payment-method-row mb-2">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="payment_methods[{{ $index }}][name]"
                                                               value="{{ $method['name'] }}" placeholder="Method name">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select" name="payment_methods[{{ $index }}][type]">
                                                            <option value="cash" {{ $method['type'] == 'cash' ? 'selected' : '' }}>Cash</option>
                                                            <option value="bank_transfer" {{ $method['type'] == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                            <option value="card" {{ $method['type'] == 'card' ? 'selected' : '' }}>Card</option>
                                                            <option value="mobile_money" {{ $method['type'] == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="payment_methods[{{ $index }}][enabled]"
                                                                   value="1" {{ $method['enabled'] ? 'checked' : '' }}>
                                                            <label class="form-check-label">Enabled</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-payment-method">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-payment-method">
                                    <i class="bi bi-plus"></i> {{ __('Add Payment Method') }}
                                </button>
                            </div>
                        </div>

                        <!-- Tax Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Tax Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="tax_enabled" class="form-label">{{ __('Enable Tax Calculation') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tax_enabled" name="tax_enabled" value="1"
                                           {{ old('tax_enabled', setting('tax_enabled', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tax_enabled">
                                        {{ __('Calculate taxes on fees') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tax_rate" class="form-label">{{ __('Tax Rate (%)') }}</label>
                                <input type="number" class="form-control @error('tax_rate') is-invalid @enderror"
                                       id="tax_rate" name="tax_rate" min="0" max="100" step="0.01"
                                       value="{{ old('tax_rate', setting('tax_rate', 0)) }}">
                                @error('tax_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tax_inclusive" class="form-label">{{ __('Tax Inclusive') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tax_inclusive" name="tax_inclusive" value="1"
                                           {{ old('tax_inclusive', setting('tax_inclusive', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tax_inclusive">
                                        {{ __('Tax is included in the displayed price') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Year -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Financial Year') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="financial_year_start" class="form-label">{{ __('Financial Year Start') }}</label>
                                <input type="date" class="form-control @error('financial_year_start') is-invalid @enderror"
                                       id="financial_year_start" name="financial_year_start"
                                       value="{{ old('financial_year_start', setting('financial_year_start', date('Y') . '-01-01')) }}" required>
                                @error('financial_year_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="financial_year_end" class="form-label">{{ __('Financial Year End') }}</label>
                                <input type="date" class="form-control @error('financial_year_end') is-invalid @enderror"
                                       id="financial_year_end" name="financial_year_end"
                                       value="{{ old('financial_year_end', setting('financial_year_end', date('Y') . '-12-31')) }}" required>
                                @error('financial_year_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save Finance Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let paymentMethodIndex = {{ setting('payment_methods') ? count(json_decode(setting('payment_methods'), true)) : 0 }};

    document.getElementById('add-payment-method').addEventListener('click', function() {
        const container = document.getElementById('payment-methods-container');
        const row = document.createElement('div');
        row.className = 'payment-method-row mb-2';
        row.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="payment_methods[${paymentMethodIndex}][name]" placeholder="Method name">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="payment_methods[${paymentMethodIndex}][type]">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_methods[${paymentMethodIndex}][enabled]" value="1" checked>
                        <label class="form-check-label">Enabled</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-payment-method">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(row);
        paymentMethodIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-payment-method') || e.target.closest('.remove-payment-method')) {
            e.target.closest('.payment-method-row').remove();
        }
    });
});
</script>
@endsection