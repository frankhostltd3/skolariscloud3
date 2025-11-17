@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Edit Assignment')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-pencil me-2 text-primary"></i>Edit Assignment</h1>
            <p class="text-muted mb-0">Update assignment details</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tenant.teacher.classroom.exercises.update', $exercise) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $exercise->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $exercise->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                      id="instructions" name="instructions" rows="5" 
                                      placeholder="Provide detailed instructions for students...">{{ old('instructions', $exercise->instructions) }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">These instructions will be shown to students when they view the assignment.</div>
                        </div>

                        <!-- Class and Subject -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $exercise->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" 
                                        id="subject_id" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $exercise->subject_id) == $subject->id ? 'selected' : '' }}>
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
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" 
                                       value="{{ old('due_date', $exercise->due_date->format('Y-m-d\TH:i')) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="max_score" class="form-label">Maximum Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_score') is-invalid @enderror" 
                                       id="max_score" name="max_score" 
                                       value="{{ old('max_score', $exercise->max_score) }}" 
                                       min="0" step="0.01" required>
                                @error('max_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submission Type -->
                        <div class="mb-3">
                            <label for="submission_type" class="form-label">Submission Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('submission_type') is-invalid @enderror" 
                                    id="submission_type" name="submission_type" required>
                                <option value="">Select Type</option>
                                <option value="file" {{ old('submission_type', $exercise->submission_type) == 'file' ? 'selected' : '' }}>
                                    File Upload Only
                                </option>
                                <option value="text" {{ old('submission_type', $exercise->submission_type) == 'text' ? 'selected' : '' }}>
                                    Text Answer Only
                                </option>
                                <option value="both" {{ old('submission_type', $exercise->submission_type) == 'both' ? 'selected' : '' }}>
                                    Both File and Text
                                </option>
                            </select>
                            @error('submission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Late Submission Settings -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="mb-3">Late Submission Settings</h6>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_late_submission" 
                                           name="allow_late_submission" value="1"
                                           {{ old('allow_late_submission', $exercise->allow_late_submission) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_late_submission">
                                        Allow late submissions after due date
                                    </label>
                                </div>

                                <div id="penalty-section" class="{{ old('allow_late_submission', $exercise->allow_late_submission) ? '' : 'd-none' }}">
                                    <label for="late_penalty_percent" class="form-label">Late Penalty (%)</label>
                                    <input type="number" class="form-control @error('late_penalty_percent') is-invalid @enderror" 
                                           id="late_penalty_percent" name="late_penalty_percent" 
                                           value="{{ old('late_penalty_percent', $exercise->late_penalty_percent) }}" 
                                           min="0" max="100" step="1">
                                    @error('late_penalty_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Percentage deducted from the score for late submissions (0-100). 
                                        Example: 10% penalty means if a student scores 80/100, they get 72/100 for a late submission.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Warning if submissions exist -->
                        @if($exercise->submissions()->count() > 0)
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> This assignment has {{ $exercise->submissions()->count() }} submission(s). 
                            Changing certain settings (like max score) may affect existing grades.
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Assignment
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
                    <h6 class="mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Be clear and specific in your instructions</li>
                        <li class="mb-2">Set realistic due dates considering students' workload</li>
                        <li class="mb-2">Consider allowing late submissions with penalty for flexibility</li>
                        <li class="mb-2">Max score should reflect the complexity of the assignment</li>
                        <li class="mb-0">Review submission type based on assignment requirements</li>
                    </ul>
                </div>
            </div>

            <!-- Current Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2 text-info"></i>Current Stats</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <strong>Total Submissions:</strong> {{ $exercise->submissions()->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>Graded:</strong> {{ $exercise->submissions()->whereNotNull('score')->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>Pending:</strong> {{ $exercise->submissions()->whereNull('score')->count() }}
                        </li>
                        <li class="mb-0">
                            <strong>Created:</strong> {{ $exercise->created_at->format('M j, Y') }}
                        </li>
                    </ul>
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
    
    allowLateCheckbox.addEventListener('change', function() {
        if (this.checked) {
            penaltySection.classList.remove('d-none');
        } else {
            penaltySection.classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection

