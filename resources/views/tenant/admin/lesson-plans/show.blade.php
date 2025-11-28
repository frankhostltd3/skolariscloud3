@extends('tenant.layouts.app')

@section('title', __('Lesson Plan Review'))

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $lessonPlan->title }}</h1>
                <p class="text-muted mb-0">
                    {{ __('Submitted by :teacher on :date', ['teacher' => $lessonPlan->teacher->name ?? __('Unknown'), 'date' => optional($lessonPlan->submitted_at)->toDayDateTimeString() ?? __('Draft')]) }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.admin.lesson-plans.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
                </a>
            </div>
        </div>

        @foreach (['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $flash => $variant)
            @if (session($flash))
                <div class="alert alert-{{ $variant }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session($flash) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="{{ __('Close') }}"></button>
                </div>
            @endif
        @endforeach

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Status') }}</p>
                        <span
                            class="badge bg-{{ $lessonPlan->status_badge_class ?? 'secondary' }}">{{ $lessonPlan->status_label }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Review') }}</p>
                        <span
                            class="badge bg-{{ $lessonPlan->review_status_badge_class ?? 'secondary' }}">{{ $lessonPlan->review_status_label }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Submitted') }}</p>
                        <div class="fw-semibold">
                            {{ optional($lessonPlan->submitted_at)->toDayDateTimeString() ?? __('Not submitted') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Reviewer') }}</p>
                        <div class="fw-semibold">{{ $lessonPlan->reviewer->name ?? __('Not assigned') }}</div>
                        @if ($lessonPlan->reviewed_at)
                            <div class="text-muted small">{{ $lessonPlan->reviewed_at->diffForHumans() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Lesson Overview') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small">{{ __('Class') }}</dt>
                            <dd class="col-sm-8">{{ $lessonPlan->class->name ?? __('Class N/A') }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Subject') }}</dt>
                            <dd class="col-sm-8">{{ $lessonPlan->subject->name ?? __('Subject N/A') }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Lesson Date') }}</dt>
                            <dd class="col-sm-8">
                                {{ optional($lessonPlan->lesson_date)->toFormattedDateString() ?? __('TBD') }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Time & Duration') }}</dt>
                            <dd class="col-sm-8">
                                @if ($lessonPlan->start_time && $lessonPlan->end_time)
                                    {{ $lessonPlan->start_time->format('H:i') }} –
                                    {{ $lessonPlan->end_time->format('H:i') }} ({{ $lessonPlan->duration_minutes ?? '—' }}
                                    {{ __('mins') }})
                                @else
                                    {{ __('Not provided') }}
                                @endif
                            </dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Teacher Notes') }}</dt>
                            <dd class="col-sm-8">{{ $lessonPlan->notes ?? __('—') }}</dd>
                        </dl>
                    </div>
                </div>

                @php
                    $sections = [
                        __('Objectives') => $lessonPlan->objectives,
                        __('Materials Needed') => $lessonPlan->materials_needed,
                        __('Introduction') => $lessonPlan->introduction,
                        __('Main Content') => $lessonPlan->main_content,
                        __('Activities') => $lessonPlan->activities,
                        __('Assessment') => $lessonPlan->assessment,
                        __('Homework') => $lessonPlan->homework,
                    ];
                @endphp

                @foreach ($sections as $title => $content)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $title }}</h5>
                        </div>
                        <div class="card-body">
                            @if (is_array($content) && !empty($content))
                                <ul class="mb-0">
                                    @foreach ($content as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @elseif(is_string($content) && trim($content) !== '')
                                <p class="mb-0">{!! nl2br(e($content)) !!}</p>
                            @else
                                <p class="text-muted mb-0">{{ __('No details provided.') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Recent Plans from this Teacher') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentPlans->isEmpty())
                            <p class="text-muted mb-0">{{ __('No other lesson plans on record yet.') }}</p>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($recentPlans as $plan)
                                    <a href="{{ route('tenant.admin.lesson-plans.show', $plan) }}"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $plan->title }}</div>
                                            <div class="text-muted small">
                                                {{ optional($plan->lesson_date)->format('M d, Y') ?? __('TBD') }}</div>
                                        </div>
                                        <span
                                            class="badge bg-{{ $plan->review_status_badge_class ?? 'secondary' }}">{{ $plan->review_status_label }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Teacher & Class Info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="text-muted small mb-1">{{ __('Teacher') }}</p>
                            <div class="fw-semibold">{{ $lessonPlan->teacher->name ?? __('Unknown') }}</div>
                            <div class="text-muted small">{{ $lessonPlan->teacher->email ?? __('No email') }}</div>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1">{{ __('Class') }}</p>
                            <div class="fw-semibold">{{ $lessonPlan->class->name ?? __('Class N/A') }}</div>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">{{ __('Subject') }}</p>
                            <div class="fw-semibold">{{ $lessonPlan->subject->name ?? __('Subject N/A') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Review Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        @if (in_array($lessonPlan->review_status, [
                                \App\Models\LessonPlan::REVIEW_PENDING,
                                \App\Models\LessonPlan::REVIEW_REVISION,
                            ]))
                            <form action="{{ route('tenant.admin.lesson-plans.approve', $lessonPlan) }}" method="POST"
                                class="mb-3">
                                @csrf
                                <label class="form-label fw-semibold">{{ __('Approve Plan') }}</label>
                                <textarea name="feedback" class="form-control mb-2" rows="3"
                                    placeholder="{{ __('Optional note for the teacher...') }}">{{ old('feedback') }}</textarea>
                                <button type="submit" class="btn btn-success w-100">{{ __('Approve & Notify') }}</button>
                            </form>

                            <form action="{{ route('tenant.admin.lesson-plans.request-revision', $lessonPlan) }}"
                                method="POST" class="mb-3">
                                @csrf
                                <label class="form-label fw-semibold">{{ __('Request Revision') }}</label>
                                <textarea name="feedback" class="form-control mb-2" rows="3" required
                                    placeholder="{{ __('Explain what needs to change...') }}">{{ old('feedback') }}</textarea>
                                <button type="submit"
                                    class="btn btn-info w-100">{{ __('Send Revision Request') }}</button>
                            </form>

                            <form action="{{ route('tenant.admin.lesson-plans.reject', $lessonPlan) }}" method="POST">
                                @csrf
                                <label class="form-label fw-semibold">{{ __('Reject Plan') }}</label>
                                <textarea name="feedback" class="form-control mb-2" rows="3" required
                                    placeholder="{{ __('Share the reason for rejection...') }}">{{ old('feedback') }}</textarea>
                                <button type="submit" class="btn btn-danger w-100">{{ __('Reject & Notify') }}</button>
                            </form>
                        @else
                            <p class="text-muted">{{ __('This lesson plan has already been processed.') }}</p>
                            <form action="{{ route('tenant.admin.lesson-plans.reopen', $lessonPlan) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary w-100"
                                    onclick="return confirm('{{ __('Reopen this review for further changes?') }}');">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Reopen Review') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Teacher Feedback History') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($lessonPlan->review_feedback)
                            <p class="mb-0">{{ $lessonPlan->review_feedback }}</p>
                            <div class="text-muted small mt-2">
                                {{ __('Last updated :date', ['date' => optional($lessonPlan->reviewed_at)->toDayDateTimeString() ?? __('N/A')]) }}
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No feedback shared yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
