@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold">{{ $country->full_name }}</h1>
            <div class="text-muted"><span
                    class="badge {{ $country->is_active ? 'bg-success' : 'bg-warning' }}">{{ $country->is_active ? __('Active') : __('Inactive') }}</span>
            </div>
        </div>
        <div class="d-flex gap-2"><a class="btn btn-primary" href="{{ route('tenant.academics.countries.edit', $country) }}"><i
                    class="bi bi-pencil me-1"></i>{{ __('Edit') }}</a><a class="btn btn-outline-secondary"
                href="{{ route('tenant.academics.countries.index') }}"><i
                    class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a></div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><label class="text-muted small">{{ __('Name') }}</label>
                    <p class="fw-semibold">{{ $country->name }}</p>
                </div>
                <div class="col-md-3"><label class="text-muted small">{{ __('ISO Codes') }}</label>
                    <p><code>{{ $country->iso_code_2 }}</code> / <code>{{ $country->iso_code_3 }}</code></p>
                </div>
                @if ($country->phone_code)
                    <div class="col-md-3"><label class="text-muted small">{{ __('Phone Code') }}</label>
                        <p>{{ $country->phone_code }}</p>
                    </div>
                @endif
                @if ($country->currency_code)
                    <div class="col-md-6"><label class="text-muted small">{{ __('Currency') }}</label>
                        <p>{{ $country->currency_symbol }} ({{ $country->currency_code }})</p>
                    </div>
                @endif
                @if ($country->timezone)
                    <div class="col-md-6"><label class="text-muted small">{{ __('Timezone') }}</label>
                        <p>{{ $country->timezone }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if ($country->examinationBodies->isNotEmpty())
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">{{ __('Examination Bodies') }} ({{ $country->examinationBodies->count() }})
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach ($country->examinationBodies as $body)
                        <li class="mb-2">• {{ $body->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
