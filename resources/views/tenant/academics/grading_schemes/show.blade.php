@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold">{{ $gradingScheme->name }}</h1>
            <div class="mt-1">
                @if ($gradingScheme->is_current)
                    <span class="badge bg-success">{{ __('Current System') }}</span>
                    @endif @if ($gradingScheme->is_active)
                    <span class="badge bg-primary">{{ __('Active') }}</span>@else<span
                            class="badge bg-warning">{{ __('Inactive') }}</span>
                    @endif
            </div>
        </div>
        <div class="d-flex gap-2"><a class="btn btn-primary"
                href="{{ route('tenant.academics.grading_schemes.edit', $gradingScheme) }}"><i
                    class="bi bi-pencil me-1"></i>{{ __('Edit') }}</a>
            @if (!$gradingScheme->is_current)
                <form method="POST" action="{{ route('tenant.academics.grading_schemes.set_current', $gradingScheme) }}"
                    class="d-inline">@csrf @method('PUT')<button class="btn btn-success" type="submit"><i
                            class="bi bi-check-circle me-1"></i>{{ __('Set as Current') }}</button></form>
            @endif
            <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.grading_schemes.index') }}">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
        </div>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small mb-3">{{ __('Grading System Details') }}</h6>
                    <div class="mb-3"><label class="text-muted small">{{ __('Name') }}</label>
                        <p class="fw-semibold mb-0">{{ $gradingScheme->name }}</p>
                    </div>
                    @if ($gradingScheme->country)
                        <div class="mb-3"><label class="text-muted small">{{ __('Country') }}</label>
                            <p class="mb-0">{{ $gradingScheme->country }}</p>
                        </div>
                        @endif @if ($gradingScheme->examinationBody)
                            <div class="mb-3"><label class="text-muted small">{{ __('Examination Body') }}</label>
                                <p class="mb-0">{{ $gradingScheme->examinationBody->name }}
                                    ({{ $gradingScheme->examinationBody->code }})</p>
                            </div>
                            @endif @if ($gradingScheme->description)
                                <div class="mb-3"><label class="text-muted small">{{ __('Description') }}</label>
                                    <p class="mb-0">{{ $gradingScheme->description }}</p>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-6"><label class="text-muted small">{{ __('Total Bands') }}</label>
                                    <p class="fw-semibold mb-0">{{ $gradingScheme->bands->count() }}</p>
                                </div>
                                <div class="col-6"><label class="text-muted small">{{ __('Coverage') }}</label>
                                    <p class="fw-semibold mb-0">{{ $gradingScheme->score_coverage }}%</p>
                                </div>
                            </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Grading Bands') }} ({{ $gradingScheme->bands->count() }})</h5>
                </div>
                <div class="card-body">
                    @if ($gradingScheme->bands->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">{{ __('No grading bands defined yet.') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('Grade') }}</th>
                                        <th>{{ __('Label') }}</th>
                                        <th>{{ __('Score Range') }}</th>
                                        <th>{{ __('Grade Point') }}</th>
                                        <th>{{ __('Remarks') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gradingScheme->bands as $band)
                                        <tr>
                                            <td><span
                                                    class="badge bg-{{ $band->badge_color }} fs-6">{{ $band->grade }}</span>
                                            </td>
                                            <td>{{ $band->label ?? '—' }}</td>
                                            <td><span class="text-nowrap">{{ number_format($band->min_score, 2) }}% -
                                                    {{ number_format($band->max_score, 2) }}%</span></td>
                                            <td>{{ $band->grade_point ? number_format($band->grade_point, 2) : '—' }}</td>
                                            <td class="small">{{ $band->remarks ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Grading Visualization') }}</h6>
                </div>
                <div class="card-body">
                    @if ($gradingScheme->bands->isEmpty())
                        <p class="text-muted text-center mb-0">{{ __('Add grading bands to see the visualization') }}</p>
                    @else
                        <div class="progress" style="height: 40px;">
                            @php $totalRange = 100; @endphp
                            @foreach ($gradingScheme->bands->sortBy('min_score') as $band)
                                @php $width = (($band->max_score - $band->min_score) / $totalRange) * 100; @endphp
                                <div class="progress-bar bg-{{ $band->badge_color }}" role="progressbar"
                                    style="width: {{ $width }}%;"
                                    title="{{ $band->grade }}: {{ $band->min_score }}%-{{ $band->max_score }}%">
                                    <strong>{{ $band->grade }}</strong><br><small>{{ number_format($band->min_score, 0) }}-{{ number_format($band->max_score, 0) }}</small>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted small mt-2 text-center">
                            {{ __('Visual representation of score ranges for each grade') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
