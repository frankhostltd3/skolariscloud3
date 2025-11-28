@extends('layouts.dashboard-teacher')

@section('title', 'Create Assignment')

@include('components.wysiwyg')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Create New Assignment</h1>
                <p class="text-muted mb-0">Create a new assignment for your students</p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.exercises.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('tenant.teacher.classroom.exercises.store') }}" method="POST">
                            @csrf

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Assignment Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}"
                                    placeholder="e.g., Chapter 5 Problem Set" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control wysiwyg-editor @error('description') is-invalid @enderror" id="description"
                                    name="description" data-placeholder="Describe the assignment context">{!! old('description') !!}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Instructions -->
                            <div class="mb-3">
                                <label for="instructions" class="form-label">Instructions</label>
                                <textarea class="form-control wysiwyg-editor @error('instructions') is-invalid @enderror" id="instructions"
                                    name="instructions" data-placeholder="Provide step-by-step instructions">{!! old('instructions') !!}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Provide clear, step-by-step instructions for completing this
                                    assignment.</div>
                            </div>

                            <!-- Assignment Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Assignment Content <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control wysiwyg-editor wysiwyg-large @error('content') is-invalid @enderror" id="content"
                                    name="content" data-placeholder="Enter the assignment content here">{!! old('content') !!}</textarea>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter the actual assignment here - questions, problems, tasks, essay prompts, etc. that
                                    students need to complete.
                                    Use the Heading 2/3 buttons to label each question so students get an outline just like
                                    quizzes.
                                </div>
                            </div>

                            <!-- Class and Subject -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach (\App\Models\SchoolClass::orderBy('name')->get() as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach (\App\Models\Subject::orderBy('name')->get() as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Due Date and Max Score -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">Due Date <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                        class="form-control @error('due_date') is-invalid @enderror" id="due_date"
                                        name="due_date" value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Students must submit before this date and time.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="max_score" class="form-label">Maximum Score <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_score') is-invalid @enderror"
                                        id="max_score" name="max_score" value="{{ old('max_score', 100) }}" min="0"
                                        step="0.01" required>
                                    @error('max_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Total points possible for this assignment.</div>
                                </div>
                            </div>

                            <!-- Submission Type -->
                            <div class="mb-3">
                                <label for="submission_type" class="form-label">Submission Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('submission_type') is-invalid @enderror"
                                    id="submission_type" name="submission_type" required>
                                    <option value="">Select Type</option>
                                    <option value="file" {{ old('submission_type') == 'file' ? 'selected' : '' }}>
                                        File Upload Only
                                    </option>
                                    <option value="text" {{ old('submission_type') == 'text' ? 'selected' : '' }}>
                                        Text Answer Only
                                    </option>
                                    <option value="both"
                                        {{ old('submission_type', 'both') == 'both' ? 'selected' : '' }}>
                                        Both File and Text
                                    </option>
                                </select>
                                @error('submission_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">How students will submit their work.</div>
                            </div>

                            <!-- Late Submission Settings -->
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Late Submission Settings
                                    </h6>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="allow_late_submission"
                                            name="allow_late_submission" value="1"
                                            {{ old('allow_late_submission', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_late_submission">
                                            Allow late submissions after due date
                                        </label>
                                    </div>

                                    <div id="penalty-section">
                                        <label for="late_penalty_percent" class="form-label">Late Penalty (%)</label>
                                        <input type="number"
                                            class="form-control @error('late_penalty_percent') is-invalid @enderror"
                                            id="late_penalty_percent" name="late_penalty_percent"
                                            value="{{ old('late_penalty_percent', 10) }}" min="0" max="100"
                                            step="1">
                                        @error('late_penalty_percent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Percentage deducted from the score for late submissions (0-100).
                                            <br>Example: 10% penalty on a score of 80/100 = 72/100
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tenant.teacher.classroom.exercises.index') }}"
                                    class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create Assignment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Tips Card -->
                <div class="card border-0 shadow-sm bg-light mb-4">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips for Creating Assignments
                        </h6>
                        <ul class="small mb-0 ps-3">
                            <li class="mb-2"><strong>Clear Title:</strong> Use descriptive titles (e.g., "Math Chapter 5
                                Problems")</li>
                            <li class="mb-2"><strong>Instructions:</strong> Be specific about what students need to do
                            </li>
                            <li class="mb-2"><strong>Due Date:</strong> Give students enough time to complete the work
                            </li>
                            <li class="mb-2"><strong>Submission Type:</strong> Choose based on assignment requirements
                            </li>
                            <li class="mb-2"><strong>Late Penalty:</strong> 10-20% is common practice</li>
                            <li class="mb-0"><strong>Max Score:</strong> Align with your grading rubric</li>
                        </ul>
                    </div>
                </div>

                <!-- Examples Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-bookmark me-2 text-info"></i>Example Assignments</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="small">Essay Assignment:</strong>
                            <ul class="small mb-0 ps-3">
                                <li>Type: Both (file + text)</li>
                                <li>Max Score: 100</li>
                                <li>Late Penalty: 15%</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <strong class="small">Problem Set:</strong>
                            <ul class="small mb-0 ps-3">
                                <li>Type: File upload</li>
                                <li>Max Score: 50</li>
                                <li>Late Penalty: 10%</li>
                            </ul>
                        </div>
                        <div class="mb-0">
                            <strong class="small">Short Answer:</strong>
                            <ul class="small mb-0 ps-3">
                                <li>Type: Text only</li>
                                <li>Max Score: 20</li>
                                <li>Late Penalty: 5%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const allowLateCheckbox = document.getElementById('allow_late_submission');
                const penaltySection = document.getElementById('penalty-section');

                if (allowLateCheckbox && penaltySection) {
                    allowLateCheckbox.addEventListener('change', function() {
                        penaltySection.classList.toggle('d-none', !this.checked);
                    });
                }

                const dueDateInput = document.getElementById('due_date');
                if (dueDateInput) {
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    dueDateInput.min = now.toISOString().slice(0, 16);
                }
            });
        </script>
    @endpush
@endsection
