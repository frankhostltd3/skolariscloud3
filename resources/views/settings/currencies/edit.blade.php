@extends('tenant.layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('tenant.settings.admin.currencies.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h4 fw-semibold mb-1">Edit Currency</h1>
                    <p class="text-muted mb-0">Update currency details and exchange rate.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('tenant.settings.admin.currencies.update', $currency) }}"
                        novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="code" class="form-label fw-medium">
                                Currency Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code', $currency->code) }}" maxlength="3" required
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
                                name="name" value="{{ old('name', $currency->name) }}" maxlength="255" required
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
                                name="symbol" value="{{ old('symbol', $currency->symbol) }}" maxlength="10" required
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
                                id="exchange_rate" name="exchange_rate"
                                value="{{ old('exchange_rate', $currency->exchange_rate) }}" step="0.000001" min="0.000001"
                                max="999999999" required>
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
                                    value="1" {{ old('is_active', $currency->is_active) ? 'checked' : '' }}
                                    @if ($currency->is_default) disabled @endif>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <div class="form-text">
                                @if ($currency->is_default)
                                    Default currency cannot be deactivated
                                @else
                                    Only active currencies can be used for payments
                                @endif
                            </div>
                        </div>

                        @if ($currency->code !== 'USD')
                            <div class="mb-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="auto_update_enabled"
                                        name="auto_update_enabled" value="1"
                                        {{ old('auto_update_enabled', $currency->auto_update_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_update_enabled">
                                        Enable Auto-Update
                                    </label>
                                </div>
                                <div class="form-text">
                                    Automatically update exchange rate daily from external API
                                    @if ($currency->last_updated_at)
                                        (Last updated: {{ $currency->last_updated_at->format('M d, Y g:i A') }})
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($currency->is_default)
                            <div class="alert alert-primary d-flex align-items-start gap-2">
                                <i class="bi bi-star-fill mt-1"></i>
                                <div>
                                    <strong>Default Currency:</strong> This currency is set as the default for all payment
                                    transactions. It cannot be deactivated or deleted.
                                </div>
                            </div>
                        @endif

                        <div class="alert alert-info d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>
                                <strong>Exchange Rate Reference:</strong> All exchange rates are relative to USD (1.0).
                                Use current market rates for accurate currency conversion.
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('tenant.settings.admin.currencies.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Currency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
