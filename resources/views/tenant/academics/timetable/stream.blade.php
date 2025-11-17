@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-1">{{ __('Stream Timetable') }} - {{ $class->name }} / {{ $stream->name }}</h1>
                <p class="text-muted mb-0">{{ __('Weekly timetable overview for this stream') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.academics.timetable.class', $class) }}" class="btn btn-outline-secondary">&larr;
                    {{ __('Back to Class') }}</a>
                <a href="{{ route('tenant.academics.timetable.index') }}"
                    class="btn btn-outline-secondary">{{ __('All Entries') }}</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Weekly Schedule') }}</h5>
                <small class="text-muted">{{ __('Entries grouped by day') }}</small>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php $dayNames = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday']; @endphp
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
                    <p class="text-muted text-center my-4">{{ __('No timetable entries found for this stream.') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
