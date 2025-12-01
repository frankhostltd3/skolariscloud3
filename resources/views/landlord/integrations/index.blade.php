@extends('landlord.layouts.app')

@section('content')
    @php
        $snapshotCollection = collect($snapshots ?? []);
        $timelineCollection = collect($timeline ?? []);
        $nextActionsList = collect($nextActions ?? []);
    @endphp
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm mb-4 bg-gradient"
            style="background: radial-gradient(circle at top right, rgba(112, 80, 240, 0.18), rgba(18, 21, 36, 0.92));">
            <div class="card-body p-4 p-lg-5 text-white">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50 mb-2">Landlord Integrations</div>
                        <h1 class="display-6 fw-semibold mb-3">{{ __('World-Class Integrations Hub') }}</h1>
                        <p class="lead mb-0 text-white-75">
                            {{ __('Monitor every connector, automation, and data handshake that keeps your campuses real-time and production ready.') }}
                        </p>
                    </div>
                    <div class="text-lg-end">
                        <div
                            class="badge bg-{{ $heroMetrics['uptime_badge_variant'] ?? 'success' }}-subtle text-{{ $heroMetrics['uptime_badge_variant'] ?? 'success' }}-emphasis px-3 py-2 mb-2">
                            {{ $heroMetrics['uptime_badge_text'] ?? __('Live telemetry on standby') }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white-75">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span>{{ $heroMetrics['compliance_summary'] ?? __('Compliance status unavailable') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label class="form-label text-secondary text-uppercase small mb-1">{{ __('Region') }}</label>
                <select name="region" class="form-select">
                    <option value="">{{ __('All regions') }}</option>
                    @foreach ($filters['regions'] ?? [] as $region)
                        <option value="{{ $region }}" @selected($selectedRegion === $region)>{{ strtoupper($region) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary text-uppercase small mb-1">{{ __('Integration type') }}</label>
                <select name="type" class="form-select">
                    <option value="">{{ __('All types') }}</option>
                    @foreach ($filters['types'] ?? [] as $type)
                        <option value="{{ $type }}" @selected($selectedType === $type)>
                            {{ \Illuminate\Support\Str::headline($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">{{ __('Apply Filters') }}</button>
                <a href="{{ route('landlord.integrations') }}"
                    class="btn btn-outline-secondary flex-fill">{{ __('Reset') }}</a>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-uppercase text-secondary small fw-semibold">Connected Services</span>
                            <i class="bi bi-diagram-3 text-primary"></i>
                        </div>
                        <div class="display-5 fw-semibold mb-1">{{ $heroMetrics['connected_services'] ?? 0 }}</div>
                        <p class="text-secondary mb-0">{{ __('12 critical • 18 advanced • 7 experimental') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-uppercase text-secondary small fw-semibold">Active Automations</span>
                            <i class="bi bi-cpu text-primary"></i>
                        </div>
                        <div class="display-5 fw-semibold mb-1">{{ $heroMetrics['active_automations'] ?? 0 }}</div>
                        <p class="text-secondary mb-0">{{ __('Queue latency < 350 ms across regions') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-uppercase text-secondary small fw-semibold">Data Reliability</span>
                            <i class="bi bi-activity text-primary"></i>
                        </div>
                        <div class="display-5 fw-semibold mb-1">{{ ($heroMetrics['data_reliability'] ?? '0.00') . '%' }}
                        </div>
                        <p class="text-secondary mb-0">{{ __('Streaming error budget used 8.3% this month') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h4 fw-semibold mb-1">{{ __('Live integrations') }}</h2>
                        <p class="text-secondary mb-0">
                            {{ __('Every connector monitored in real time with telemetry, rate limits, and escalation policy.') }}
                        </p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span
                            class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">{{ __('Realtime Webhooks') }}</span>
                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">{{ __('Batch Pipelines') }}</span>
                        <span
                            class="badge bg-secondary-subtle text-secondary-emphasis px-3 py-2">{{ __('Manual Overrides') }}</span>
                    </div>
                </div>
                @if ($snapshotCollection->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('No telemetry snapshots available yet. Configure integration observers or run the telemetry sync job.') }}
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($snapshotCollection as $snapshot)
                            <div class="col-md-6">
                                <div class="border rounded-4 h-100 p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="fw-semibold h5 mb-1">{{ $snapshot->display_name }}</div>
                                            <span
                                                class="text-secondary">{{ $snapshot->vendor ?? __('Vendor pending') }}</span>
                                        </div>
                                        <span
                                            class="badge bg-{{ $snapshot->status_color }}-subtle text-{{ $snapshot->status_color }}-emphasis px-3 py-2 text-capitalize">
                                            {{ $snapshot->status }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-4 mb-3">
                                        <div>
                                            <div class="text-uppercase small text-secondary">Latency</div>
                                            <div class="fw-semibold">
                                                @if ($snapshot->latency_ms)
                                                    {{ $snapshot->latency_ms }} ms avg
                                                @else
                                                    {{ __('Pending telemetry') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-uppercase small text-secondary">Last sync</div>
                                            <div class="fw-semibold">
                                                {{ optional($snapshot->last_synced_at)->diffForHumans() ?? __('Not available') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-4 mb-3">
                                        <div>
                                            <div class="text-uppercase small text-secondary">Throughput</div>
                                            <div class="fw-semibold">
                                                {{ number_format($snapshot->throughput_per_minute) }}/min</div>
                                        </div>
                                        <div>
                                            <div class="text-uppercase small text-secondary">Error rate</div>
                                            <div class="fw-semibold">{{ number_format($snapshot->error_rate, 2) }}%</div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="text-uppercase small text-secondary mb-2">Channels</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @forelse ($snapshot->channel_badges as $channel)
                                                <span class="badge bg-light text-dark border">{{ $channel }}</span>
                                            @empty
                                                <span
                                                    class="text-secondary small">{{ __('Channel metadata pending sync') }}</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h5 fw-semibold mb-1">{{ __('Automation & Event Timeline') }}</h2>
                                <p class="text-secondary mb-0">{{ __('Recent orchestration events across all regions.') }}
                                </p>
                            </div>
                            <span class="badge bg-dark text-white">UTC</span>
                        </div>
                        @if ($timelineCollection->isEmpty())
                            <div class="alert alert-secondary mb-0">
                                <i class="bi bi-slash-circle me-2"></i>
                                {{ __('No recent integration events for the selected filters.') }}
                            </div>
                        @else
                            <div class="timeline">
                                @foreach ($timelineCollection as $event)
                                    @php
                                        $severityColor = match ($event->severity) {
                                            'success' => 'success',
                                            'warning' => 'warning',
                                            'danger', 'incident' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <div class="d-flex gap-3 mb-4">
                                        <div class="text-secondary small text-uppercase fw-semibold"
                                            style="min-width: 60px;">
                                            {{ optional($event->occurred_at)->timezone('UTC')->format('H:i') }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="badge bg-{{ $severityColor }}-subtle text-{{ $severityColor }}-emphasis text-capitalize">{{ $event->severity }}</span>
                                                <strong>{{ $event->title }}</strong>
                                            </div>
                                            <div class="text-secondary">{{ $event->detail }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h5 fw-semibold mb-1">{{ __('Next best actions') }}</h2>
                                <p class="text-secondary mb-0">
                                    {{ __('Keep the integrator backlog prioritized and visible.') }}</p>
                            </div>
                            <i class="bi bi-lightning-charge text-primary fs-4"></i>
                        </div>
                        <ul class="list-unstyled mb-4">
                            @forelse ($nextActionsList as $step)
                                <li class="d-flex align-items-start gap-3 mb-3">
                                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                    <div>{{ $step }}</div>
                                </li>
                            @empty
                                <li class="text-secondary small">
                                    {{ __('No action items at the moment. Keep telemetry streaming for live recommendations.') }}
                                </li>
                            @endforelse
                        </ul>
                        <div class="p-3 rounded-4 bg-primary-subtle text-primary-emphasis">
                            <div class="fw-semibold mb-1">{{ __('Need concierge support?') }}</div>
                            <div class="small mb-3">
                                {{ __('Our integrations squad is on standby for enterprise rollouts and regional compliance reviews.') }}
                            </div>
                            <a href="mailto:integrations@skolaris.com"
                                class="btn btn-primary btn-sm">{{ __('Book a strategy session') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <div class="text-uppercase small text-secondary fw-semibold mb-2">
                            {{ __('Enterprise-grade roadmap') }}</div>
                        <h2 class="h4 fw-semibold mb-3">{{ __('Upcoming connectors & certifications') }}</h2>
                        <p class="text-secondary mb-4">
                            {{ __('We co-build with campuses worldwide. Preview the next wave of automation targets and compliance deliverables to plan your launches confidently.') }}
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-info-subtle text-info-emphasis">Beta</span>
                                        <strong>BambooHR + Payroll</strong>
                                    </div>
                                    <p class="text-secondary small mb-0">
                                        {{ __('Live pilot with 3 campuses • SOC 2 + HIPAA review underway') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Planning</span>
                                        <strong>Azure Event Grid</strong>
                                    </div>
                                    <p class="text-secondary small mb-0">
                                        {{ __('Multi-region event streaming with replay + dlq tooling') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success-subtle text-success-emphasis">Certified</span>
                                        <strong>Stripe Treasury</strong>
                                    </div>
                                    <p class="text-secondary small mb-0">
                                        {{ __('Cleared for Kenya, Uganda, South Africa rollouts') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Research</span>
                                        <strong>AI Invigilator APIs</strong>
                                    </div>
                                    <p class="text-secondary small mb-0">
                                        {{ __('Ethical review + latency modeling in progress') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="border rounded-4 p-4 h-100 bg-light">
                            <div class="text-uppercase small text-secondary fw-semibold mb-2">
                                {{ __('Operational checklist') }}</div>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start gap-3 mb-3">
                                    <i class="bi bi-shield-check text-success fs-5"></i>
                                    <div>
                                        <strong>{{ __('Security review cadence') }}</strong>
                                        <p class="text-secondary mb-0 small">
                                            {{ __('Quarterly pentest & SOC 2 bridge letter archived in Vault.') }}</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-3 mb-3">
                                    <i class="bi bi-diagram-3-fill text-primary fs-5"></i>
                                    <div>
                                        <strong>{{ __('Failover testing window') }}</strong>
                                        <p class="text-secondary mb-0 small">
                                            {{ __('Next chaos drill: Saturday 02:00 UTC (sandbox + prod shadow).') }}</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-3 mb-3">
                                    <i class="bi bi-people-fill text-info fs-5"></i>
                                    <div>
                                        <strong>{{ __('Campus onboarding program') }}</strong>
                                        <p class="text-secondary mb-0 small">
                                            {{ __('Eight campuses scheduled for white-glove enablement in Q2.') }}</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-3">
                                    <i class="bi bi-journal-text text-warning fs-5"></i>
                                    <div>
                                        <strong>{{ __('Runbook refresh') }}</strong>
                                        <p class="text-secondary mb-0 small">
                                            {{ __('Integration catalog docs updated weekly with rollback steps.') }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
