@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold">{{ $examinationBody->name }}</h1>
            <div class="text-muted">
                @if ($examinationBody->is_international)
                    <span class="badge bg-info">{{ __('International') }}</span>
                @endif
                <span
                    class="badge {{ $examinationBody->is_active ? 'bg-success' : 'bg-warning' }}">{{ $examinationBody->is_active ? __('Active') : __('Inactive') }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('tenant.academics.examination-bodies.edit', $examinationBody) }}"><i
                    class="bi bi-pencil me-1"></i>{{ __('Edit') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.examination-bodies.index') }}"><i
                    class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><label class="text-muted small">{{ __('Name') }}</label>
                    <p class="fw-semibold">{{ $examinationBody->name }}</p>
                </div>
                @if ($examinationBody->code)
                    <div class="col-md-6"><label class="text-muted small">{{ __('Code') }}</label>
                        <p><code>{{ $examinationBody->code }}</code></p>
                    </div>
                @endif
                @if ($examinationBody->country)
                    <div class="col-md-6"><label class="text-muted small">{{ __('Country') }}</label>
                        <p>{{ $examinationBody->country->full_name }}</p>
                    </div>
                @endif
                @if ($examinationBody->website)
                    <div class="col-md-6"><label class="text-muted small">{{ __('Website') }}</label>
                        <p><a href="{{ $examinationBody->website }}" target="_blank">{{ $examinationBody->website }}</a>
                        </p>
                    </div>
                @endif
                @if ($examinationBody->description)
                    <div class="col-12"><label class="text-muted small">{{ __('Description') }}</label>
                        <p>{{ $examinationBody->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
