@extends('layouts.tenant.student')

@section('title', 'Quizzes & Exams')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-clipboard-check me-2"></i>{{ __('Quizzes & Exams') }}
            </h4>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            // Calculate statistics
            // Handle both Collection and Paginator
            $totalQuizzes = method_exists($quizzes, 'total') ? $quizzes->total() : $quizzes->count();
            $notStarted = 0;
            $inProgress = 0;
            $completed = 0;
            $averageScore = 0;
            $totalScore = 0;
            $gradedCount = 0;

            foreach ($quizzes as $quiz) {
                $attempt = $attempts->get($quiz->id);
                if (!$attempt) {
                    $notStarted++;
                } elseif ($attempt && !$attempt->submitted_at) {
                    $inProgress++;
                } else {
                    $completed++;
                    if ($attempt->score !== null) {
                        $totalPoints = max(
                            1,
                            $quiz->total_points ?? ($quiz->total_marks ?? ($quiz->questions_count ?? 1)),
                        );
                        $normalized = round(($attempt->score / $totalPoints) * 100, 1);
                        $totalScore += $normalized;
                        $gradedCount++;
                    }
                }
            }

            if ($gradedCount > 0) {
                $averageScore = round($totalScore / $gradedCount, 1);
            }
        @endphp

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Total Quizzes') }}</h6>
                                <h3 class="mb-0">{{ $totalQuizzes }}</h3>
                            </div>
                            <div class="text-primary" style="font-size: 2rem;">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Not Started') }}</h6>
                                <h3 class="mb-0">{{ $notStarted }}</h3>
                            </div>
                            <div class="text-secondary" style="font-size: 2rem;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Completed') }}</h6>
                                <h3 class="mb-0 text-success">{{ $completed }}</h3>
                            </div>
                            <div class="text-success" style="font-size: 2rem;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Average Score') }}</h6>
                                <h3 class="mb-0 text-info">{{ $averageScore }}%</h3>
                            </div>
                            <div class="text-info" style="font-size: 2rem;">
                                <i class="bi bi-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($quizzes->count() > 0)
            <!-- Quizzes Grid -->
            <div class="row">
                @foreach ($quizzes as $quiz)
                    @php
                        $attempt = $attempts->get($quiz->id);
                        $isNotStarted = !$attempt;
                        $isInProgress = $attempt && !$attempt->submitted_at;
                        $isCompleted = $attempt && $attempt->submitted_at;
                        $isGraded = $isCompleted && $attempt->score !== null;

                        // Check if quiz is available now
                        $now = now();
                        $isBeforeStart = $quiz->start_at && $now->lt($quiz->start_at);
                        $isAfterEnd = $quiz->end_at && $now->gt($quiz->end_at);
                        $isOpen = !$isBeforeStart && !$isAfterEnd;

                        // Status badge
                        if ($isGraded) {
                            $statusBadge = 'success';
                            $statusText = __('Graded');
                            $statusIcon = 'check-circle-fill';
                        } elseif ($isCompleted) {
                            $statusBadge = 'info';
                            $statusText = __('Submitted');
                            $statusIcon = 'hourglass-split';
                        } elseif ($isInProgress) {
                            $statusBadge = 'warning';
                            $statusText = __('In Progress');
                            $statusIcon = 'play-circle';
                        } else {
                            $statusBadge = 'secondary';
                            $statusText = __('Not Started');
                            $statusIcon = 'circle';
                        }

                        // Availability status
                        if ($isBeforeStart) {
                            $availabilityBadge = 'secondary';
                            $availabilityText = __('Opens Soon');
                        } elseif ($isAfterEnd) {
                            $availabilityBadge = 'danger';
                            $availabilityText = __('Closed');
                        } else {
                            $availabilityBadge = 'success';
                            $availabilityText = __('Open');
                        }
                    @endphp

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <!-- Card Header with Status -->
                            <div class="card-header bg-{{ $statusBadge }} bg-opacity-10 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-{{ $statusBadge }}">
                                        <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                    </span>
                                    <span class="badge bg-{{ $availabilityBadge }}">
                                        {{ $availabilityText }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <!-- Quiz Title -->
                                <h5 class="card-title mb-2">
                                    <i class="bi bi-file-earmark-text text-primary me-2"></i>{{ $quiz->title }}
                                </h5>

                                <!-- Description -->
                                @if ($quiz->description)
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($quiz->description, 100) }}
                                    </p>
                                @endif

                                <!-- Quiz Details -->
                                <div class="mb-3">
                                    <div class="row g-2 small">
                                        <div class="col-6">
                                            <i class="bi bi-person text-muted me-1"></i>
                                            <strong>{{ __('Teacher:') }}</strong><br>
                                            <span class="ms-3">{{ $quiz->teacher->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <i class="bi bi-star text-warning me-1"></i>
                                            <strong>{{ __('Points:') }}</strong><br>
                                            <span class="ms-3">{{ $quiz->total_points ?? 'N/A' }}</span>
                                        </div>
                                        @if ($quiz->duration_minutes)
                                            <div class="col-6">
                                                <i class="bi bi-clock text-info me-1"></i>
                                                <strong>{{ __('Duration:') }}</strong><br>
                                                <span class="ms-3">{{ $quiz->duration_minutes }}
                                                    {{ __('mins') }}</span>
                                            </div>
                                        @endif
                                        <div class="col-6">
                                            <i class="bi bi-list-ol text-primary me-1"></i>
                                            <strong>{{ __('Questions:') }}</strong><br>
                                            <span
                                                class="ms-3">{{ $quiz->questions_count ?? ($quiz->questions->count() ?? 0) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Time Information -->
                                <div class="mb-3 small">
                                    @if ($quiz->start_at)
                                        <div class="mb-1">
                                            <i class="bi bi-calendar-event text-success me-1"></i>
                                            <strong>{{ __('Opens:') }}</strong>
                                            {{ $quiz->start_at->format('M d, Y g:i A') }}
                                        </div>
                                    @endif
                                    @if ($quiz->end_at)
                                        <div class="mb-1">
                                            <i class="bi bi-calendar-x text-danger me-1"></i>
                                            <strong>{{ __('Closes:') }}</strong>
                                            {{ $quiz->end_at->format('M d, Y g:i A') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Score Display (if graded) -->
                                @if ($isGraded)
                                    @php
                                        $rawScore = $attempt->score;
                                        $pointsTotal = max(
                                            1,
                                            $quiz->total_points ??
                                                ($quiz->total_marks ?? ($quiz->questions_count ?? 1)),
                                        );
                                        $scorePercent =
                                            $rawScore !== null ? round(($rawScore / $pointsTotal) * 100, 1) : null;
                                    @endphp
                                    <div class="alert alert-success mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-trophy me-2"></i>
                                                <strong>{{ __('Score:') }}</strong>
                                            </span>
                                            <span class="fs-5">
                                                {{ $scorePercent }}%
                                            </span>
                                        </div>
                                        <small
                                            class="text-muted">{{ $rawScore }}/{{ $quiz->total_points ?? ($quiz->total_marks ?? 'N/A') }}</small>
                                        @if ($attempt->grade)
                                            <div class="mt-2 text-center">
                                                <span class="badge bg-success fs-6">{{ $attempt->grade }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Progress Indicator (if in progress) -->
                                @if ($isInProgress)
                                    <div class="alert alert-warning mb-3">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        {{ __('You have started this quiz but not submitted yet.') }}
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="mt-auto">
                                    @if ($isCompleted)
                                        <a href="{{ route('tenant.student.quizzes.show', $quiz->id) }}"
                                            class="btn btn-outline-primary w-100">
                                            <i class="bi bi-eye me-2"></i>{{ __('View Results') }}
                                        </a>
                                    @elseif($isInProgress && $isOpen)
                                        <a href="{{ route('tenant.student.quizzes.take', $quiz->id) }}"
                                            class="btn btn-warning w-100">
                                            <i class="bi bi-play-circle me-2"></i>{{ __('Continue Quiz') }}
                                        </a>
                                    @elseif($isOpen && !$isAfterEnd)
                                        <a href="{{ route('tenant.student.quizzes.show', $quiz->id) }}"
                                            class="btn btn-success w-100">
                                            <i class="bi bi-play-fill me-2"></i>{{ __('Start Quiz') }}
                                        </a>
                                    @elseif($isBeforeStart)
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="bi bi-lock me-2"></i>{{ __('Not Yet Available') }}
                                        </button>
                                    @else
                                        <a href="{{ route('tenant.student.quizzes.show', $quiz->id) }}"
                                            class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-eye me-2"></i>{{ __('View Details') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Footer with Timestamp -->
                            <div class="card-footer bg-light border-0 small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ __('Created:') }} {{ $quiz->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if (method_exists($quizzes, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $quizzes->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('No Quizzes Available') }}</h5>
                    <p class="text-muted">{{ __('There are no quizzes available for you at the moment.') }}</p>
                    <p class="text-muted small">{{ __('Check back later or contact your teacher for more information.') }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Tips & Information Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-info bg-opacity-10 border-0">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>{{ __('Quiz Tips & Guidelines') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-2">{{ __('Before Starting:') }}</h6>
                        <ul class="small mb-3">
                            <li>{{ __('Check the quiz duration and plan your time accordingly') }}</li>
                            <li>{{ __('Ensure you have a stable internet connection') }}</li>
                            <li>{{ __('Read all instructions carefully before beginning') }}</li>
                            <li>{{ __('Make sure you\'re in a quiet environment') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-2">{{ __('During the Quiz:') }}</h6>
                        <ul class="small mb-3">
                            <li>{{ __('Answer all questions to the best of your ability') }}</li>
                            <li>{{ __('Keep an eye on the timer if there\'s a time limit') }}</li>
                            <li>{{ __('Review your answers before submitting') }}</li>
                            <li>{{ __('Submit before the deadline - late submissions may not be accepted') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>{{ __('Important:') }}</strong>
                    {{ __('Once you start a quiz with a time limit, the timer cannot be paused. Make sure you have enough time to complete it.') }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection
