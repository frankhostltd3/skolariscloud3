@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Classes') }}</h1>
            <p class="text-muted mb-0">{{ __('Manage your school classes and their details') }}</p>
        </div>
        <a class="btn btn-primary" href="{{ route('tenant.academics.classes.create') }}">
            <i class="bi bi-plus-circle me-1"></i>{{ __('Create Class') }}
        </a>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">{{ __('Total Classes') }}</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_classes'] ?? 0) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="bi bi-building text-primary" style="font-size: 1.5rem;"></i>
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
                            <p class="text-muted mb-1 small">{{ __('Active Classes') }}</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['active_classes'] ?? 0) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
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
                            <p class="text-muted mb-1 small">{{ __('Total Students') }}</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_students'] ?? 0) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <i class="bi bi-people text-info" style="font-size: 1.5rem;"></i>
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
                            <p class="text-muted mb-1 small">{{ __('Total Capacity') }}</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_capacity'] ?? 0) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <i class="bi bi-clipboard-data text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Search and Filter Form --}}
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="q" class="form-label small">{{ __('Search') }}</label>
                    <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}"
                        class="form-control" placeholder="{{ __('Search by name or code...') }}" />
                </div>
                <div class="col-md-3">
                    <label for="education_level_id" class="form-label small">{{ __('Education Level') }}</label>
                    <select class="form-select" id="education_level_id" name="education_level_id">
                        <option value="">{{ __('All Levels') }}</option>
                        @foreach ($educationLevels as $level)
                            <option value="{{ $level->id }}"
                                {{ (string) ($filters['education_level_id'] ?? '') === (string) $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="is_active" class="form-label small">{{ __('Status') }}</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>
                            {{ __('Active') }}
                        </option>
                        <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>
                            {{ __('Inactive') }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                        </button>
                        @if (request()->hasAny(['q', 'education_level_id', 'is_active']))
                            <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.classes.index') }}">
                                <i class="bi bi-x-circle me-1"></i>{{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Classes Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">{{ __('ID') }}</th>
                            <th>{{ __('Class Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Education Level') }}</th>
                            <th class="text-center">{{ __('Students') }}</th>
                            <th class="text-center">{{ __('Capacity') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td>
                                    <a href="{{ route('tenant.academics.classes.show', $class) }}"
                                        class="text-decoration-none">
                                        #{{ $class->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('tenant.academics.classes.show', $class) }}"
                                        class="text-decoration-none fw-semibold">
                                        {{ $class->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($class->code)
                                        <span class="badge bg-secondary">{{ $class->code }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($class->educationLevel)
                                        <span class="badge bg-info">{{ $class->educationLevel->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $class->computed_students_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($class->capacity)
                                        <span>{{ $class->capacity }}</span>
                                        @if ($class->capacity_percentage > 0)
                                            <small class="text-muted">({{ $class->capacity_percentage }}%)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-warning' }}">
                                        {{ $class->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-outline-secondary"
                                            href="{{ route('tenant.academics.classes.show', $class) }}"
                                            data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a class="btn btn-outline-primary"
                                            href="{{ route('tenant.academics.classes.edit', $class) }}"
                                            data-bs-toggle="tooltip" title="{{ __('Edit Class') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('tenant.academics.classes.destroy', $class) }}"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this class? This action cannot be undone.') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" type="submit"
                                                data-bs-toggle="tooltip" title="{{ __('Delete Class') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-2">{{ __('No classes found.') }}</p>
                                    <a href="{{ route('tenant.academics.classes.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('Create Your First Class') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($classes->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        {{ __('Showing') }} {{ $classes->firstItem() }} {{ __('to') }} {{ $classes->lastItem() }}
                        {{ __('of') }} {{ $classes->total() }} {{ __('classes') }}
                    </div>
                    <div>
                        {{ $classes->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
