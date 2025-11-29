@extends('tenant.layouts.app')

@section('title', 'Edit Mobile Money Gateway')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Edit Gateway: {{ $gateway->name }}</h1>
                        <p class="text-muted mb-0">Update mobile money gateway configuration</p>
                    </div>
                    <a href="{{ route('tenant.settings.mobile-money.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>

                <form action="{{ route('tenant.settings.mobile-money.update', $gateway) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Provider Info (Read-only) -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Provider Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Provider</label>
                                    <input type="text" class="form-control" value="{{ $gateway->provider_name }}"
                                        readonly>
                                    <input type="hidden" name="provider" value="{{ $gateway->provider }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gateway Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $gateway->name) }}" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Country Code</label>
                                    <select name="country_code" class="form-select">
                                        <option value="">Select Country</option>
                                        @php
                                            $countries = [
                                                'UG' => 'Uganda',
                                                'KE' => 'Kenya',
                                                'TZ' => 'Tanzania',
                                                'RW' => 'Rwanda',
                                                'GH' => 'Ghana',
                                                'NG' => 'Nigeria',
                                                'ZA' => 'South Africa',
                                                'ZM' => 'Zambia',
                                                'MW' => 'Malawi',
                                                'MZ' => 'Mozambique',
                                                'BW' => 'Botswana',
                                                'NA' => 'Namibia',
                                                'CI' => 'Ivory Coast',
                                                'SN' => 'Senegal',
                                                'CM' => 'Cameroon',
                                                'EG' => 'Egypt',
                                                'PH' => 'Philippines',
                                                'ID' => 'Indonesia',
                                                'IN' => 'India',
                                                'SG' => 'Singapore',
                                                'MY' => 'Malaysia',
                                                'TH' => 'Thailand',
                                                'VN' => 'Vietnam',
                                                'BR' => 'Brazil',
                                                'MX' => 'Mexico',
                                                'AR' => 'Argentina',
                                                'CO' => 'Colombia',
                                                'US' => 'United States',
                                                'GB' => 'United Kingdom',
                                                'FR' => 'France',
                                                'DE' => 'Germany',
                                            ];
                                        @endphp
                                        @foreach ($countries as $code => $name)
                                            <option value="{{ $code }}"
                                                {{ $gateway->country_code == $code ? 'selected' : '' }}>
                                                {{ $name }} ({{ $code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Currency Code</label>
                                    <select name="currency_code" class="form-select">
                                        <option value="">Select Currency</option>
                                        @php
                                            $currencies = [
                                                'UGX' => 'Ugandan Shilling',
                                                'KES' => 'Kenyan Shilling',
                                                'TZS' => 'Tanzanian Shilling',
                                                'RWF' => 'Rwandan Franc',
                                                'GHS' => 'Ghanaian Cedi',
                                                'NGN' => 'Nigerian Naira',
                                                'ZAR' => 'South African Rand',
                                                'ZMW' => 'Zambian Kwacha',
                                                'MWK' => 'Malawian Kwacha',
                                                'XOF' => 'CFA Franc BCEAO',
                                                'XAF' => 'CFA Franc BEAC',
                                                'EGP' => 'Egyptian Pound',
                                                'PHP' => 'Philippine Peso',
                                                'IDR' => 'Indonesian Rupiah',
                                                'INR' => 'Indian Rupee',
                                                'SGD' => 'Singapore Dollar',
                                                'MYR' => 'Malaysian Ringgit',
                                                'THB' => 'Thai Baht',
                                                'BRL' => 'Brazilian Real',
                                                'MXN' => 'Mexican Peso',
                                                'USD' => 'US Dollar',
                                                'EUR' => 'Euro',
                                                'GBP' => 'British Pound',
                                            ];
                                        @endphp
                                        @foreach ($currencies as $code => $name)
                                            <option value="{{ $code }}"
                                                {{ $gateway->currency_code == $code ? 'selected' : '' }}>
                                                {{ $code }} - {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Environment <span class="text-danger">*</span></label>
                                    <select name="environment" class="form-select" required>
                                        <option value="sandbox" {{ $gateway->environment == 'sandbox' ? 'selected' : '' }}>
                                            Sandbox (Testing)</option>
                                        <option value="production"
                                            {{ $gateway->environment == 'production' ? 'selected' : '' }}>Production (Live)
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Configuration -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-key me-2"></i>API Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-shield-lock me-2"></i>
                                <strong>Security:</strong> Leave credential fields empty to keep existing values. Only fill
                                to update.
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">API Base URL</label>
                                    <input type="url" name="api_base_url" class="form-control"
                                        value="{{ old('api_base_url', $gateway->api_base_url) }}"
                                        placeholder="https://api.provider.com">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Public Key / API Key</label>
                                    <input type="text" name="public_key" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->public_key)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Secret Key / API Secret</label>
                                    <input type="password" name="secret_key" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->secret_key)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">API User ID</label>
                                    <input type="text" name="api_user" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->api_user)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">API Password</label>
                                    <input type="password" name="api_password" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->api_password)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subscription Key</label>
                                    <input type="text" name="subscription_key" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->subscription_key)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Encryption Key</label>
                                    <input type="password" name="encryption_key" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->encryption_key)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Client ID</label>
                                    <input type="text" name="client_id" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->client_id)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Client Secret</label>
                                    <input type="password" name="client_secret" class="form-control"
                                        placeholder="Leave empty to keep existing">
                                    @if ($gateway->client_secret)
                                        <div class="form-text text-success"><i class="bi bi-check-circle"></i> Currently
                                            configured</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Merchant Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Merchant Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Merchant ID</label>
                                    <input type="text" name="merchant_id" class="form-control"
                                        value="{{ old('merchant_id', $gateway->merchant_id) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Merchant Name</label>
                                    <input type="text" name="merchant_name" class="form-control"
                                        value="{{ old('merchant_name', $gateway->merchant_name) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Short Code / Paybill</label>
                                    <input type="text" name="short_code" class="form-control"
                                        value="{{ old('short_code', $gateway->short_code) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Till Number</label>
                                    <input type="text" name="till_number" class="form-control"
                                        value="{{ old('till_number', $gateway->till_number) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number" class="form-control"
                                        value="{{ old('account_number', $gateway->account_number) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- URLs -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Callback URLs</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Callback URL</label>
                                    <input type="url" name="callback_url" class="form-control"
                                        value="{{ old('callback_url', $gateway->callback_url) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Return URL</label>
                                    <input type="url" name="return_url" class="form-control"
                                        value="{{ old('return_url', $gateway->return_url) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cancel URL</label>
                                    <input type="url" name="cancel_url" class="form-control"
                                        value="{{ old('cancel_url', $gateway->cancel_url) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Additional Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Supported Networks</label>
                                    <div class="row">
                                        @php
                                            $networks = [
                                                'MTN',
                                                'Airtel',
                                                'Vodafone',
                                                'Tigo',
                                                'Orange',
                                                'Safaricom',
                                                'Econet',
                                                'Telecel',
                                            ];
                                            $selectedNetworks = $gateway->supported_networks ?? [];
                                        @endphp
                                        @foreach ($networks as $network)
                                            <div class="col-6 col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="supported_networks[]"
                                                        value="{{ $network }}" class="form-check-input"
                                                        id="network_{{ $network }}"
                                                        {{ in_array($network, $selectedNetworks) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="network_{{ $network }}">{{ $network }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2">{{ old('description', $gateway->description) }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                            id="isActive" {{ $gateway->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isActive">Gateway Active</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" name="is_default" value="1" class="form-check-input"
                                            id="isDefault" {{ $gateway->is_default ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isDefault">Default Gateway</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('tenant.settings.mobile-money.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update Gateway
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
