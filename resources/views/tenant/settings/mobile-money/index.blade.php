@extends('tenant.layouts.app')

@section('title', 'Mobile Money Gateways')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Mobile Money Gateways</h1>
                <p class="text-muted mb-0">Configure mobile money payment providers for your school</p>
            </div>
            <a href="{{ route('tenant.settings.mobile-money.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Gateway
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($gateways->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-phone text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Mobile Money Gateways Configured</h4>
                    <p class="text-muted mb-4">
                        Add a mobile money gateway to start accepting payments via MTN MoMo, M-Pesa, Airtel Money, and more.
                    </p>
                    <a href="{{ route('tenant.settings.mobile-money.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Your First Gateway
                    </a>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($gateways as $gateway)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 {{ $gateway->is_default ? 'border-primary' : '' }}">
                            @if ($gateway->is_default)
                                <div class="card-header bg-primary text-white py-2">
                                    <i class="bi bi-star-fill me-1"></i> Default Gateway
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        @if ($gateway->logo_url)
                                            <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->name }}" class="rounded"
                                                style="width: 50px; height: 50px; object-fit: contain;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="bi bi-phone text-secondary" style="font-size: 1.5rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">{{ $gateway->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $gateway->provider_name }}</p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('tenant.settings.mobile-money.edit', $gateway) }}">
                                                    <i class="bi bi-pencil me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('tenant.settings.mobile-money.test', $gateway) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-lightning me-2"></i> Test Connection
                                                    </button>
                                                </form>
                                            </li>
                                            @if (!$gateway->is_default)
                                                <li>
                                                    <form
                                                        action="{{ route('tenant.settings.mobile-money.set-default', $gateway) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-star me-2"></i> Set as Default
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form
                                                    action="{{ route('tenant.settings.mobile-money.destroy', $gateway) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this gateway?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    @if ($gateway->country_code)
                                        <span class="badge bg-light text-dark me-1">
                                            <i class="bi bi-globe me-1"></i>{{ $gateway->country_code }}
                                        </span>
                                    @endif
                                    @if ($gateway->currency_code)
                                        <span class="badge bg-light text-dark me-1">
                                            <i class="bi bi-currency-exchange me-1"></i>{{ $gateway->currency_code }}
                                        </span>
                                    @endif
                                    <span
                                        class="badge {{ $gateway->environment == 'production' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($gateway->environment) }}
                                    </span>
                                </div>

                                @if ($gateway->supported_networks)
                                    <div class="mb-3">
                                        <small class="text-muted">Supported Networks:</small><br>
                                        @foreach ($gateway->supported_networks as $network)
                                            <span class="badge bg-secondary me-1">{{ $network }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($gateway->last_tested_at)
                                    <div class="small {{ $gateway->test_successful ? 'text-success' : 'text-danger' }}">
                                        <i
                                            class="bi bi-{{ $gateway->test_successful ? 'check-circle' : 'x-circle' }} me-1"></i>
                                        Last tested: {{ $gateway->last_tested_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                                <form action="{{ route('tenant.settings.mobile-money.toggle-active', $gateway) }}"
                                    method="POST">
                                    @csrf
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="active_{{ $gateway->id }}"
                                            {{ $gateway->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                                        <label class="form-check-label" for="active_{{ $gateway->id }}">
                                            {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </form>
                                <a href="{{ route('tenant.settings.mobile-money.show', $gateway) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Provider Templates Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-grid me-2"></i>Quick Add Popular Providers</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $popularProviders = [
                            'mtn_momo' => ['icon' => '📱', 'color' => 'warning'],
                            'mpesa' => ['icon' => '💚', 'color' => 'success'],
                            'airtel_money' => ['icon' => '🔴', 'color' => 'danger'],
                            'flutterwave' => ['icon' => '🦋', 'color' => 'info'],
                            'paystack' => ['icon' => '💙', 'color' => 'primary'],
                            'stripe' => ['icon' => '💳', 'color' => 'secondary'],
                        ];
                    @endphp
                    @foreach ($popularProviders as $providerKey => $config)
                        @if (isset($providerList[$providerKey]))
                            <div class="col-6 col-md-4 col-lg-2 mb-3">
                                <a href="{{ route('tenant.settings.mobile-money.create', ['provider' => $providerKey]) }}"
                                    class="btn btn-outline-{{ $config['color'] }} w-100 py-3">
                                    <span style="font-size: 1.5rem;">{{ $config['icon'] }}</span><br>
                                    <small>{{ $providerList[$providerKey]['name'] }}</small>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
