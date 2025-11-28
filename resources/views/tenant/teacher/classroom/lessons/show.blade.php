@extends('layouts.dashboard-teacher')

@section('title', __('Lesson Plan Details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h1 class="h3 mb-0">{{ $lessonPlan->title }}</h1>
                    <span class="badge bg-{{ $lessonPlan->status_badge_class ?? 'secondary' }}">
                        {{ $lessonPlan->status_label }}
                    </span>
                    <span class="badge bg-{{ $lessonPlan->review_status_badge_class ?? 'secondary' }}">
                        {{ $lessonPlan->review_status_label }}
                    </span>
                </div>
                <p class="text-muted mb-0">
                    <i class="bi bi-mortarboard me-2"></i>{{ $lessonPlan->class->name ?? __('Class N/A') }}
                    <span class="mx-2">•</span>
                    <i class="bi bi-journal-text me-2"></i>{{ $lessonPlan->subject->name ?? __('Subject N/A') }}
                    <span class="mx-2">•</span>
                    <i
                        class="bi bi-calendar-event me-2"></i>{{ optional($lessonPlan->lesson_date)->format('M d, Y') ?? __('TBD') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.teacher.classroom.lessons.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back to list') }}
                </a>
                @if ($lessonPlan->isEditable())
                    <a href="{{ route('tenant.teacher.classroom.lessons.edit', $lessonPlan) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>{{ __('Edit Plan') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>{{ __('Lesson Overview') }}
                        </h5>
                        <small class="text-muted">
                            {{ __('Last updated') }} {{ optional($lessonPlan->updated_at)->diffForHumans() }}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Class') }}</small>
                                <div class="fw-semibold">{{ $lessonPlan->class->name ?? __('Not specified') }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Subject') }}</small>
                                <div class="fw-semibold">{{ $lessonPlan->subject->name ?? __('Not specified') }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">{{ __('Lesson Date') }}</small>
                                <div class="fw-semibold">
                                    {{ optional($lessonPlan->lesson_date)->format('M d, Y') ?? __('TBD') }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">{{ __('Start / End Time') }}</small>
                                <div class="fw-semibold">
                                    @if ($lessonPlan->start_time && $lessonPlan->end_time)
                                        {{ $lessonPlan->start_time->format('H:i') }} -
                                        {{ $lessonPlan->end_time->format('H:i') }}
                                    @elseif($lessonPlan->start_time)
                                        {{ $lessonPlan->start_time->format('H:i') }}
                                    @else
                                        {{ __('Not set') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">{{ __('Duration') }}</small>
                                <div class="fw-semibold">
                                    {{ $lessonPlan->duration_minutes ? $lessonPlan->duration_minutes . ' ' . __('min') : __('Auto / TBD') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Review Status') }}</small>
                                <div class="fw-semibold">
                                    {{ $lessonPlan->review_status_label }}
                                    @if ($lessonPlan->reviewed_at)
                                        <span class="text-muted small">({{ __('updated') }}
                                            {{ $lessonPlan->reviewed_at->diffForHumans() }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Reviewer') }}</small>
                                <div class="fw-semibold">
                                    {{ $lessonPlan->reviewer->name ?? __('Not assigned yet') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bullseye me-2"></i>{{ __('Learning Objectives') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse ($lessonPlan->objectives ?? [] as $index => $objective)
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="badge bg-primary-subtle text-primary">{{ $index + 1 }}</span>
                                <div>{{ $objective }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No objectives provided.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2 flex-wrap">
                        <i class="bi bi-journal-text text-primary"></i>
                        <h5 class="card-title mb-0">{{ __('Instructional Content') }}</h5>
                    </div>
                    <div class="card-body">
                        <section class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-semibold">{{ __('Introduction / Warm-up') }}</h6>
                            @if ($lessonPlan->introduction)
                                <div class="wysiwyg-content">{!! $lessonPlan->introduction !!}</div>
                            @else
                                <p class="text-muted">{{ __('No introduction provided.') }}</p>
                            @endif
                        </section>
                        <section>
                            <h6 class="text-uppercase text-muted small fw-semibold">{{ __('Main Content / Instruction') }}
                            </h6>
                            @if ($lessonPlan->main_content)
                                <div class="wysiwyg-content">{!! $lessonPlan->main_content !!}</div>
                            @else
                                <p class="text-muted">{{ __('No main content provided.') }}</p>
                            @endif
                        </section>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people me-2"></i>{{ __('Student Activities') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse ($lessonPlan->activities ?? [] as $index => $activity)
                            <div class="d-flex gap-3 mb-3">
                                <span class="badge bg-success-subtle text-success">{{ $index + 1 }}</span>
                                <div>{{ $activity }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No activities documented.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>{{ __('Assessment & Follow-up') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <section class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-semibold">{{ __('Assessment / Evaluation') }}
                            </h6>
                            @if ($lessonPlan->assessment)
                                <div class="wysiwyg-content">{!! $lessonPlan->assessment !!}</div>
                            @else
                                <p class="text-muted">{{ __('No assessment notes provided.') }}</p>
                            @endif
                        </section>
                        <section>
                            <h6 class="text-uppercase text-muted small fw-semibold">{{ __('Homework / Extension') }}</h6>
                            @if ($lessonPlan->homework)
                                <div class="wysiwyg-content">{!! $lessonPlan->homework !!}</div>
                            @else
                                <p class="text-muted">{{ __('No homework provided.') }}</p>
                            @endif
                        </section>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sticky me-2"></i>{{ __('Additional Notes') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($lessonPlan->notes)
                            <div class="wysiwyg-content">{!! $lessonPlan->notes !!}</div>
                        @else
                            <p class="text-muted mb-0">{{ __('No additional notes recorded.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-wrench-adjustable me-2"></i>{{ __('Quick Actions') }}
                        </h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <form action="{{ route('tenant.teacher.classroom.lessons.duplicate', $lessonPlan) }}"
                            method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-dark w-100">
                                <i class="bi bi-files me-2"></i>{{ __('Duplicate Plan') }}
                            </button>
                        </form>
                        @if ($lessonPlan->canSubmit())
                            <form action="{{ route('tenant.teacher.classroom.lessons.submit', $lessonPlan) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="bi bi-send-check me-2"></i>
                                    {{ $lessonPlan->review_status === App\Models\LessonPlan::REVIEW_REVISION ? __('Resubmit for Review') : __('Submit for Review') }}
                                </button>
                            </form>
                        @endif
                        @if (
                            $lessonPlan->review_status === App\Models\LessonPlan::REVIEW_APPROVED &&
                                $lessonPlan->status === App\Models\LessonPlan::STATUS_SCHEDULED)
                            <form action="{{ route('tenant.teacher.classroom.lessons.mark-in-progress', $lessonPlan) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-info w-100">
                                    <i class="bi bi-play-circle me-2"></i>{{ __('Mark In Progress') }}
                                </button>
                            </form>
                        @endif
                        @if ($lessonPlan->canMarkDelivered())
                            <form action="{{ route('tenant.teacher.classroom.lessons.mark-completed', $lessonPlan) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-check2-circle me-2"></i>{{ __('Mark as Completed') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-box-seam me-2"></i>{{ __('Materials Needed') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse ($lessonPlan->materials_needed ?? [] as $material)
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check2-circle text-success"></i>
                                <span>{{ $material }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No materials listed.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>{{ __('Review Feedback') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($lessonPlan->review_feedback)
                            <p class="mb-0">{{ $lessonPlan->review_feedback }}</p>
                            <small class="text-muted d-block mt-2">
                                {{ __('Reviewed by') }} {{ $lessonPlan->reviewer->name ?? __('Admin') }}
                                @if ($lessonPlan->reviewed_at)
                                    • {{ $lessonPlan->reviewed_at->format('M d, Y H:i') }}
                                @endif
                            </small>
                        @else
                            <p class="text-muted mb-0">{{ __('No feedback recorded yet.') }}</p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>{{ __('Recent Lesson Plans') }}
                        </h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($recentPlans as $plan)
                            <a href="{{ route('tenant.teacher.classroom.lessons.show', $plan) }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $plan->title }}</div>
                                    <small class="text-muted">
                                        {{ optional($plan->lesson_date)->format('M d, Y') ?? __('TBD') }}
                                        • {{ $plan->status_label }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $plan->review_status_badge_class ?? 'secondary' }}">
                                    {{ $plan->review_status_label }}
                                </span>
                            </a>
                        @empty
                            <div class="list-group-item text-muted">{{ __('No other lesson plans yet.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .wysiwyg-content {
            background: #f8f9fa;
            border-radius: 0.4rem;
            padding: 1rem;
        }

        .wysiwyg-content :where(p, ul, ol) {
            margin-bottom: 0.75rem;
        }

        .wysiwyg-content ul {
            padding-left: 1.25rem;
        }
    </style>
@endpush
