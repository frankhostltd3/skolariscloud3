@extends('layouts.tenant.student')

@section('title', $exercise->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('tenant.student.classroom.exercises.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Assignments
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Assignment Details -->
        <div class="col-lg-8">
            <!-- Main Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="flex-grow-1">
                            <h2 class="mb-2">{{ $exercise->title }}</h2>
                            <div class="d-flex gap-3 flex-wrap text-muted small">
                                <span>
                                    <i class="bi bi-book me-1"></i>{{ $exercise->subject->name }}
                                </span>
                                <span>
                                    <i class="bi bi-diagram-3 me-1"></i>{{ $exercise->class->name }}
                                </span>
                                <span>
                                    <i class="bi bi-person me-1"></i>{{ $exercise->teacher->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Alert -->
                    @if($submission)
                        @if($submission->grade !== null)
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Assignment Graded</h5>
                                    <p class="mb-0">Your submission has been graded by {{ $exercise->teacher->name }}</p>
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0">{{ $submission->grade }}%</h3>
                                    <small>{{ $submission->score }}/{{ $exercise->max_score }}</small>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-hourglass-split me-3 fs-4"></i>
                                <div>
                                    <h5 class="mb-1">Submission Under Review</h5>
                                    <p class="mb-0">Submitted {{ $submission->submitted_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endif
                    @else
                        @php
                            $now = \Carbon\Carbon::now();
                            $isOverdue = $now->isAfter($exercise->due_date);
                            $daysUntilDue = $now->diffInDays($exercise->due_date, false);
                        @endphp
                        @if($isOverdue)
                            @if($exercise->allow_late_submission)
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-1">Assignment Overdue</h5>
                                        <p class="mb-0">
                                            Due date passed {{ $exercise->due_date->diffForHumans() }}.
                                            @if($exercise->late_penalty_percent)
                                                Late submissions accepted with {{ $exercise->late_penalty_percent }}% penalty per day.
                                            @else
                                                Late submissions accepted without penalty.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="bi bi-x-circle-fill me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-1">Submission Closed</h5>
                                        <p class="mb-0">This assignment is overdue and late submissions are not accepted.</p>
                                    </div>
                                </div>
                            @endif
                        @elseif($daysUntilDue <= 2)
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="bi bi-clock-fill me-3 fs-4"></i>
                                <div>
                                    <h5 class="mb-1">Due Soon!</h5>
                                    <p class="mb-0">This assignment is due in {{ $daysUntilDue }} day{{ $daysUntilDue !== 1 ? 's' : '' }}.</p>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Description -->
                    @if($exercise->description)
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Description</h5>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($exercise->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Instructions -->
                    @if($exercise->instructions)
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="bi bi-list-check me-2"></i>Instructions</h5>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($exercise->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Attachment -->
                    @if($exercise->attachment_path)
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="bi bi-paperclip me-2"></i>Attachment</h5>
                            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded">
                                <i class="bi bi-file-earmark text-primary fs-3"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold">{{ basename($exercise->attachment_path) }}</p>
                                    <small class="text-muted">{{ $exercise->attachment_size }}</small>
                                </div>
                                <a href="{{ route('tenant.student.classroom.exercises.download', $exercise) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submission Section -->
            @if($submission)
                <!-- Existing Submission -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="bi bi-file-check me-2"></i>Your Submission</h5>
                    </div>
                    <div class="card-body">
                        <!-- Submission Text -->
                        @if($submission->submission_text)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Submission Text:</h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($submission->submission_text)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Submission File -->
                        @if($submission->file_path)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Attached File:</h6>
                                <div class="d-flex align-items-center gap-3 bg-light p-3 rounded">
                                    <i class="bi bi-file-earmark-arrow-up text-success fs-3"></i>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-bold">{{ basename($submission->file_path) }}</p>
                                        <small class="text-muted">{{ $submission->file_size }}</small>
                                    </div>
                                    <a href="{{ route('tenant.student.classroom.exercises.submission.download', [$exercise, $submission]) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Grade & Feedback -->
                        @if($submission->grade !== null)
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-success bg-opacity-10 p-3 rounded text-center">
                                        <p class="text-muted mb-1 small">Your Score</p>
                                        <h3 class="mb-0 text-success">{{ $submission->score }}/{{ $exercise->max_score }}</h3>
                                        <p class="mb-0 small">{{ $submission->grade }}%</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded text-center">
                                        <p class="text-muted mb-1 small">Graded On</p>
                                        <h5 class="mb-0 text-primary">{{ $submission->graded_at->format('M d, Y') }}</h5>
                                        <p class="mb-0 small">{{ $submission->graded_at->format('h:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($submission->feedback)
                                <div class="mt-4">
                                    <h6 class="text-muted mb-2">Teacher Feedback:</h6>
                                    <div class="alert alert-light border">
                                        {!! nl2br(e($submission->feedback)) !!}
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Submission Meta -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Submitted</small>
                                    <strong>{{ $submission->submitted_at->format('M d, Y h:i A') }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Status</small>
                                    @if($submission->is_late)
                                        <span class="badge bg-warning">Late Submission</span>
                                    @else
                                        <span class="badge bg-success">On Time</span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Grading Status</small>
                                    @if($submission->grade !== null)
                                        <span class="badge bg-success">Graded</span>
                                    @else
                                        <span class="badge bg-info">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Submission Form -->
                @if(\Carbon\Carbon::now()->isBefore($exercise->due_date) || $exercise->allow_late_submission)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Submit Assignment</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('tenant.student.classroom.exercises.submit', $exercise) }}" 
                                  method="POST" enctype="multipart/form-data" id="submissionForm">
                                @csrf

                                <!-- Submission Text -->
                                <div class="mb-4">
                                    <label for="submission_text" class="form-label">
                                        Your Answer <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="submission_text" id="submission_text" 
                                              class="form-control @error('submission_text') is-invalid @enderror" 
                                              rows="8" required>{{ old('submission_text') }}</textarea>
                                    @error('submission_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Write your answer or explanation here. Be clear and detailed.
                                    </small>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-4">
                                    <label for="submission_file" class="form-label">
                                        Attach File (Optional)
                                    </label>
                                    <input type="file" name="submission_file" id="submission_file" 
                                           class="form-control @error('submission_file') is-invalid @enderror">
                                    @error('submission_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Max file size: 50MB. Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, Images
                                    </small>
                                </div>

                                <!-- Submission Confirmation -->
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Important:</strong> Once you submit, you cannot edit or resubmit this assignment.
                                    Please review your work before submitting.
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i> Submit Assignment
                                    </button>
                                    <a href="{{ route('tenant.student.classroom.exercises.index') }}" 
                                       class="btn btn-outline-secondary btn-lg">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Right Column: Summary -->
        <div class="col-lg-4">
            <!-- Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Assignment Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Due Date</small>
                        <strong>{{ $exercise->due_date->format('M d, Y') }}</strong>
                        <div class="text-muted small">{{ $exercise->due_date->format('h:i A') }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Maximum Score</small>
                        <strong class="fs-4">{{ $exercise->max_score }}</strong>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Time Remaining</small>
                        @php
                            $now = \Carbon\Carbon::now();
                            $isOverdue = $now->isAfter($exercise->due_date);
                        @endphp
                        @if($isOverdue)
                            <span class="badge bg-danger">
                                Overdue by {{ $now->diffInDays($exercise->due_date) }} days
                            </span>
                        @else
                            <strong class="text-success">
                                {{ $now->diffInDays($exercise->due_date) }} days
                            </strong>
                        @endif
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Late Submission</small>
                        @if($exercise->allow_late_submission)
                            <span class="badge bg-success">Allowed</span>
                            @if($exercise->late_penalty_percent)
                                <div class="mt-1 small text-muted">
                                    Penalty: {{ $exercise->late_penalty_percent }}% per day
                                </div>
                            @endif
                        @else
                            <span class="badge bg-danger">Not Allowed</span>
                        @endif
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Your Status</small>
                        @if($submission)
                            @if($submission->grade !== null)
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Graded
                                </span>
                            @else
                                <span class="badge bg-info px-3 py-2">
                                    <i class="bi bi-hourglass-split me-1"></i> Submitted
                                </span>
                            @endif
                        @else
                            <span class="badge bg-warning px-3 py-2">
                                <i class="bi bi-circle me-1"></i> Not Submitted
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Teacher Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Teacher</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-person-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <strong class="d-block">{{ $exercise->teacher->name }}</strong>
                            <small class="text-muted">{{ $exercise->subject->name }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
});
</script>
@endpush
@endsection
