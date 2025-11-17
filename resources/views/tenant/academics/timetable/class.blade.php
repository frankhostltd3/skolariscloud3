@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-1">
                    {{ $isStudentView ?? false ? __('My Class Timetable') : __('Class Timetable') }} - {{ $class->name }}
                </h1>
                <p class="text-muted mb-0">
                    {{ $isStudentView ?? false ? __('Showing timetable for your class') : __('Weekly timetable overview for this class') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.academics.timetable.index') }}" class="btn btn-outline-secondary">&larr;
                    {{ __('Back') }}</a>
                @if (!($isStudentView ?? false))
                    <a href="{{ route('tenant.academics.timetable.generate') }}"
                        class="btn btn-outline-primary">{{ __('Generate') }}</a>
                @endif
            </div>
        </div>

        @if (isset($streams) && $streams->count())
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Streams Summary') }}</h5>
                    <small class="text-muted">{{ __('Quick overview of periods per stream') }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Stream') }}</th>
                                    <th>{{ __('Scheduled Periods') }}</th>
                                    <th>{{ __('View') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($streams as $stream)
                                    <tr>
                                        <td>{{ $stream->name }}</td>
                                        <td>{{ $streamCounts[$stream->id] ?? 0 }}</td>
                                        <td>
                                            <a href="{{ route('tenant.academics.timetable.stream', $stream) }}"
                                                class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Weekly Schedule') }}</h5>
                <small class="text-muted">{{ __('Days without entries are hidden') }}</small>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $dayNames = [
                            1 => 'Monday',
                            2 => 'Tuesday',
                            3 => 'Wednesday',
                            4 => 'Thursday',
                            5 => 'Friday',
                            6 => 'Saturday',
                            7 => 'Sunday',
                        ];
                    @endphp
                    @foreach ($schedule as $day => $entries)
                        @if ($entries->count())
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded h-100">
                                    <div
                                        class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                        <strong>{{ __($dayNames[$day]) }}</strong>
                                        <span class="badge bg-primary">{{ $entries->count() }} {{ __('Periods') }}</span>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($entries as $e)
                                            <li class="list-group-item small">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-semibold">{{ $e->starts_at }} -
                                                        {{ $e->ends_at }}</span>
                                                    <span class="text-muted">{{ $e->subject->name ?? '—' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span
                                                        class="text-muted">{{ $e->teacher->full_name ?? ($e->teacher->name ?? __('Unassigned')) }}</span>
                                                    <span class="badge bg-secondary">{{ $e->room ?? __('No Room') }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @if (collect($schedule)->flatten()->count() === 0)
                    <p class="text-muted text-center my-4">{{ __('No timetable entries found for this class.') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
