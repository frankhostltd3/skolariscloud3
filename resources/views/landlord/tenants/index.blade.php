@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Tenant directory') }}</span>
            <h1 class="h3 fw-semibold mb-2">{{ __('Monitor every school tenant at a glance') }}</h1>
            <p class="text-secondary mb-0">{{ __('Search recent signups, review plan assignments, and open tenant records for deeper troubleshooting.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('landlord.tenants.create') }}" class="btn btn-primary btn-sm">
                <span class="bi bi-plus-lg me-2"></span>{{ __('Provision tenant') }}
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <span class="bi bi-download me-2"></span>{{ __('Export') }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('landlord.tenants.export.excel') }}">
                        <span class="bi bi-file-earmark-excel me-2"></span>{{ __('Export to Excel') }}
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('landlord.tenants.export.sql') }}">
                        <span class="bi bi-file-earmark-code me-2"></span>{{ __('Export to SQL') }}
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('landlord.tenants.export.odata') }}">
                        <span class="bi bi-cloud me-2"></span>{{ __('Export to OData') }}
                    </a></li>
                </ul>
            </div>
            <a href="{{ route('landlord.tenants.import') }}" class="btn btn-outline-primary btn-sm">
                <span class="bi bi-upload me-2"></span>{{ __('Import') }}
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('landlord.tenants.index') }}" class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label class="form-label" for="search">{{ __('Search tenants') }}</label>
                    <input type="search" id="search" name="search" class="form-control"
                           placeholder="{{ __('Name, domain, or ID') }}" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="plan">{{ __('Plan') }}</label>
                    <select id="plan" name="plan" class="form-select">
                        <option value="">{{ __('All plans') }}</option>
                        <option value="starter" {{ ($filters['plan'] ?? '') === 'starter' ? 'selected' : '' }}>{{ __('Starter') }}</option>
                        <option value="growth" {{ ($filters['plan'] ?? '') === 'growth' ? 'selected' : '' }}>{{ __('Growth') }}</option>
                        <option value="premium" {{ ($filters['plan'] ?? '') === 'premium' ? 'selected' : '' }}>{{ __('Premium') }}</option>
                        <option value="enterprise" {{ ($filters['plan'] ?? '') === 'enterprise' ? 'selected' : '' }}>{{ __('Enterprise') }}</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label" for="country">{{ __('Country') }}</label>
                    <input type="text" id="country" name="country" class="form-control"
                           placeholder="{{ __('e.g. KE') }}" value="{{ $filters['country'] ?? '' }}">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">{{ __('Search') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="text-secondary text-uppercase small">
                    <tr>
                        <th scope="col">{{ __('Tenant') }}</th>
                        <th scope="col">{{ __('Plan') }}</th>
                        <th scope="col">{{ __('Contacts') }}</th>
                        <th scope="col">{{ __('Primary domain') }}</th>
                        <th scope="col">{{ __('Country') }}</th>
                        <th scope="col" class="text-end">{{ __('Created') }}</th>
                        <th scope="col" class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tenants as $tenant)
                        @php
                            $domain = $domains->get($tenant->id)?->first();
                            $plan = $tenant->getAttribute('plan_value');
                            $planLabel = $plan ? \Illuminate\Support\Str::of($plan)->headline() : __('Starter');
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $tenant->getAttribute('display_name') }}</td>
                            <td><span class="badge text-bg-light text-body border">{{ $planLabel }}</span></td>
                            <td class="text-secondary small">
                                <div>{{ $tenant->getAttribute('contact_email') ?? '—' }}</div>
                                @php($phones = $tenant->getAttribute('phones') ?? [])
                                @if(!empty($phones))
                                    <div class="small">{{ implode(', ', array_slice($phones, 0, 2)) }}@if(count($phones) > 2) {{ __(' +:count more', ['count' => count($phones) - 2]) }} @endif</div>
                                @endif
                            </td>
                            <td class="text-secondary small">{{ $domain->domain ?? __('Not assigned') }}</td>
                            <td class="text-secondary small">{{ strtoupper($tenant->getAttribute('country_code') ?? '—') }}</td>
                            <td class="text-end text-secondary small">{{ optional($tenant->created_at)->format('M j, Y') }}</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <span class="bi bi-three-dots"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('landlord.tenants.edit', $tenant) }}">
                                            <span class="bi bi-pencil me-2"></span>{{ __('Edit') }}
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#"
                                               onclick="event.preventDefault(); if(confirm('{{ __('Are you sure you want to delete this tenant?') }}')) { document.getElementById('delete-form-{{ $tenant->id }}').submit(); }">
                                            <span class="bi bi-trash me-2"></span>{{ __('Delete') }}
                                        </a></li>
                                    </ul>
                                </div>
                                <form id="delete-form-{{ $tenant->id }}" method="POST" action="{{ route('landlord.tenants.destroy', $tenant) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">{{ __('No tenants found yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tenants instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white border-0">
                {{ $tenants->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
