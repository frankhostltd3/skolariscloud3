@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Create Online Exam')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-clipboard-check me-2 text-primary"></i>Create Online Exam
            </h1>
            <p class="text-muted mb-0">Set up a comprehensive online examination</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Exams
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.teacher.classroom.exams.store') }}" method="POST" id="examForm">
        @csrf

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Exam Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Exam Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="e.g., Mid-Term Exam - Mathematics" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of the exam">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions for Students</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                      id="instructions" name="instructions" rows="4" 
                                      placeholder="Exam rules and instructions">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Class and Subject -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @isset($classes)
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" 
                                        id="subject_id" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @isset($subjects)
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i>Exam Schedule
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="starts_at" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                       id="starts_at" name="starts_at" value="{{ old('starts_at') }}" required>
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="ends_at" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" 
                                       id="ends_at" name="ends_at" value="{{ old('ends_at') }}" required>
                                @error('ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="duration_minutes" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" 
                                       min="1" required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grading -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-award me-2"></i>Grading Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_marks') is-invalid @enderror" 
                                       id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}" 
                                       min="1" required>
                                @error('total_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="pass_marks" class="form-label">Pass Marks</label>
                                <input type="number" class="form-control @error('pass_marks') is-invalid @enderror" 
                                       id="pass_marks" name="pass_marks" value="{{ old('pass_marks') }}" 
                                       min="1" placeholder="50">
                                @error('pass_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="grading_method" class="form-label">Grading Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('grading_method') is-invalid @enderror" 
                                        id="grading_method" name="grading_method" required>
                                    <option value="auto" {{ old('grading_method', 'auto') == 'auto' ? 'selected' : '' }}>
                                        Auto Grading
                                    </option>
                                    <option value="manual" {{ old('grading_method') == 'manual' ? 'selected' : '' }}>
                                        Manual Grading
                                    </option>
                                    <option value="mixed" {{ old('grading_method') == 'mixed' ? 'selected' : '' }}>
                                        Mixed (Auto + Manual)
                                    </option>
                                </select>
                                @error('grading_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Exam Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="shuffle_questions" 
                                           name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shuffle_questions">
                                        Shuffle Questions
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="shuffle_options" 
                                           name="shuffle_options" value="1" {{ old('shuffle_options') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shuffle_options">
                                        Shuffle Answer Options
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_backtrack" 
                                           name="allow_backtrack" value="1" {{ old('allow_backtrack', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_backtrack">
                                        Allow Backtracking
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_results_immediately" 
                                           name="show_results_immediately" value="1" {{ old('show_results_immediately') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_results_immediately">
                                        Show Results Immediately
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="disable_copy_paste" 
                                           name="disable_copy_paste" value="1" {{ old('disable_copy_paste', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disable_copy_paste">
                                        Disable Copy/Paste
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="proctored" 
                                           name="proctored" value="1" {{ old('proctored') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="proctored">
                                        Enable Proctoring
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label for="max_tab_switches" class="form-label">Max Tab Switches Allowed</label>
                                <input type="number" class="form-control @error('max_tab_switches') is-invalid @enderror" 
                                       id="max_tab_switches" name="max_tab_switches" value="{{ old('max_tab_switches', 5) }}" 
                                       min="0" max="20">
                                <small class="text-muted">Set to 0 for unlimited</small>
                                @error('max_tab_switches')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="auto_submit_on" class="form-label">Auto Submit <span class="text-danger">*</span></label>
                                <select class="form-select @error('auto_submit_on') is-invalid @enderror" 
                                        id="auto_submit_on" name="auto_submit_on" required>
                                    <option value="time_up" {{ old('auto_submit_on') == 'time_up' ? 'selected' : '' }}>
                                        When Time is Up
                                    </option>
                                    <option value="manual" {{ old('auto_submit_on') == 'manual' ? 'selected' : '' }}>
                                        Manual Only
                                    </option>
                                    <option value="both" {{ old('auto_submit_on', 'both') == 'both' ? 'selected' : '' }}>
                                        Both
                                    </option>
                                </select>
                                @error('auto_submit_on')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <i class="bi bi-toggle-on me-2"></i>Exam Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>
                                    📝 Draft
                                </option>
                                <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>
                                    📅 Scheduled
                                </option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                    ✅ Active
                                </option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                    ✔️ Completed
                                </option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>
                                    📦 Archived
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Draft</strong>: Save for later editing<br>
                                <strong>Scheduled</strong>: Set to activate automatically<br>
                                <strong>Active</strong>: Students can take exam now
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-lightbulb me-2 text-warning"></i>Exam Tips
                        </h6>
                        <ul class="small mb-0">
                            <li>Create exam first, then add questions</li>
                            <li>Enable proctoring for high-stakes exams</li>
                            <li>Limit tab switches to prevent cheating</li>
                            <li>Test the exam before publishing</li>
                            <li>Review all settings carefully</li>
                            <li>Set clear instructions for students</li>
                        </ul>
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
                            <a href="{{ route('tenant.teacher.classroom.exams.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-file-earmark me-2"></i>Save as Draft
                                </button>
                                <button type="submit" name="action" value="schedule" class="btn btn-primary">
                                    <i class="bi bi-calendar-check me-2"></i>Save & Schedule
                                </button>
                            </div>
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
    // Form submission handling
    document.getElementById('examForm').addEventListener('submit', function(e) {
        const submitBtn = e.submitter;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }
    });

    // Validate dates
    document.getElementById('ends_at')?.addEventListener('change', function() {
        const starts = document.getElementById('starts_at').value;
        const ends = this.value;
        
        if (starts && ends && ends <= starts) {
            alert('End date must be after start date');
            this.value = '';
        }
    });

    // Auto-update status based on action
    document.querySelectorAll('button[name="action"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const statusSelect = document.getElementById('status');
            if (this.value === 'save_draft') {
                statusSelect.value = 'draft';
            } else if (this.value === 'schedule') {
                statusSelect.value = 'scheduled';
            }
        });
    });

    // Warning for proctoring
    document.getElementById('proctored')?.addEventListener('change', function() {
        if (this.checked) {
            alert('Proctoring requires students to allow camera and screen sharing permissions.');
        }
    });
</script>
@endpush

