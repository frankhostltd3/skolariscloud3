@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-2">{{ __('New plan') }}</span>
            <h1 class="h3 fw-semibold mb-1">{{ __('Create a billing package') }}</h1>
            <p class="text-secondary mb-0">{{ __('Define pricing, highlight key features, and choose whether to spotlight the plan.') }}</p>
        </div>
        <a href="{{ route('landlord.billing.plans.index') }}" class="btn btn-outline-secondary">
            <span class="bi bi-arrow-left me-1"></span>{{ __('Back to plans') }}
        </a>
    </div>

    <form action="{{ route('landlord.billing.plans.store') }}" method="post" novalidate>
        @csrf
        @include('landlord.billing.plans._form')
    </form>
@endsection
