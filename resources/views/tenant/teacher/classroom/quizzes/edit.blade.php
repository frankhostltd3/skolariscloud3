@extends('layouts.dashboard-teacher')

@section('title', 'Edit Quiz')

@include('components.wysiwyg')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Quiz
                </h1>
                <p class="text-muted mb-0">Update quiz details and settings</p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.quizzes.show', $quiz) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Quiz
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('tenant.teacher.classroom.quizzes.update', $quiz) }}" method="POST" id="quizForm">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Basic Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Quiz Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $quiz->title) }}"
                                    placeholder="e.g., Chapter 5 Review Quiz" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control wysiwyg-editor @error('description') is-invalid @enderror" id="description"
                                    name="description" data-placeholder="Describe the quiz overview">{{ old('description', $quiz->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Instructions -->
                            <div class="mb-3">
                                <label for="instructions" class="form-label">Instructions for Students</label>
                                <textarea class="form-control wysiwyg-editor @error('instructions') is-invalid @enderror" id="instructions"
                                    name="instructions" data-placeholder="Provide guidance for students">{{ old('instructions', $quiz->instructions) }}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Class and Subject -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id', $quiz->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $quiz->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar-range me-2"></i>Availability
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="available_from" class="form-label">Available From</label>
                                    <input type="datetime-local"
                                        class="form-control @error('available_from') is-invalid @enderror"
                                        id="available_from" name="available_from"
                                        value="{{ old('available_from', $quiz->available_from?->format('Y-m-d\TH:i')) }}">
                                    @error('available_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="available_until" class="form-label">Available Until</label>
                                    <input type="datetime-local"
                                        class="form-control @error('available_until') is-invalid @enderror"
                                        id="available_until" name="available_until"
                                        value="{{ old('available_until', $quiz->available_until?->format('Y-m-d\TH:i')) }}">
                                    @error('available_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-gear me-2"></i>Quiz Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="duration_minutes" class="form-label">Time Limit (minutes)</label>
                                    <input type="number"
                                        class="form-control @error('duration_minutes') is-invalid @enderror"
                                        id="duration_minutes" name="duration_minutes"
                                        value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1"
                                        placeholder="30">
                                    <small class="text-muted">Leave empty for unlimited time</small>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="total_marks" class="form-label">Total Marks <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_marks') is-invalid @enderror"
                                        id="total_marks" name="total_marks"
                                        value="{{ old('total_marks', $quiz->total_marks) }}" min="1" required>
                                    @error('total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="pass_marks" class="form-label">Pass Marks</label>
                                    <input type="number" class="form-control @error('pass_marks') is-invalid @enderror"
                                        id="pass_marks" name="pass_marks"
                                        value="{{ old('pass_marks', $quiz->pass_marks) }}" min="1"
                                        placeholder="50">
                                    @error('pass_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="max_attempts" class="form-label">Maximum Attempts</label>
                                <input type="number" class="form-control @error('max_attempts') is-invalid @enderror"
                                    id="max_attempts" name="max_attempts"
                                    value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1"
                                    max="10">
                                @error('max_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Options -->
                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="shuffle_questions"
                                            name="shuffle_questions" value="1"
                                            {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shuffle_questions">
                                            Shuffle Questions
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="shuffle_answers"
                                            name="shuffle_answers" value="1"
                                            {{ old('shuffle_answers', $quiz->shuffle_answers) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shuffle_answers">
                                            Shuffle Answer Options
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_results_immediately"
                                            name="show_results_immediately" value="1"
                                            {{ old('show_results_immediately', $quiz->show_results_immediately) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_results_immediately">
                                            Show Results Immediately
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_correct_answers"
                                            name="show_correct_answers" value="1"
                                            {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_correct_answers">
                                            Show Correct Answers
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_review"
                                            name="allow_review" value="1"
                                            {{ old('allow_review', $quiz->allow_review) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_review">
                                            Allow Review After Submission
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-toggle-on me-2"></i>Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Quiz Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="draft"
                                        {{ old('status', $quiz->status) == 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="published"
                                        {{ old('status', $quiz->status) == 'published' ? 'selected' : '' }}>
                                        Published
                                    </option>
                                    <option value="archived"
                                        {{ old('status', $quiz->status) == 'archived' ? 'selected' : '' }}>
                                        Archived
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('tenant.teacher.classroom.quizzes.show', $quiz) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Quiz
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const availableUntil = document.getElementById('available_until');
            if (!availableUntil) {
                return;
            }

            availableUntil.addEventListener('change', function() {
                const availableFrom = document.getElementById('available_from');
                const fromValue = availableFrom ? availableFrom.value : '';
                const untilValue = this.value;

                if (fromValue && untilValue && untilValue < fromValue) {
                    alert('End date must be after start date');
                    this.value = '';
                }
            });
        });
    </script>
@endpush
