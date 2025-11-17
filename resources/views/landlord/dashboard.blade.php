@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-success-subtle text-success-emphasis px-3 py-2 mb-3">{{ __('Landlord overview') }}</span>
            <h1 class="display-6 fw-semibold mb-2">{{ __('Keep every Skolaris tenant healthy and growing') }}</h1>
            <p class="text-secondary mb-0">{{ __('Review tenant activity, plan adoption, and domain readiness at a glance, then drill into the operators that need your support.') }}</p>
        </div>
        <div class="text-lg-end">
            <p class="text-secondary small mb-1">{{ __('Current landlord') }}</p>
            <p class="fw-semibold mb-0">{{ auth('landlord')->user()?->email }}</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-2">{{ __('Active tenants') }}</p>
                    <p class="display-4 fw-semibold mb-0">{{ number_format($metrics['totalTenants']) }}</p>
                    <p class="text-secondary small mb-0">{{ __('Across all markets') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-2">{{ __('New this month') }}</p>
                    <p class="display-4 fw-semibold mb-0">{{ number_format($metrics['newTenantsThisMonth']) }}</p>
                    <p class="text-secondary small mb-0">{{ __('Onboarded since :date', ['date' => now()->startOfMonth()->format('M j')]) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-2">{{ __('Connected domains') }}</p>
                    <p class="display-4 fw-semibold mb-0">{{ number_format($metrics['activeDomains']) }}</p>
                    <p class="text-secondary small mb-0">{{ __('Pointed and SSL-ready') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ __('Plan adoption') }}</h2>
                        <span class="badge text-bg-primary-subtle text-primary-emphasis">{{ __('Overview') }}</span>
                    </div>
                    @forelse ($planBreakdown as $plan)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">{{ $plan['label'] }}</span>
                                <span class="text-secondary small">{{ trans_choice(':count tenant|:count tenants', $plan['count'], ['count' => $plan['count']]) }}</span>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $plan['count'] }}" aria-valuemin="0" aria-valuemax="{{ max(1, $metrics['totalTenants']) }}">
                                <div class="progress-bar" style="width: {{ $metrics['totalTenants'] ? round(($plan['count'] / max(1, $metrics['totalTenants'])) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">{{ __('No tenants have been provisioned yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ __('Recent tenant activity') }}</h2>
                        <span class="badge text-bg-success-subtle text-success-emphasis">{{ __('Last 8 tenants') }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-secondary text-uppercase small">
                                    <th scope="col">{{ __('Tenant') }}</th>
                                    <th scope="col">{{ __('Plan') }}</th>
                                    <th scope="col">{{ __('Primary domain') }}</th>
                                    <th scope="col">{{ __('Country') }}</th>
                                    <th scope="col" class="text-end">{{ __('Joined') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentTenants as $tenant)
                                    @php
                                        $plan = $tenant->getAttribute('plan_value') ?? 'unassigned';
                                        $planLabel = $plan === 'unassigned' ? __('Unassigned') : \Illuminate\Support\Str::of($plan)->headline();
                                        $domain = $tenant->getAttribute('primary_domain');
                                        $country = $tenant->getAttribute('country_code') ?? '—';
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $tenant->getAttribute('display_name') }}</td>
                                        <td>
                                            <span class="badge text-bg-light text-body border">{{ $planLabel }}</span>
                                        </td>
                                        <td class="text-secondary small">{{ $domain ?? __('Not assigned') }}</td>
                                        <td class="text-secondary small">{{ strtoupper($country) }}</td>
                                        <td class="text-end text-secondary small">{{ optional($tenant->created_at)->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary py-5">
                                            {{ __('No tenants have been created yet. Start by provisioning your first campus.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
