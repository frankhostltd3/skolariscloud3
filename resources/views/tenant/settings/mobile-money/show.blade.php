@extends('tenant.layouts.app')

@section('title', $gateway->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ $gateway->name }}</h1>
                <p class="text-muted mb-0">{{ $gateway->provider_name }} Gateway Details</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.settings.mobile-money.edit', $gateway) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('tenant.settings.mobile-money.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Gateway Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Gateway Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Provider</strong>
                                <p class="mb-0">{{ $gateway->provider_name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Slug</strong>
                                <p class="mb-0"><code>{{ $gateway->slug }}</code></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Country</strong>
                                <p class="mb-0">{{ $gateway->country_code ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Currency</strong>
                                <p class="mb-0">{{ $gateway->currency_code ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Environment</strong>
                                <p class="mb-0">
                                    <span
                                        class="badge {{ $gateway->environment == 'production' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($gateway->environment) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Status</strong>
                                <p class="mb-0">
                                    <span class="badge {{ $gateway->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if ($gateway->is_default)
                                        <span class="badge bg-primary">Default</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($gateway->description)
                            <div class="mt-3">
                                <strong class="text-muted">Description</strong>
                                <p class="mb-0">{{ $gateway->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Configuration Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Configuration Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $configFields = [
                                    'api_base_url' => 'API Base URL',
                                    'public_key' => 'Public Key',
                                    'secret_key' => 'Secret Key',
                                    'api_user' => 'API User',
                                    'api_password' => 'API Password',
                                    'subscription_key' => 'Subscription Key',
                                    'client_id' => 'Client ID',
                                    'client_secret' => 'Client Secret',
                                    'encryption_key' => 'Encryption Key',
                                    'webhook_secret' => 'Webhook Secret',
                                ];
                            @endphp
                            @foreach ($configFields as $field => $label)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        @if ($gateway->$field)
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="bi bi-circle text-muted me-2"></i>
                                        @endif
                                        <span>{{ $label }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- URLs -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Callback URLs</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-muted">Callback URL</strong>
                            <p class="mb-0 text-break">
                                @if ($gateway->callback_url)
                                    <code>{{ $gateway->callback_url }}</code>
                                @else
                                    <span class="text-muted">Not configured</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-muted">Return URL</strong>
                            <p class="mb-0 text-break">
                                @if ($gateway->return_url)
                                    <code>{{ $gateway->return_url }}</code>
                                @else
                                    <span class="text-muted">Not configured</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-0">
                            <strong class="text-muted">Cancel URL</strong>
                            <p class="mb-0 text-break">
                                @if ($gateway->cancel_url)
                                    <code>{{ $gateway->cancel_url }}</code>
                                @else
                                    <span class="text-muted">Not configured</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('tenant.settings.mobile-money.test', $gateway) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-wifi me-1"></i> Test Connection
                                </button>
                            </form>

                            <form action="{{ route('tenant.settings.mobile-money.toggle-active', $gateway) }}"
                                method="POST">
                                @csrf
                                <button type="submit"
                                    class="btn btn-outline-{{ $gateway->is_active ? 'warning' : 'success' }} w-100">
                                    <i class="bi bi-{{ $gateway->is_active ? 'pause' : 'play' }} me-1"></i>
                                    {{ $gateway->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            @if (!$gateway->is_default)
                                <form action="{{ route('tenant.settings.mobile-money.set-default', $gateway) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info w-100">
                                        <i class="bi bi-star me-1"></i> Set as Default
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Test Results -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Last Test Result</h5>
                    </div>
                    <div class="card-body">
                        @if ($gateway->last_tested_at)
                            <div class="alert {{ $gateway->test_successful ? 'alert-success' : 'alert-danger' }} mb-0">
                                <div class="d-flex align-items-center mb-2">
                                    <i
                                        class="bi bi-{{ $gateway->test_successful ? 'check-circle' : 'x-circle' }} me-2"></i>
                                    <strong>{{ $gateway->test_successful ? 'Passed' : 'Failed' }}</strong>
                                </div>
                                <small>{{ $gateway->last_tested_at->diffForHumans() }}</small>
                                @if ($gateway->test_message)
                                    <hr class="my-2">
                                    <small>{{ $gateway->test_message }}</small>
                                @endif
                            </div>
                        @else
                            <p class="text-muted mb-0">No tests run yet. Click "Test Connection" to verify your
                                configuration.</p>
                        @endif
                    </div>
                </div>

                <!-- Supported Networks -->
                @if ($gateway->supported_networks)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-broadcast me-2"></i>Supported Networks</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($gateway->supported_networks as $network)
                                <span class="badge bg-secondary me-1 mb-1">{{ $network }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Merchant Info -->
                @if ($gateway->merchant_id || $gateway->short_code || $gateway->till_number)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Merchant Details</h5>
                        </div>
                        <div class="card-body">
                            @if ($gateway->merchant_id)
                                <div class="mb-2">
                                    <small class="text-muted">Merchant ID</small>
                                    <p class="mb-0">{{ $gateway->merchant_id }}</p>
                                </div>
                            @endif
                            @if ($gateway->merchant_name)
                                <div class="mb-2">
                                    <small class="text-muted">Merchant Name</small>
                                    <p class="mb-0">{{ $gateway->merchant_name }}</p>
                                </div>
                            @endif
                            @if ($gateway->short_code)
                                <div class="mb-2">
                                    <small class="text-muted">Short Code</small>
                                    <p class="mb-0">{{ $gateway->short_code }}</p>
                                </div>
                            @endif
                            @if ($gateway->till_number)
                                <div class="mb-0">
                                    <small class="text-muted">Till Number</small>
                                    <p class="mb-0">{{ $gateway->till_number }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>History</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Created</small>
                            <p class="mb-0">{{ $gateway->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">Last Updated</small>
                            <p class="mb-0">{{ $gateway->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
