@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Quiz Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $quiz->title }}</h1>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $quiz->status === 'published' ? 'success' : ($quiz->status === 'draft' ? 'secondary' : 'warning') }}">
                    {{ ucfirst($quiz->status) }}
                </span>
            </p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.quizzes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Quizzes
            </a>
            <a href="{{ route('tenant.teacher.classroom.quizzes.edit', $quiz) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit Quiz
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Quiz Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Quiz Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mb-0">{{ $quiz->description ?: 'No description provided' }}</p>
                    </div>

                    @if($quiz->instructions)
                        <div class="mb-3">
                            <strong>Instructions:</strong>
                            <p class="mb-0">{{ $quiz->instructions }}</p>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Class:</strong>
                            <p class="mb-0">{{ $quiz->class->name }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Subject:</strong>
                            <p class="mb-0">{{ $quiz->subject->name }}</p>
                        </div>

                        @if($quiz->available_from)
                            <div class="col-md-6 mb-3">
                                <strong>Available From:</strong>
                                <p class="mb-0">{{ $quiz->available_from->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif

                        @if($quiz->available_until)
                            <div class="col-md-6 mb-3">
                                <strong>Available Until:</strong>
                                <p class="mb-0">{{ $quiz->available_until->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-question-circle me-2"></i>Questions ({{ $quiz->questions->count() }})
                    </h5>
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Question
                    </button>
                </div>
                <div class="card-body">
                    @if($quiz->questions->count() > 0)
                        <!-- Questions will be listed here -->
                        <p class="text-muted">Questions functionality coming soon...</p>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-question-circle text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No Questions Added</h5>
                            <p class="text-muted mb-4">Start building your quiz by adding questions</p>
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add First Question
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quiz Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Quiz Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Questions:</span>
                        <strong>{{ $quiz->questions->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Marks:</span>
                        <strong>{{ $quiz->total_marks }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Pass Marks:</span>
                        <strong>{{ $quiz->pass_marks ?: 'Not set' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Duration:</span>
                        <strong>{{ $quiz->duration_minutes ? $quiz->duration_minutes . ' mins' : 'Unlimited' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Max Attempts:</span>
                        <strong>{{ $quiz->max_attempts }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Attempts:</span>
                        <strong>{{ $quiz->attempts->count() }}</strong>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Settings
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-{{ $quiz->shuffle_questions ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                            Shuffle Questions
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-{{ $quiz->shuffle_answers ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                            Shuffle Answers
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-{{ $quiz->show_results_immediately ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                            Show Results Immediately
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-{{ $quiz->show_correct_answers ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                            Show Correct Answers
                        </li>
                        <li>
                            <i class="bi bi-{{ $quiz->allow_review ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                            Allow Review
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

