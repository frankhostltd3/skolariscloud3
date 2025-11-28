@extends('layouts.dashboard-teacher')

@section('title', 'Platform Integrations')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h1 class="h3 mb-2 mb-md-1">
                    <i class="bi bi-plug me-2 text-primary"></i>Platform Integrations
                </h1>
                <p class="text-muted mb-0">Connect Zoom, Google Meet, and Microsoft Teams for seamless virtual class
                    management.</p>
            </div>
            <a href="{{ route('tenant.teacher.classroom.virtual.index') }}" class="btn btn-outline-secondary mt-3 mt-md-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Virtual Classes
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

        @if ($tableMissing)
            <div class="alert alert-warning" role="alert">
                <div class="d-flex">
                    <i class="bi bi-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Platform integrations are not available yet.</strong>
                        <p class="mb-2 small">Ask your administrator to run the latest tenant migrations and seed the
                            platform integrations catalog:</p>
                        <code class="d-block mb-1">php artisan tenants:migrate</code>
                        <code class="d-block">php artisan tenants:seed-integrations</code>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($platformMeta as $key => $meta)
                    @php
                        $integration = $platforms[$key];
                        $status = $statuses[$key];
                        $managed = $integration?->managedByAdmin();
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div
                            class="card h-100 border-{{ $integration && $integration->is_enabled ? 'success' : 'secondary' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi {{ $meta['icon'] }} me-2"></i>{{ $meta['label'] }}
                                </h5>
                                <span class="badge {{ $status['badge'] }}">{{ $status['label'] }}</span>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">
                                <p class="text-muted small mb-0">{{ $meta['description'] }}</p>

                                <div class="alert alert-light border small mb-0">
                                    {{ $status['message'] }}
                                </div>

                                @if ($managed)
                                    <div class="alert alert-primary small mb-0">
                                        <i class="bi bi-building me-2"></i>This integration is centrally managed by your
                                        administrator.
                                    </div>
                                @endif

                                <div class="d-grid gap-2">
                                    <a href="{{ route('tenant.teacher.classroom.integrations.setup', $key) }}"
                                        class="btn btn-outline-primary btn-sm{{ $managed ? ' disabled' : '' }}"
                                        @if ($managed) aria-disabled="true" tabindex="-1" title="Managed by administrator" @endif>
                                        <i class="bi bi-gear me-2"></i>Configure
                                    </a>

                                    <form action="{{ route('tenant.teacher.classroom.integrations.test', $key) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm w-100"
                                            {{ !$integration || !$integration->isConfigured() || $managed ? 'disabled' : '' }}
                                            title="{{ $managed ? 'Managed by administrator' : 'Runs a quick connectivity check' }}">
                                            <i class="bi bi-check-circle me-2"></i>Test Connection
                                        </button>
                                    </form>

                                    <form action="{{ route('tenant.teacher.classroom.integrations.disable', $key) }}"
                                        method="POST"
                                        onsubmit="return confirm('Disable {{ $meta['label'] }} integration?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                            {{ !$integration || !$integration->is_enabled || $managed ? 'disabled' : '' }}
                                            title="{{ $managed ? 'Managed by administrator' : 'Disable this integration' }}">
                                            <i class="bi bi-x-circle me-2"></i>Disable
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-question-circle me-2 text-info"></i>Setup Guide
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary">Zoom</h6>
                            <ol class="small text-muted mb-0">
                                <li>Go to <a href="https://marketplace.zoom.us/" target="_blank" rel="noopener">Zoom
                                        Marketplace</a></li>
                                <li>Create a Server-to-Server OAuth app</li>
                                <li>Copy the API Key & Secret</li>
                                <li>Paste credentials in the setup form</li>
                            </ol>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-success">Google Meet</h6>
                            <ol class="small text-muted mb-0">
                                <li>Visit <a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google
                                        Cloud Console</a></li>
                                <li>Enable the Google Meet API</li>
                                <li>Create OAuth 2.0 credentials</li>
                                <li>Add redirect URI then copy the client ID/secret</li>
                            </ol>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary">Microsoft Teams</h6>
                            <ol class="small text-muted mb-0">
                                <li>Go to <a href="https://portal.azure.com/" target="_blank" rel="noopener">Azure
                                        Portal</a></li>
                                <li>Register a new application</li>
                                <li>Configure Graph permissions</li>
                                <li>Create a client secret and copy details</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
