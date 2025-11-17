@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Terms / Semesters') }}</h1>
            <p class="text-muted mb-0">{{ __('Manage academic terms and semesters') }}</p>
        </div>
        <a class="btn btn-primary" href="{{ route('tenant.academics.terms.create') }}">
            <i class="bi bi-plus-circle me-1"></i>{{ __('Create Term') }}
        </a>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.academics.terms.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small">{{ __('Search') }}</label>
                    <input type="text" name="q" class="form-control"
                        placeholder="{{ __('Term name, code, or year...') }}" value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">{{ __('Academic Year') }}</label>
                    <select name="academic_year" class="form-select">
                        <option value="">{{ __('All Years') }}</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="current" {{ request('status') == 'current' ? 'selected' : '' }}>{{ __('Current') }}
                        </option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}
                        </option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>
                            {{ __('Upcoming') }}</option>
                        <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>{{ __('Past') }}
                        </option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            {{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-funnel me-1"></i>{{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Terms Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Term Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Academic Year') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th class="text-center">{{ __('Progress') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terms as $term)
                            <tr class="{{ $term->is_current ? 'table-primary' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($term->is_current)
                                            <i class="bi bi-star-fill text-warning me-2" data-bs-toggle="tooltip"
                                                title="{{ __('Current Term') }}"></i>
                                        @endif
                                        <div>
                                            <a href="{{ route('tenant.academics.terms.show', $term) }}"
                                                class="text-decoration-none fw-semibold">
                                                {{ $term->name }}
                                            </a>
                                            @if ($term->description)
                                                <br><small
                                                    class="text-muted">{{ Str::limit($term->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($term->code)
                                        <span class="badge bg-secondary">{{ $term->code }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $term->academic_year }}</td>
                                <td>
                                    <div class="small">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $term->start_date->format('M d, Y') }}
                                        <br>
                                        <i class="bi bi-calendar-check me-1"></i>{{ $term->end_date->format('M d, Y') }}
                                        <br>
                                        <span class="text-muted">({{ $term->duration_in_weeks }} weeks)</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($term->status == 'ongoing')
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $term->progress_percentage }}%">
                                                {{ $term->progress_percentage }}%
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">{{ $term->progress_percentage }}%</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $term->status_badge_class }}">
                                        {{ $term->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if (!$term->is_current)
                                            <form action="{{ route('tenant.academics.terms.set-current', $term) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-warning"
                                                    data-bs-toggle="tooltip" title="{{ __('Set as Current') }}">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a class="btn btn-outline-secondary"
                                            href="{{ route('tenant.academics.terms.show', $term) }}"
                                            data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a class="btn btn-outline-primary"
                                            href="{{ route('tenant.academics.terms.edit', $term) }}"
                                            data-bs-toggle="tooltip" title="{{ __('Edit Term') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if (!$term->is_current)
                                            <form action="{{ route('tenant.academics.terms.destroy', $term) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this term?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete Term') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-calendar-event text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-2">{{ __('No terms found.') }}</p>
                                    <a href="{{ route('tenant.academics.terms.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('Create Your First Term') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($terms->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        {{ __('Showing') }} {{ $terms->firstItem() }} {{ __('to') }} {{ $terms->lastItem() }}
                        {{ __('of') }} {{ $terms->total() }} {{ __('terms') }}
                    </div>
                    <div>
                        {{ $terms->links() }}
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
