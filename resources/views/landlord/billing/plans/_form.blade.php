<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-grid gap-3">
                <div>
                    <label for="name" class="form-label fw-semibold">{{ __('Plan name') }}</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $plan->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="slug" class="form-label">{{ __('Slug') }} <span class="text-secondary small">{{ __('(auto generates if left blank)') }}</span></label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $plan->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tagline" class="form-label">{{ __('Tagline') }}</label>
                        <input type="text" name="tagline" id="tagline" class="form-control @error('tagline') is-invalid @enderror" value="{{ old('tagline', $plan->tagline) }}">
                        @error('tagline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="form-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="price_amount" class="form-label">{{ __('Price amount') }}</label>
                        <input type="number" step="0.01" min="0" name="price_amount" id="price_amount" class="form-control @error('price_amount') is-invalid @enderror" value="{{ old('price_amount', $plan->price_amount) }}">
                        <div class="form-text">{{ __('Leave blank when using a custom price label.') }}</div>
                        @error('price_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="price_display" class="form-label">{{ __('Price label') }}</label>
                        <input type="text" name="price_display" id="price_display" class="form-control @error('price_display') is-invalid @enderror" value="{{ old('price_display', $plan->price_display) }}">
                        @error('price_display')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="currency" class="form-label">{{ __('Currency') }}</label>
                        <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency['code'] }}" @selected(old('currency', $plan->currency) === $currency['code'])>
                                    {{ $currency['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="billing_period" class="form-label">{{ __('Billing period code') }}</label>
                        <input type="text" name="billing_period" id="billing_period" class="form-control @error('billing_period') is-invalid @enderror" value="{{ old('billing_period', $plan->billing_period) }}" required>
                        @error('billing_period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="billing_period_label" class="form-label">{{ __('Billing period label') }}</label>
                        <input type="text" name="billing_period_label" id="billing_period_label" class="form-control @error('billing_period_label') is-invalid @enderror" value="{{ old('billing_period_label', $plan->billing_period_label) }}" required>
                        @error('billing_period_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="cta_label" class="form-label">{{ __('Call-to-action label') }}</label>
                        <input type="text" name="cta_label" id="cta_label" class="form-control @error('cta_label') is-invalid @enderror" value="{{ old('cta_label', $plan->cta_label) }}">
                        @error('cta_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="position" class="form-label">{{ __('Position order') }}</label>
                        <input type="number" min="0" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $plan->position ?? 0) }}">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="features" class="form-label">{{ __('Plan features (one per line)') }}</label>
                    @php
                        $featuresValue = old('features');
                        if ($featuresValue === null) {
                            $featuresValue = is_array($plan->features) ? implode("\n", $plan->features) : '';
                        }
                    @endphp
                    <textarea name="features" id="features" rows="5" class="form-control @error('features') is-invalid @enderror">{{ $featuresValue }}</textarea>
                    <div class="form-text">{{ __('Use concise bullet points to highlight what this package includes.') }}</div>
                    @error('features')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-grid gap-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $plan->is_active) ?? true)>
                    <label class="form-check-label fw-semibold" for="is_active">{{ __('Display publicly') }}</label>
                    <div class="form-text">{{ __('Toggle visibility on the landing page and parent dashboard.') }}</div>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_highlighted" name="is_highlighted" value="1" @checked(old('is_highlighted', $plan->is_highlighted))>
                    <label class="form-check-label fw-semibold" for="is_highlighted">{{ __('Highlight as popular') }}</label>
                    <div class="form-text">{{ __('Adds a badge and accent colour to emphasise this plan.') }}</div>
                </div>

                <div class="alert alert-info small mb-0">
                    <span class="bi bi-info-circle me-1"></span>
                    {{ __('Need region-specific pricing? Duplicate a plan and set currency and label accordingly.') }}
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span class="bi bi-save me-1"></span>{{ __('Save plan') }}
                    </button>
                    <a href="{{ route('landlord.billing.plans.index') }}" class="btn btn-outline-secondary">
                        {{ __('Back to plans') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
