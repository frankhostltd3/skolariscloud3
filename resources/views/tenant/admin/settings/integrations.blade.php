@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', __('Integration Settings'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h4 mb-1">{{ __('Video Platform Integrations') }}</h1>
                        <p class="text-muted mb-0">
                            {{ __('Centralised management for Zoom, Google Meet, and Microsoft Teams credentials.') }}</p>
                    </div>
                    <a href="{{ route('tenant.settings.admin.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Settings Overview') }}
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
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (!$tableExists)
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        {{ __('The platform_integrations table is missing for this tenant. Run tenant migrations and the platform integration seeder before configuring integrations.') }}
                    </div>
                @else
                    <form action="{{ route('tenant.settings.admin.integrations.update') }}" method="POST" class="mb-4">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            @foreach ($platforms as $key => $platform)
                                @php
                                    /** @var \App\Models\PlatformIntegration|null $integration */
                                    $integration = $integrations->get($key);
                                    $status = $statuses[$key];
                                    $oldScope = 'platforms.' . $key;
                                    $isEnabled = old($oldScope . '.is_enabled', $integration?->is_enabled);
                                @endphp
                                <div class="col-md-4">
                                    <div class="card h-100 shadow-sm border-0">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="{{ $platform['icon'] }} text-primary fs-5"></span>
                                                <span class="fw-semibold">{{ $platform['label'] }}</span>
                                            </div>
                                            <span class="badge {{ $status['badge_class'] }}">{{ $status['label'] }}</span>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-3">{{ $platform['description'] }}</p>

                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="{{ $key }}-enabled"
                                                        name="platforms[{{ $key }}][is_enabled]" value="1"
                                                        {{ $isEnabled ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="{{ $key }}-enabled">{{ __('Enable integration') }}</label>
                                                </div>
                                                <p class="text-muted small mb-0">
                                                    {{ __('When enabled, virtual classes will use centrally managed credentials by default.') }}
                                                </p>
                                            </div>

                                            @if ($key === 'zoom')
                                                <input type="hidden" name="platforms[zoom][has_existing_api_key]"
                                                    value="{{ $integration && $integration->api_key ? 1 : 0 }}">
                                                <input type="hidden" name="platforms[zoom][has_existing_api_secret]"
                                                    value="{{ $integration && $integration->api_secret ? 1 : 0 }}">

                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="zoom-api-key">{{ __('API Key / Account ID') }}</label>
                                                    <input type="text"
                                                        class="form-control @error('platforms.zoom.api_key') is-invalid @enderror"
                                                        id="zoom-api-key" name="platforms[zoom][api_key]"
                                                        value="{{ old('platforms.zoom.api_key') }}"
                                                        placeholder="{{ __('Enter new API key') }}">
                                                    @error('platforms.zoom.api_key')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small
                                                        class="text-muted">{{ $integration && $integration->api_key ? __('Stored securely. Leave blank to keep the current key.') : __('Required when enabling.') }}</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="zoom-api-secret">{{ __('API Secret / Client Secret') }}</label>
                                                    <input type="password"
                                                        class="form-control @error('platforms.zoom.api_secret') is-invalid @enderror"
                                                        id="zoom-api-secret" name="platforms[zoom][api_secret]"
                                                        value="{{ old('platforms.zoom.api_secret') }}"
                                                        placeholder="{{ __('Enter new API secret') }}">
                                                    @error('platforms.zoom.api_secret')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small
                                                        class="text-muted">{{ $integration && $integration->api_secret ? __('Stored securely. Leave blank to keep the current secret.') : __('Required when enabling.') }}</small>
                                                </div>
                                            @elseif ($key === 'google_meet' || $key === 'microsoft_teams')
                                                <input type="hidden"
                                                    name="platforms[{{ $key }}][has_existing_client_id]"
                                                    value="{{ $integration && $integration->client_id ? 1 : 0 }}">
                                                <input type="hidden"
                                                    name="platforms[{{ $key }}][has_existing_client_secret]"
                                                    value="{{ $integration && $integration->client_secret ? 1 : 0 }}">

                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="{{ $key }}-client-id">{{ __('Client ID') }}</label>
                                                    <input type="text"
                                                        class="form-control @error('platforms.' . $key . '.client_id') is-invalid @enderror"
                                                        id="{{ $key }}-client-id"
                                                        name="platforms[{{ $key }}][client_id]"
                                                        value="{{ old('platforms.' . $key . '.client_id') }}"
                                                        placeholder="{{ __('Enter new client ID') }}">
                                                    @error('platforms.' . $key . '.client_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small
                                                        class="text-muted">{{ $integration && $integration->client_id ? __('Stored securely. Leave blank to retain current ID.') : __('Required when enabling.') }}</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="{{ $key }}-client-secret">{{ __('Client Secret') }}</label>
                                                    <input type="password"
                                                        class="form-control @error('platforms.' . $key . '.client_secret') is-invalid @enderror"
                                                        id="{{ $key }}-client-secret"
                                                        name="platforms[{{ $key }}][client_secret]"
                                                        value="{{ old('platforms.' . $key . '.client_secret') }}"
                                                        placeholder="{{ __('Enter new client secret') }}">
                                                    @error('platforms.' . $key . '.client_secret')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small
                                                        class="text-muted">{{ $integration && $integration->client_secret ? __('Stored securely. Leave blank to retain current secret.') : __('Required when enabling.') }}</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="{{ $key }}-redirect">{{ __('Redirect URI') }}</label>
                                                    <input type="url"
                                                        class="form-control @error('platforms.' . $key . '.redirect_uri') is-invalid @enderror"
                                                        id="{{ $key }}-redirect"
                                                        name="platforms[{{ $key }}][redirect_uri]"
                                                        value="{{ old('platforms.' . $key . '.redirect_uri', $integration?->redirect_uri) }}"
                                                        placeholder="https://example.com/oauth/callback">
                                                    @error('platforms.' . $key . '.redirect_uri')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <div class="bg-light rounded p-3">
                                                <div class="d-flex align-items-start gap-2">
                                                    <span class="bi bi-shield-lock text-primary"></span>
                                                    <div>
                                                        <p class="fw-semibold mb-1">{{ __('Configuration Summary') }}</p>
                                                        <p class="text-muted small mb-1">{{ $status['message'] }}</p>
                                                        <a href="{{ $platform['docs_url'] }}" target="_blank"
                                                            class="small">{{ __('View platform documentation') }} <span
                                                                class="bi bi-box-arrow-up-right"></span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>{{ __('Save Integration Settings') }}
                            </button>
                        </div>
                    </form>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i
                                    class="bi bi-info-circle text-primary me-2"></i>{{ __('Deployment Checklist') }}</h5>
                            <ul class="mb-0 text-muted">
                                <li>{{ __('Run tenant migrations to create the platform_integrations table if it does not exist.') }}
                                </li>
                                <li>{{ __('Execute :command after new schools are created to seed default rows.', ['command' => 'php artisan tenants:seed-integrations']) }}
                                </li>
                                <li>{{ __('Share the integration status with teaching staff; teacher-level overrides are automatically disabled when admin-managed credentials are active.') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
