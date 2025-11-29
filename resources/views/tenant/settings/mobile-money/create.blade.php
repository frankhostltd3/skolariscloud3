@extends('tenant.layouts.app')

@section('title', 'Add Mobile Money Gateway')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Add Mobile Money Gateway</h1>
                        <p class="text-muted mb-0">Configure a new mobile money payment provider</p>
                    </div>
                    <a href="{{ route('tenant.settings.mobile-money.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>

                <form action="{{ route('tenant.settings.mobile-money.store') }}" method="POST" id="gatewayForm">
                    @csrf

                    <!-- Provider Selection -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-1-circle me-2"></i>Select Provider</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Payment Provider <span class="text-danger">*</span></label>
                                    <select name="provider" id="providerSelect" class="form-select" required>
                                        @foreach ($providerList as $key => $provider)
                                            <option value="{{ $key }}"
                                                {{ $selectedProvider == $key ? 'selected' : '' }}
                                                data-countries="{{ json_encode($provider['countries']) }}"
                                                data-currencies="{{ json_encode($provider['currencies']) }}"
                                                data-required="{{ json_encode($provider['required_fields']) }}">
                                                {{ $provider['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gateway Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $providerConfig['name'] ?? '') }}"
                                        placeholder="e.g., MTN MoMo Uganda" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Country Code</label>
                                    <select name="country_code" id="countrySelect" class="form-select">
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
                                            <option value="{{ $code }}">{{ $name }} ({{ $code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Currency Code</label>
                                    <select name="currency_code" id="currencySelect" class="form-select">
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
                                            <option value="{{ $code }}">{{ $code }} - {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Environment <span class="text-danger">*</span></label>
                                    <select name="environment" class="form-select" required>
                                        <option value="sandbox">Sandbox (Testing)</option>
                                        <option value="production">Production (Live)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Configuration -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-2-circle me-2"></i>API Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Note:</strong> All credentials are encrypted before storage. Fill only the fields
                                required by your provider.
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3" id="apiBaseUrlField">
                                    <label class="form-label">API Base URL</label>
                                    <input type="url" name="api_base_url" class="form-control"
                                        placeholder="https://api.provider.com">
                                    <div class="form-text">The base URL for API requests</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="publicKeyField">
                                    <label class="form-label">Public Key / API Key</label>
                                    <input type="text" name="public_key" class="form-control"
                                        placeholder="pk_live_xxxxx">
                                </div>
                                <div class="col-md-6 mb-3" id="secretKeyField">
                                    <label class="form-label">Secret Key / API Secret</label>
                                    <input type="password" name="secret_key" class="form-control"
                                        placeholder="sk_live_xxxxx">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="apiUserField">
                                    <label class="form-label">API User ID</label>
                                    <input type="text" name="api_user" class="form-control"
                                        placeholder="API User / Username">
                                </div>
                                <div class="col-md-6 mb-3" id="apiPasswordField">
                                    <label class="form-label">API Password</label>
                                    <input type="password" name="api_password" class="form-control"
                                        placeholder="API Password">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="subscriptionKeyField">
                                    <label class="form-label">Subscription Key / Ocp-Apim Key</label>
                                    <input type="text" name="subscription_key" class="form-control"
                                        placeholder="For MTN MoMo and similar">
                                </div>
                                <div class="col-md-6 mb-3" id="encryptionKeyField">
                                    <label class="form-label">Encryption Key</label>
                                    <input type="password" name="encryption_key" class="form-control"
                                        placeholder="For Flutterwave and similar">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="clientIdField">
                                    <label class="form-label">Client ID / Consumer Key</label>
                                    <input type="text" name="client_id" class="form-control"
                                        placeholder="OAuth Client ID">
                                </div>
                                <div class="col-md-6 mb-3" id="clientSecretField">
                                    <label class="form-label">Client Secret / Consumer Secret</label>
                                    <input type="password" name="client_secret" class="form-control"
                                        placeholder="OAuth Client Secret">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="webhookSecretField">
                                    <label class="form-label">Webhook Secret</label>
                                    <input type="password" name="webhook_secret" class="form-control"
                                        placeholder="For verifying webhooks">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Merchant Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-3-circle me-2"></i>Merchant Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Merchant ID</label>
                                    <input type="text" name="merchant_id" class="form-control"
                                        placeholder="Your merchant ID">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Merchant Name</label>
                                    <input type="text" name="merchant_name" class="form-control"
                                        placeholder="Display name for payments">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3" id="shortCodeField">
                                    <label class="form-label">Short Code / Paybill</label>
                                    <input type="text" name="short_code" class="form-control"
                                        placeholder="e.g., 174379">
                                </div>
                                <div class="col-md-4 mb-3" id="tillNumberField">
                                    <label class="form-label">Till Number</label>
                                    <input type="text" name="till_number" class="form-control"
                                        placeholder="Buy Goods Till Number">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number" class="form-control"
                                        placeholder="Default account reference">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Callback URLs -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-4-circle me-2"></i>Callback URLs</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Callback URL (Webhook)</label>
                                    <input type="url" name="callback_url" class="form-control"
                                        value="{{ url('/api/payments/callback') }}"
                                        placeholder="https://yourschool.com/api/payments/callback">
                                    <div class="form-text">URL where payment notifications will be sent</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Return URL (Success)</label>
                                    <input type="url" name="return_url" class="form-control"
                                        value="{{ url('/payments/success') }}"
                                        placeholder="https://yourschool.com/payments/success">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cancel URL</label>
                                    <input type="url" name="cancel_url" class="form-control"
                                        value="{{ url('/payments/cancel') }}"
                                        placeholder="https://yourschool.com/payments/cancel">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-5-circle me-2"></i>Additional Settings</h5>
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
                                        @endphp
                                        @foreach ($networks as $network)
                                            <div class="col-6 col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="supported_networks[]"
                                                        value="{{ $network }}" class="form-check-input"
                                                        id="network_{{ $network }}">
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
                                    <textarea name="description" class="form-control" rows="2"
                                        placeholder="Optional description for this gateway..."></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                            id="isActive">
                                        <label class="form-check-label" for="isActive">Activate Gateway</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" name="is_default" value="1" class="form-check-input"
                                            id="isDefault">
                                        <label class="form-check-label" for="isDefault">Set as Default Gateway</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('tenant.settings.mobile-money.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Gateway
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const providerSelect = document.getElementById('providerSelect');

            providerSelect.addEventListener('change', function() {
                // You can add dynamic field showing/hiding based on provider
                // For now, all fields are shown
            });
        });
    </script>
@endpush
