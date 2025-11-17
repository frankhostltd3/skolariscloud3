@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-2">{{ __('Pricing catalogue') }}</span>
            <h1 class="h3 fw-semibold mb-1">{{ __('Manage subscription packages') }}</h1>
            <p class="text-secondary mb-0">{{ __('Curate the plans schools see on the marketing site and tenant dashboards.') }}</p>
        </div>
        <a href="{{ route('landlord.billing.plans.create') }}" class="btn btn-primary">
            <span class="bi bi-plus-circle me-1"></span>{{ __('Create plan') }}
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="bi bi-check-circle-fill me-2"></span>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="text-secondary text-uppercase small">
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Price') }}</th>
                        <th scope="col">{{ __('Billing') }}</th>
                        <th scope="col">{{ __('Features') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col" class="text-end">{{ __('Updated') }}</th>
                        <th scope="col" class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td class="fw-semibold">
                                <div>{{ $plan->name }}</div>
                                <div class="text-secondary small">{{ $plan->tagline }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $plan->display_price }}</span>
                                <div class="text-secondary small">{{ $plan->currency }}</div>
                            </td>
                            <td>
                                <span class="badge text-bg-light text-body border">{{ $plan->billing_period_label }}</span>
                            </td>
                            <td>
                                @php
                                    $featureCount = $plan->features_list ? count($plan->features_list) : 0;
                                @endphp
                                <span class="badge text-bg-info-subtle text-info-emphasis">{{ $featureCount }} {{ \Illuminate\Support\Str::plural(__('feature'), $featureCount) }}</span>
                            </td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge text-bg-success-subtle text-success-emphasis">{{ __('Visible') }}</span>
                                @else
                                    <span class="badge text-bg-secondary-subtle text-secondary-emphasis">{{ __('Hidden') }}</span>
                                @endif
                                @if($plan->is_highlighted)
                                    <span class="badge text-bg-warning-subtle text-warning-emphasis ms-1">{{ __('Highlighted') }}</span>
                                @endif
                            </td>
                            <td class="text-secondary small text-end">{{ optional($plan->updated_at)->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="btn-group" role="group" aria-label="{{ __('Plan actions') }}">
                                    <a href="{{ route('landlord.billing.plans.edit', $plan) }}" class="btn btn-outline-primary btn-sm">
                                        <span class="bi bi-pencil-square"></span>
                                    </a>
                                    <form action="{{ route('landlord.billing.plans.destroy', $plan) }}" method="post" onsubmit="return confirm('{{ __('Are you sure you want to delete this plan?') }}');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <span class="bi bi-trash"></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">{{ __('No plans configured yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top-0">
            {{ $plans->links() }}
        </div>
    </div>
@endsection
