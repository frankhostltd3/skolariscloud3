@extends('tenant.layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('settings.currencies.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h4 fw-semibold mb-1">Add Currency</h1>
                    <p class="text-muted mb-0">Create a new currency for payment processing.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('settings.currencies.store') }}" novalidate>
                        @csrf

                        <div class="mb-4">
                            <label for="code" class="form-label fw-medium">
                                Currency Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code') }}" maxlength="3" required
                                placeholder="USD, EUR, GBP...">
                            <div class="form-text">3-letter ISO currency code (e.g., USD, EUR, GBP)</div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium">
                                Currency Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" maxlength="255" required
                                placeholder="US Dollar, Euro, British Pound...">
                            <div class="form-text">Full name of the currency</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="symbol" class="form-label fw-medium">
                                Currency Symbol <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('symbol') is-invalid @enderror" id="symbol"
                                name="symbol" value="{{ old('symbol') }}" maxlength="10" required
                                placeholder="$, €, £, UGX...">
                            <div class="form-text">Symbol used to display the currency (e.g., $, €, £)</div>
                            @error('symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="exchange_rate" class="form-label fw-medium">
                                Exchange Rate (to USD) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('exchange_rate') is-invalid @enderror"
                                id="exchange_rate" name="exchange_rate" value="{{ old('exchange_rate', '1.000000') }}"
                                step="0.000001" min="0.000001" max="999999999" required>
                            <div class="form-text">
                                Exchange rate relative to USD (1.0). For example, if 1 USD = 3700 UGX, enter 3700.
                            </div>
                            @error('exchange_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <div class="form-text">Only active currencies can be used for payments</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="auto_update_enabled"
                                    name="auto_update_enabled" value="1"
                                    {{ old('auto_update_enabled', false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_update_enabled">
                                    Enable Auto-Update
                                </label>
                            </div>
                            <div class="form-text">Automatically update exchange rate daily from external API</div>
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>
                                <strong>Exchange Rate Reference:</strong> All exchange rates are relative to USD (1.0).
                                Use current market rates for accurate currency conversion.
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('settings.currencies.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create Currency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
