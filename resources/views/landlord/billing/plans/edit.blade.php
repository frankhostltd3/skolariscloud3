@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-warning-subtle text-warning-emphasis px-3 py-2 mb-2">{{ __('Edit plan') }}</span>
            <h1 class="h3 fw-semibold mb-1">{{ $plan->name }}</h1>
            <p class="text-secondary mb-0">{{ __('Adjust pricing, availability, or the benefits families and schools will see.') }}</p>
        </div>
        <a href="{{ route('landlord.billing.plans.index') }}" class="btn btn-outline-secondary">
            <span class="bi bi-arrow-left me-1"></span>{{ __('Back to plans') }}
        </a>
    </div>

    <form action="{{ route('landlord.billing.plans.update', $plan) }}" method="post" novalidate>
        @csrf
        @method('put')

        @include('landlord.billing.plans._form')
    </form>
@endsection
