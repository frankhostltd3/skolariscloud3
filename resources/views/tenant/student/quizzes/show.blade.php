@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', $quiz->title)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('tenant.student.quizzes.index') }}">{{ __('Quizzes') }}</a></li>
            <li class="breadcrumb-item active">{{ $quiz->title }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <!-- Quiz Details Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>{{ $quiz->title }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Description -->
                    @if($quiz->description)
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">{{ __('Description') }}</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{!! nl2br(e($quiz->description)) !!}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Quiz Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <i class="bi bi-person text-primary me-2"></i>
                                <strong>{{ __('Teacher:') }}</strong>
                                <span>{{ $quiz->teacher->name ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-list-ol text-primary me-2"></i>
                                <strong>{{ __('Total Questions:') }}</strong>
                                <span>{{ $quiz->questions->count() }}</span>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-star text-warning me-2"></i>
                                <strong>{{ __('Total Points:') }}</strong>
                                <span>{{ $quiz->total_points ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($quiz->duration_minutes)
                                <div class="mb-3">
                                    <i class="bi bi-clock text-info me-2"></i>
                                    <strong>{{ __('Duration:') }}</strong>
                                    <span>{{ $quiz->duration_minutes }} {{ __('minutes') }}</span>
                                </div>
                            @endif
                            @if($quiz->start_at)
                                <div class="mb-3">
                                    <i class="bi bi-calendar-event text-success me-2"></i>
                                    <strong>{{ __('Opens:') }}</strong>
                                    <span>{{ $quiz->start_at->format('M d, Y g:i A') }}</span>
                                </div>
                            @endif
                            @if($quiz->end_at)
                                <div class="mb-3">
                                    <i class="bi bi-calendar-x text-danger me-2"></i>
                                    <strong>{{ __('Closes:') }}</strong>
                                    <span>{{ $quiz->end_at->format('M d, Y g:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>{{ __('Important Instructions') }}
                        </h6>
                        <ul class="mb-0 small">
                            <li>{{ __('Read all questions carefully before answering') }}</li>
                            @if($quiz->duration_minutes)
                                <li>{{ __('You have') }} <strong>{{ $quiz->duration_minutes }} {{ __('minutes') }}</strong> {{ __('to complete this quiz') }}</li>
                                <li class="text-danger"><strong>{{ __('The timer cannot be paused once started') }}</strong></li>
                            @endif
                            <li>{{ __('Make sure you have a stable internet connection') }}</li>
                            <li>{{ __('Review your answers before submitting') }}</li>
                            <li>{{ __('You cannot change your answers after submission') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Attempt Status -->
            @if($attempt)
                @if($attempt->submitted_at)
                    <!-- Completed Quiz -->
                    <div class="card shadow-sm border-success mb-4">
                        <div class="card-header bg-success bg-opacity-10">
                            <h5 class="mb-0">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                {{ __('Quiz Completed') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('Submitted on:') }}</strong><br>
                                        {{ $attempt->submitted_at->format('M d, Y g:i A') }}
                                    </p>
                                    @if($attempt->is_late ?? false)
                                        <span class="badge bg-warning">{{ __('Late Submission') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($attempt->score !== null)
                                        <p class="mb-2">
                                            <strong>{{ __('Your Score:') }}</strong>
                                        </p>
                                        <div class="display-4 text-success">
                                            {{ $attempt->score }}%
                                        </div>
                                        @if($attempt->grade)
                                            <div class="mt-2">
                                                <span class="badge bg-success fs-5">{{ __('Grade:') }} {{ $attempt->grade }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-hourglass-split me-2"></i>
                                            {{ __('Your quiz is being graded. Check back later for results.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($attempt->feedback && $quiz->show_feedback_after_grades)
                                <hr>
                                <h6>{{ __('Teacher Feedback:') }}</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{!! nl2br(e($attempt->feedback)) !!}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- In Progress -->
                    <div class="card shadow-sm border-warning mb-4">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h5 class="mb-0">
                                <i class="bi bi-play-circle text-warning me-2"></i>
                                {{ __('Quiz In Progress') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                {{ __('You have started this quiz but have not submitted it yet.') }}
                            </p>
                            <p class="mb-3">
                                <strong>{{ __('Started:') }}</strong> {{ $attempt->started_at->format('M d, Y g:i A') }}
                            </p>
                            @php
                                $now = now();
                                $isOpen = (!$quiz->start_at || $now->gte($quiz->start_at)) && (!$quiz->end_at || $now->lte($quiz->end_at));
                            @endphp
                            @if($isOpen)
                                <a href="{{ route('tenant.student.quizzes.take', $quiz->id) }}" class="btn btn-warning">
                                    <i class="bi bi-play-circle me-2"></i>{{ __('Continue Quiz') }}
                                </a>
                            @else
                                <div class="alert alert-danger mb-0">
                                    <i class="bi bi-x-circle me-2"></i>
                                    {{ __('The quiz time window has closed.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <!-- Not Started -->
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h5 class="mb-0">
                            <i class="bi bi-play-fill text-primary me-2"></i>
                            {{ __('Ready to Start?') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $now = now();
                            $isBeforeStart = $quiz->start_at && $now->lt($quiz->start_at);
                            $isAfterEnd = $quiz->end_at && $now->gt($quiz->end_at);
                            $isOpen = !$isBeforeStart && !$isAfterEnd;
                        @endphp

                        @if($isBeforeStart)
                            <div class="alert alert-warning">
                                <i class="bi bi-clock me-2"></i>
                                {{ __('This quiz will be available on:') }}
                                <strong>{{ $quiz->start_at->format('M d, Y g:i A') }}</strong>
                            </div>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-lock me-2"></i>{{ __('Quiz Not Yet Available') }}
                            </button>
                        @elseif($isAfterEnd)
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle me-2"></i>
                                {{ __('This quiz closed on:') }}
                                <strong>{{ $quiz->end_at->format('M d, Y g:i A') }}</strong>
                            </div>
                        @else
                            <p class="mb-3">{{ __('Click the button below to start the quiz. Make sure you are ready!') }}</p>
                            <form method="POST" action="{{ route('tenant.student.quizzes.start', $quiz->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-play-fill me-2"></i>{{ __('Start Quiz Now') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">{{ __('Quiz Status') }}</h6>
                </div>
                <div class="card-body">
                    @if($attempt && $attempt->submitted_at)
                        <div class="text-center mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-success">{{ __('Completed') }}</h5>
                        </div>
                    @elseif($attempt)
                        <div class="text-center mb-3">
                            <i class="bi bi-play-circle text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-warning">{{ __('In Progress') }}</h5>
                        </div>
                    @else
                        <div class="text-center mb-3">
                            <i class="bi bi-circle text-secondary" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-secondary">{{ __('Not Started') }}</h5>
                        </div>
                    @endif

                    <hr>

                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <strong>{{ __('Questions:') }}</strong> {{ $quiz->questions->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>{{ __('Total Points:') }}</strong> {{ $quiz->total_points ?? 'N/A' }}
                        </li>
                        @if($quiz->duration_minutes)
                            <li class="mb-2">
                                <strong>{{ __('Time Limit:') }}</strong> {{ $quiz->duration_minutes }} min
                            </li>
                        @endif
                        @if($attempt && $attempt->score !== null)
                            <li class="mb-2">
                                <strong>{{ __('Your Score:') }}</strong> 
                                <span class="text-success">{{ $attempt->score }}%</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>{{ __('Need Help?') }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">{{ __('If you have questions about this quiz:') }}</p>
                    <ul class="small mb-3">
                        <li>{{ __('Contact your teacher before starting') }}</li>
                        <li>{{ __('Ensure you understand all instructions') }}</li>
                        <li>{{ __('Check your internet connection') }}</li>
                    </ul>
                    @if($quiz->teacher)
                        <p class="small mb-0">
                            <strong>{{ __('Teacher:') }}</strong><br>
                            {{ $quiz->teacher->name }}<br>
                            @if($quiz->teacher->email)
                                <a href="mailto:{{ $quiz->teacher->email }}">{{ $quiz->teacher->email }}</a>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

