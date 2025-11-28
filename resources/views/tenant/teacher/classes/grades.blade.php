@extends('layouts.dashboard-teacher')

@section('title', __('Class Grades') . ' - ' . $class->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ __('Grades for') }} {{ $class->name }}</h1>
            <p class="text-muted mb-0">
                {{ __('Total grades recorded:') }} {{ $summaryCount }}
                <span class="mx-2">&middot;</span>
                {{ __('Average score:') }} {{ number_format($summaryAvg, 1) }}%
            </p>
        </div>
        <a href="{{ route('tenant.teacher.classes.show', $class) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Class') }}
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <form method="GET" action="{{ route('tenant.teacher.classes.grades', $class) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="subject_id" class="form-label">{{ __('Subject') }}</label>
                    <select name="subject_id" id="subject_id" class="form-select">
                        <option value="">{{ __('All Subjects') }}</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="assessment_type" class="form-label">{{ __('Assessment Type') }}</label>
                    <input type="text" class="form-control" id="assessment_type" name="assessment_type" value="{{ request('assessment_type') }}" placeholder="{{ __('e.g. Mid Term') }}">
                </div>
                <div class="col-md-2">
                    <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-clipboard-data me-2"></i>{{ __('Grades List') }}
            </h5>
            <span class="badge bg-primary">{{ $grades->total() }} {{ __('entries') }}</span>
        </div>
        <div class="card-body p-0">
            @if($grades->count() === 0)
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-info-circle me-2"></i>{{ __('No grades recorded for the selected filters.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Student') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Assessment') }}</th>
                                <th>{{ __('Score') }}</th>
                                <th>{{ __('Grade') }}</th>
                                <th>{{ __('Recorded On') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $grade)
                                <tr>
                                    <td>{{ $grade->student?->name ?? __('Unknown Student') }}</td>
                                    <td>{{ $grade->subject?->name ?? __('Unknown Subject') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $grade->assessment_type ?? __('Assessment') }}</div>
                                        <div class="text-muted small">{{ $grade->semester?->name ?? __('Term not set') }}</div>
                                    </td>
                                    <td>{{ $grade->score ?? __('N/A') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $grade->grade_letter === 'A' ? 'success' : ($grade->grade_letter === 'F' ? 'danger' : 'warning') }}">
                                            {{ $grade->grade_letter ?? __('N/A') }}
                                        </span>
                                    </td>
                                    <td>{{ optional($grade->assessment_date)->format('M d, Y') ?? __('N/A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top bg-light">
                    {{ $grades->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
