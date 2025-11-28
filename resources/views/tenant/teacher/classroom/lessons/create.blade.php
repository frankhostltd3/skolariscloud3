@extends('layouts.dashboard-teacher')

@section('title', 'Create Lesson Plan')

@section('content')
    @php($statusOptions = App\Models\LessonPlan::statusOptions())
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Create New Lesson Plan
                </h1>
                <p class="text-muted mb-0">Plan your teaching activities and learning objectives</p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.lessons.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Lesson Plans
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

        <form action="{{ route('tenant.teacher.classroom.lessons.store') }}" method="POST" id="lessonPlanForm">
            @csrf

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
                                <label for="title" class="form-label">Lesson Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}"
                                    placeholder="e.g., Introduction to Photosynthesis" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Class, Subject, and Date -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
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

                                <div class="col-md-4 mb-3">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
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

                                <div class="col-md-4 mb-3">
                                    <label for="lesson_date" class="form-label">Lesson Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('lesson_date') is-invalid @enderror"
                                        id="lesson_date" name="lesson_date" value="{{ old('lesson_date') }}" required>
                                    @error('lesson_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time" value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time" value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number" min="5" max="600"
                                        class="form-control @error('duration_minutes') is-invalid @enderror"
                                        id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}"
                                        placeholder="Automatically calculated if times set">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Learning Objectives -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bullseye me-2"></i>Learning Objectives
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">What should students know or be able to do by the end of this
                                lesson?</p>
                            <div id="objectives-container">
                                @if (old('objectives'))
                                    @foreach (old('objectives') as $index => $objective)
                                        <div class="objective-item mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $index + 1 }}.</span>
                                                <input type="text" class="form-control" name="objectives[]"
                                                    value="{{ $objective }}" placeholder="Enter learning objective">
                                                <button type="button" class="btn btn-outline-danger remove-objective"
                                                    title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="objective-item mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">1.</span>
                                            <input type="text" class="form-control" name="objectives[]"
                                                placeholder="Enter learning objective">
                                            <button type="button" class="btn btn-outline-danger remove-objective"
                                                title="Remove">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-objective">
                                <i class="bi bi-plus-circle me-1"></i>Add Objective
                            </button>
                        </div>
                    </div>

                    <!-- Materials Needed -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-box-seam me-2"></i>Materials Needed
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">List all resources and materials required for this lesson</p>
                            <div id="materials-container">
                                @if (old('materials_needed'))
                                    @foreach (old('materials_needed') as $index => $material)
                                        <div class="material-item mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-check2"></i></span>
                                                <input type="text" class="form-control" name="materials_needed[]"
                                                    value="{{ $material }}"
                                                    placeholder="e.g., Textbook, Whiteboard, Projector">
                                                <button type="button" class="btn btn-outline-danger remove-material"
                                                    title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="material-item mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-check2"></i></span>
                                            <input type="text" class="form-control" name="materials_needed[]"
                                                placeholder="e.g., Textbook, Whiteboard, Projector">
                                            <button type="button" class="btn btn-outline-danger remove-material"
                                                title="Remove">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-material">
                                <i class="bi bi-plus-circle me-1"></i>Add Material
                            </button>
                        </div>
                    </div>

                    <!-- Lesson Content -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-journal-text me-2"></i>Lesson Content
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Introduction -->
                            <div class="mb-4">
                                <label for="introduction" class="form-label">
                                    <strong>Introduction / Warm-up</strong>
                                    <small class="text-muted ms-2">(5-10 minutes)</small>
                                </label>
                                <textarea class="form-control wysiwyg-editor @error('introduction') is-invalid @enderror" id="introduction"
                                    name="introduction" rows="3" placeholder="How will you engage students and introduce the topic?">{{ old('introduction') }}</textarea>
                                @error('introduction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Main Content -->
                            <div class="mb-4">
                                <label for="main_content" class="form-label">
                                    <strong>Main Content / Instruction</strong>
                                    <small class="text-muted ms-2">(30-40 minutes)</small>
                                </label>
                                <textarea class="form-control wysiwyg-editor @error('main_content') is-invalid @enderror" id="main_content"
                                    name="main_content" rows="6" placeholder="Describe the main teaching activities and content delivery">{{ old('main_content') }}</textarea>
                                @error('main_content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Activities -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <strong>Student Activities / Practice</strong>
                                </label>
                                <p class="text-muted small mb-3">What will students do to practice or apply what they've
                                    learned?</p>
                                <div id="activities-container">
                                    @if (old('activities'))
                                        @foreach (old('activities') as $index => $activity)
                                            <div class="activity-item mb-2">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $index + 1 }}.</span>
                                                    <input type="text" class="form-control" name="activities[]"
                                                        value="{{ $activity }}"
                                                        placeholder="Describe student activity">
                                                    <button type="button" class="btn btn-outline-danger remove-activity"
                                                        title="Remove">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="activity-item mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text">1.</span>
                                                <input type="text" class="form-control" name="activities[]"
                                                    placeholder="Describe student activity">
                                                <button type="button" class="btn btn-outline-danger remove-activity"
                                                    title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-activity">
                                    <i class="bi bi-plus-circle me-1"></i>Add Activity
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment & Homework -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clipboard-check me-2"></i>Assessment & Follow-up
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Assessment -->
                            <div class="mb-3">
                                <label for="assessment" class="form-label">
                                    <strong>Assessment / Evaluation</strong>
                                </label>
                                <textarea class="form-control wysiwyg-editor @error('assessment') is-invalid @enderror" id="assessment"
                                    name="assessment" rows="3" placeholder="How will you check student understanding?">{{ old('assessment') }}</textarea>
                                @error('assessment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Homework -->
                            <div class="mb-3">
                                <label for="homework" class="form-label">
                                    <strong>Homework / Extension</strong>
                                </label>
                                <textarea class="form-control wysiwyg-editor @error('homework') is-invalid @enderror" id="homework" name="homework"
                                    rows="3" placeholder="What should students do to reinforce or extend their learning?">{{ old('homework') }}</textarea>
                                @error('homework')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-sticky me-2"></i>Additional Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control wysiwyg-editor @error('notes') is-invalid @enderror" id="notes" name="notes"
                                rows="4" placeholder="Any additional notes, differentiation strategies, or reflection points">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Status & Options -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-toggle-on me-2"></i>Status & Options
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status', 'draft') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Save as Template -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_template" name="is_template"
                                    value="1" {{ old('is_template') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_template">
                                    <i class="bi bi-bookmark me-1"></i>Save as Template
                                </label>
                                <small class="d-block text-muted mt-1">Reuse this lesson plan structure</small>
                            </div>

                            <div class="alert alert-info mb-0">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Draft:</strong> Work in progress<br>
                                    <strong>Scheduled:</strong> Ready for review/approval<br>
                                    <strong>In Progress:</strong> Being delivered<br>
                                    <strong>Completed:</strong> Delivered and marked done
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Guide -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-lightbulb me-2 text-warning"></i>Lesson Planning Tips
                            </h6>
                            <ul class="small mb-0">
                                <li>Start with clear, measurable objectives</li>
                                <li>Engage students in the first 5 minutes</li>
                                <li>Include varied activities for different learning styles</li>
                                <li>Plan for formative assessment throughout</li>
                                <li>Allow time for questions and clarification</li>
                                <li>Link to previous and future lessons</li>
                                <li>Save successful plans as templates</li>
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
                                <a href="{{ route('tenant.teacher.classroom.lessons.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <div>
                                    <button type="submit" name="action" value="save_draft"
                                        class="btn btn-outline-primary me-2">
                                        <i class="bi bi-file-earmark me-2"></i>Save Draft
                                    </button>
                                    <button type="submit" name="action" value="submit" class="btn btn-primary">
                                        <i class="bi bi-send-check me-2"></i>Submit for Review
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
    <!-- jQuery (Required for Summernote) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Summernote editors
            $('.wysiwyg-editor').each(function() {
                const rows = parseInt(this.getAttribute('rows'), 10);
                const computedHeight = rows ? Math.max(Math.min(rows * 40, 400), 150) : 200;

                $(this).summernote({
                    placeholder: this.getAttribute('placeholder') || 'Enter content here...',
                    tabsize: 2,
                    height: computedHeight,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function() {
                            alert('Please use "Insert Link" to add images from a URL.');
                        }
                    }
                });
            });

            // Dynamic field management functions
            function setupDynamicFields(containerId, itemClass, addButtonId, fieldName) {
                const container = document.getElementById(containerId);
                const addButton = document.getElementById(addButtonId);

                // Remove button functionality
                container.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-' + itemClass.replace('-item', ''))) {
                        const item = e.target.closest('.' + itemClass);
                        if (container.querySelectorAll('.' + itemClass).length > 1) {
                            item.remove();
                            updateNumbering(container, itemClass);
                        } else {
                            alert('At least one ' + fieldName + ' is required');
                        }
                    }
                });

                // Add button functionality
                addButton.addEventListener('click', function() {
                    const items = container.querySelectorAll('.' + itemClass);
                    const newIndex = items.length + 1;
                    const template = items[0].cloneNode(true);

                    // Clear input value
                    const input = template.querySelector('input, textarea');
                    input.value = '';

                    // Update numbering if applicable
                    const numberSpan = template.querySelector('.input-group-text');
                    if (numberSpan && !numberSpan.querySelector('i')) {
                        numberSpan.textContent = newIndex + '.';
                    }

                    container.appendChild(template);
                });
            }

            function updateNumbering(container, itemClass) {
                const items = container.querySelectorAll('.' + itemClass);
                items.forEach((item, index) => {
                    const numberSpan = item.querySelector('.input-group-text');
                    if (numberSpan && !numberSpan.querySelector('i')) {
                        numberSpan.textContent = (index + 1) + '.';
                    }
                });
            }

            // Setup dynamic fields
            setupDynamicFields('objectives-container', 'objective-item', 'add-objective', 'objective');
            setupDynamicFields('materials-container', 'material-item', 'add-material', 'material');
            setupDynamicFields('activities-container', 'activity-item', 'add-activity', 'activity');

            // Form submission handling
            document.getElementById('lessonPlanForm').addEventListener('submit', function(e) {
                const submitBtn = e.submitter;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                }
            });

            // Auto-update status based on action
            document.querySelectorAll('button[name="action"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const statusSelect = document.getElementById('status');
                    if (this.value === 'save_draft') {
                        statusSelect.value = 'draft';
                    } else if (this.value === 'submit') {
                        statusSelect.value = 'scheduled';
                    }
                });
            });

            // Character counter for textareas (optional enhancement)
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    // You can add character counters here if needed
                });
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <style>
        .objective-item,
        .material-item,
        .activity-item {
            transition: all 0.3s ease;
        }

        .objective-item:hover,
        .material-item:hover,
        .activity-item:hover {
            background-color: #f8f9fa;
            padding: 0.25rem;
            border-radius: 0.25rem;
        }

        .remove-objective,
        .remove-material,
        .remove-activity {
            transition: all 0.2s ease;
        }

        .remove-objective:hover,
        .remove-material:hover,
        .remove-activity:hover {
            transform: scale(1.1);
        }
    </style>
@endpush
