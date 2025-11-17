@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.academics.terms.index') }}">{{ __('Terms') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $term->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h4 fw-semibold mb-1">{{ $term->name }}
                    @if ($term->is_current)
                        <i class="bi bi-star-fill text-warning" data-bs-toggle="tooltip"
                            title="{{ __('Current Term') }}"></i>
                    @endif
                </h1>
                <p class="text-muted mb-0">{{ $term->academic_year }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('tenant.academics.terms.edit', $term) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
                @if (!$term->is_current)
                    <form action="{{ route('tenant.academics.terms.destroy', $term) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('{{ __('Are you sure?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @includeWhen(session('success'), 'partials.toast')

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">{{ __('Duration') }}</p>
                            <h3 class="mb-0 fw-bold">{{ $term->duration_in_weeks }}</h3>
                            <small class="text-muted">{{ __('weeks') }}</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="bi bi-calendar-week text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">{{ __('Progress') }}</p>
                            <h3 class="mb-0 fw-bold">{{ $term->progress_percentage }}%</h3>
                            <small class="text-muted">{{ __($term->status_label) }}</small>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="bi bi-bar-chart text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">{{ __('Classes') }}</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['classes_count'] }}</h3>
                            <small class="text-muted">{{ __('taught') }}</small>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <i class="bi bi-building text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">{{ __('Exams') }}</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['exams_count'] }}</h3>
                            <small class="text-muted">{{ __('scheduled') }}</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <i class="bi bi-clipboard-check text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Term Details --}}
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('Term Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Term Name') }}</label>
                            <p class="mb-0 fw-semibold">{{ $term->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Term Code') }}</label>
                            <p class="mb-0 fw-semibold">{{ $term->code ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Academic Year') }}</label>
                            <p class="mb-0 fw-semibold">{{ $term->academic_year }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Status') }}</label>
                            <p class="mb-0">
                                <span class="badge {{ $term->status_badge_class }}">{{ $term->status_label }}</span>
                                @if ($term->is_current)
                                    <span class="badge bg-warning"><i
                                            class="bi bi-star-fill me-1"></i>{{ __('Current') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Start Date') }}</label>
                            <p class="mb-0 fw-semibold">
                                <i class="bi bi-calendar3 me-1"></i>{{ $term->start_date->format('F d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('End Date') }}</label>
                            <p class="mb-0 fw-semibold">
                                <i class="bi bi-calendar-check me-1"></i>{{ $term->end_date->format('F d, Y') }}
                            </p>
                        </div>
                        @if ($term->description)
                            <div class="col-12">
                                <label class="text-muted small">{{ __('Description') }}</label>
                                <p class="mb-0">{{ $term->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="list-group list-group-flush">
                    @if (!$term->is_current)
                        <form action="{{ route('tenant.academics.terms.set-current', $term) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="list-group-item list-group-item-action">
                                <i class="bi bi-star-fill text-warning me-2"></i>{{ __('Set as Current Term') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('tenant.academics.terms.edit', $term) }}"
                        class="list-group-item list-group-item-action">
                        <i class="bi bi-pencil me-2"></i>{{ __('Edit Term') }}
                    </a>
                    <a href="{{ route('tenant.academics.terms.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-list me-2"></i>{{ __('View All Terms') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
