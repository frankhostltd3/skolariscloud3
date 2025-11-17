@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-secondary-subtle text-secondary-emphasis px-3 py-2 mb-3">{{ __('Settings') }}</span>
            <h1 class="h3 fw-semibold mb-2">{{ __('Tailor the landlord workspace') }}</h1>
            <p class="text-secondary mb-0">{{ __('Configure notifications, integrations, and operational policies for your Skolaris control plane.') }}</p>
        </div>
        <button class="btn btn-secondary btn-sm" type="button" disabled>
            <span class="bi bi-save me-2"></span>{{ __('Save changes') }}
        </button>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 fw-semibold mb-3">{{ __('Notification preferences') }}</h2>
                    <p class="text-secondary small">{{ __('Choose which alerts reach the landlord team inbox.') }}</p>
                    <div class="d-grid gap-3">
                        @foreach ($notificationOptions as $option)
                            <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" checked disabled>
                                <span class="form-check-label">{{ $option['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 fw-semibold mb-3">{{ __('Integrations') }}</h2>
                    <p class="text-secondary small">{{ __('Connect downstream systems to keep finance and success teams aligned.') }}</p>
                    <ul class="list-group list-group-flush">
                        @foreach ($integrationOptions as $integration)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge text-bg-light text-body border">{{ $integration['name'] }}</span>
                                    <span class="text-secondary small">
                                        @switch($integration['status'])
                                            @case('connected')
                                                {{ __('Connected') }}
                                                @break
                                            @case('beta')
                                                {{ __('Beta access') }}
                                                @break
                                            @default
                                                {{ __('Not connected') }}
                                        @endswitch
                                    </span>
                                </div>
                                <button class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ __('Manage') }}</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
