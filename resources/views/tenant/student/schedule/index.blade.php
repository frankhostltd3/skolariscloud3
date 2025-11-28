@extends('layouts.tenant.student')

@section('title', 'My Schedule')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="h3 fw-semibold mb-0">
                        <i class="bi bi-calendar-week me-2"></i>{{ __('My Weekly Schedule') }}
                    </h1>
                    @if ($class)
                        <a href="{{ route('tenant.student.schedule.export') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i>{{ __('Export to Calendar') }}
                        </a>
                    @endif
                </div>

                @if ($class)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>{{ __('Class:') }}</strong> {{ $class->name }}
                        @if ($stream)
                            | <strong>{{ __('Stream:') }}</strong> {{ $stream->name }}
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('You have not been assigned to a class yet. Please contact your administrator.') }}
                    </div>
                @endif
            </div>
        </div>

        @if ($class)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="120">{{ __('Day') }}</th>
                                            <th>{{ __('Schedule') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedule as $day => $slots)
                                            <tr>
                                                <td class="fw-semibold align-top">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar3 me-2 text-primary"></i>
                                                        {{ __($day) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if (count($slots) > 0)
                                                        <div class="row g-2">
                                                            @foreach ($slots as $slot)
                                                                <div class="col-md-6">
                                                                    <div
                                                                        class="card border-start border-primary border-3 h-100">
                                                                        <div class="card-body p-3">
                                                                            <div
                                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                                <h6 class="mb-0 fw-bold">
                                                                                    <i
                                                                                        class="bi bi-book me-2 text-primary"></i>
                                                                                    {{ $slot['subject'] }}
                                                                                    @if ($slot['subject_code'])
                                                                                        <small
                                                                                            class="text-muted">({{ $slot['subject_code'] }})</small>
                                                                                    @endif
                                                                                </h6>
                                                                                <span class="badge bg-primary">
                                                                                    {{ date('g:i A', strtotime($slot['start_time'])) }}
                                                                                    -
                                                                                    {{ date('g:i A', strtotime($slot['end_time'])) }}
                                                                                </span>
                                                                            </div>

                                                                            <div class="small text-muted">
                                                                                @if ($slot['teacher'] !== 'N/A')
                                                                                    <div class="mb-1">
                                                                                        <i class="bi bi-person me-1"></i>
                                                                                        <strong>{{ __('Teacher:') }}</strong>
                                                                                        {{ $slot['teacher'] }}
                                                                                    </div>
                                                                                @endif

                                                                                @if ($slot['room'])
                                                                                    <div class="mb-1">
                                                                                        <i class="bi bi-door-open me-1"></i>
                                                                                        <strong>{{ __('Room:') }}</strong>
                                                                                        {{ $slot['room'] }}
                                                                                    </div>
                                                                                @endif

                                                                                @if ($slot['notes'])
                                                                                    <div class="mt-2 fst-italic">
                                                                                        <i class="bi bi-sticky me-1"></i>
                                                                                        {{ $slot['notes'] }}
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted fst-italic">
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            {{ __('No classes scheduled') }}
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                {{ __('How to Use Your Schedule') }}
                            </h6>
                            <ul class="small mb-0">
                                <li>{{ __('Check your schedule daily to know which classes you have') }}</li>
                                <li>{{ __('Note the room numbers to find your classes easily') }}</li>
                                <li>{{ __('Export to your calendar app for reminders') }}</li>
                                <li>{{ __('Contact your class teacher if you notice any conflicts') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-lightbulb text-success me-2"></i>
                                {{ __('Schedule Tips') }}
                            </h6>
                            <ul class="small mb-0">
                                <li>{{ __('Arrive 5 minutes early to each class') }}</li>
                                <li>{{ __('Prepare materials needed for each subject') }}</li>
                                <li>{{ __('Use breaks wisely to prepare for next class') }}</li>
                                <li>{{ __('Keep this schedule accessible on your device') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
