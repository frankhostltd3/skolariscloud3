@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ $educationLevel->name }}</h1>
            <div class="text-muted">
                @if ($educationLevel->code)
                    <span class="badge bg-secondary">{{ $educationLevel->code }}</span>
                @endif
                <span class="badge {{ $educationLevel->is_active ? 'bg-success' : 'bg-warning' }}">
                    {{ $educationLevel->is_active ? __('Active') : __('Inactive') }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('tenant.academics.education-levels.edit', $educationLevel) }}">
                <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.education-levels.index') }}">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    @includeWhen(session('success'), 'partials.toast')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Level Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Name') }}</label>
                            <p class="mb-0 fw-semibold">{{ $educationLevel->name }}</p>
                        </div>
                        @if ($educationLevel->code)
                            <div class="col-md-6">
                                <label class="text-muted small">{{ __('Code') }}</label>
                                <p class="mb-0"><code>{{ $educationLevel->code }}</code></p>
                            </div>
                        @endif
                        @if ($educationLevel->min_grade || $educationLevel->max_grade)
                            <div class="col-md-6">
                                <label class="text-muted small">{{ __('Grade Range') }}</label>
                                <p class="mb-0">{{ $educationLevel->min_grade ?? '?' }} -
                                    {{ $educationLevel->max_grade ?? '?' }}</p>
                            </div>
                        @endif
                        @if ($educationLevel->description)
                            <div class="col-12">
                                <label class="text-muted small">{{ __('Description') }}</label>
                                <p class="mb-0">{{ $educationLevel->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center p-3 border rounded">
                        <div class="h2 mb-0 fw-bold">{{ $educationLevel->classes->count() }}</div>
                        <small class="text-muted">{{ __('Classes') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
