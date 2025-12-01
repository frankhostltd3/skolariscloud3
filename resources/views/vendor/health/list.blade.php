@extends('landlord.layouts.app')

@php
    $recommendedServices = [
        [
            'title' => 'Oh Dear',
            'description' => 'Native integration with Spatie Health for uptime, SSL, domain, and cron monitoring.',
            'link' => 'https://ohdear.app',
        ],
        [
            'title' => 'Sentry / Bugsnag',
            'description' => 'Real-time exception tracking across Laravel backends and JavaScript frontends.',
            'link' => 'https://sentry.io',
        ],
        [
            'title' => 'Datadog / New Relic',
            'description' => 'Deep APM, infrastructure metrics, log aggregation, and alerting.',
            'link' => 'https://www.datadoghq.com',
        ],
        [
            'title' => 'Cloudflare DNS & Status',
            'description' => 'DNS automation, edge security, performance analytics, and incident comms.',
            'link' => 'https://www.cloudflare.com',
        ],
        [
            'title' => 'Mailgun / Postmark',
            'description' => 'Transactional email observability with webhook-driven failure alerts.',
            'link' => 'https://www.mailgun.com',
        ],
        [
            'title' => 'Stripe Radar / Paystack Insights',
            'description' => 'Fraud prevention and payment health monitoring for global gateways.',
            'link' => 'https://stripe.com/radar',
        ],
    ];
@endphp

@section('content')
    <style>
        {{ $assets }}
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">
                                <i class="bi bi-heart-pulse text-danger me-2"></i>
                                {{ __('System Health Monitoring') }}
                            </h4>
                            @if ($lastRanAt)
                                <p class="mb-0 {{ $lastRanAt->diffInMinutes() > 5 ? 'text-danger' : 'text-muted' }} small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ __('Last checked') }}: {{ $lastRanAt->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <button id="health-refresh-button" type="button" class="btn btn-sm btn-primary">
                                <span class="spinner-border spinner-border-sm me-1 d-none"
                                    id="health-refresh-spinner"></span>
                                <i class="bi bi-arrow-clockwise me-1" id="health-refresh-icon"></i>
                                {{ __('Refresh Checks') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (count($checkResults?->storedCheckResults ?? []))
                        <div class="row g-3">
                            @foreach ($checkResults->storedCheckResults as $result)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <x-health-status-indicator :result="$result" />
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="card-title mb-2">{{ $result->label }}</h5>
                                                    <p class="card-text text-muted small mb-0">
                                                        @if (!empty($result->notificationMessage))
                                                            {{ $result->notificationMessage }}
                                                        @else
                                                            {{ $result->shortSummary }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('No health checks have been performed yet. Please refresh the page.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>
                        {{ __('Recommended External Services') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($recommendedServices as $service)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 border-0 bg-body-secondary">
                                    <div class="card-body">
                                        <h6 class="fw-bold">{{ $service['title'] }}</h6>
                                        <p class="text-muted small mb-3">{{ $service['description'] }}</p>
                                        <a href="{{ $service['link'] }}" target="_blank" rel="noopener"
                                            class="btn btn-outline-primary btn-sm">
                                            {{ __('Visit site') }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const refreshButton = document.getElementById('health-refresh-button');
            const refreshSpinner = document.getElementById('health-refresh-spinner');
            const refreshIcon = document.getElementById('health-refresh-icon');

            if (refreshButton) {
                refreshButton.addEventListener('click', async () => {
                    refreshButton.disabled = true;
                    refreshSpinner.classList.remove('d-none');
                    refreshIcon.classList.add('d-none');

                    try {
                        await fetch("{{ route('landlord.health.refresh') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },
                            body: JSON.stringify({
                                refresh: true
                            }),
                        });

                        window.location.reload();
                    } catch (error) {
                        console.error(error);
                        alert('{{ __('Unable to refresh health checks. Please try again.') }}');
                        refreshButton.disabled = false;
                        refreshSpinner.classList.add('d-none');
                        refreshIcon.classList.remove('d-none');
                    }
                });
            }
        </script>
    @endpush
@endsection
