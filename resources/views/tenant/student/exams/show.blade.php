@extends('layouts.tenant.student')

@section('title', $exam->title)

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <a href="{{ route('tenant.student.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Exams') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="mb-3">{{ $exam->title }}</h2>

                        <div class="d-flex gap-3 mb-4">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-clock me-1"></i> {{ $exam->duration_minutes }} {{ __('minutes') }}
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-trophy me-1"></i> {{ $exam->total_marks }} {{ __('marks') }}
                            </span>
                        </div>

                        @if ($exam->description)
                            <div class="mb-4">
                                <h5>{{ __('Description') }}</h5>
                                <p class="text-muted">{{ $exam->description }}</p>
                            </div>
                        @endif

                        @if ($exam->instructions)
                            <div class="mb-4">
                                <h5>{{ __('Instructions') }}</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($exam->instructions)) !!}
                                </div>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">{{ __('Start Time') }}</small>
                                <strong>{{ $exam->starts_at->format('M d, Y h:i A') }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('End Time') }}</small>
                                <strong>{{ $exam->ends_at->format('M d, Y h:i A') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Exam Status') }}</h5>

                        @if ($attempt)
                            @if ($attempt->completed_at)
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i> {{ __('Completed') }}
                                </div>
                                <p class="mb-1">{{ __('Submitted:') }}
                                    {{ $attempt->completed_at->format('M d, Y h:i A') }}</p>
                                @if ($exam->show_results_immediately || $exam->ends_at->isPast())
                                    <div class="mt-3 text-center">
                                        <h3 class="text-primary">{{ $attempt->score }} / {{ $exam->total_marks }}</h3>
                                        <p class="text-muted">{{ __('Your Score') }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-info mt-3">
                                        {{ __('Results will be available after the exam period ends.') }}
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-hourglass-split me-2"></i> {{ __('In Progress') }}
                                </div>
                                <a href="#" class="btn btn-primary w-100">{{ __('Resume Exam') }}</a>
                            @endif
                        @else
                            @php
                                $now = now();
                            @endphp

                            @if ($now->lt($exam->starts_at))
                                <div class="alert alert-info">
                                    {{ __('This exam has not started yet.') }}
                                </div>
                                <button class="btn btn-secondary w-100" disabled>{{ __('Not Started') }}</button>
                            @elseif($now->gt($exam->ends_at))
                                <div class="alert alert-danger">
                                    {{ __('This exam has ended.') }}
                                </div>
                                <button class="btn btn-secondary w-100" disabled>{{ __('Missed') }}</button>
                            @else
                                <div class="alert alert-primary">
                                    {{ __('You can start this exam now.') }}
                                </div>
                                <form action="{{ route('tenant.student.exams.start', $exam) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Start Exam') }}</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
