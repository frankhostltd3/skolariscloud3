@extends('layouts.tenant.student')



@section('title', $assignment->title)@section('title', $assignment->title)

@section('page-title', 'Assignment')

@section('content')

    <div class="container-fluid">
    @section('content')
        <!-- Breadcrumb -->
        <div class="container-fluid">

            <nav aria-label="breadcrumb" class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <ol class="breadcrumb">
                        <h4 class="mb-0">{{ $assignment->title }}</h4>

                        <li class="breadcrumb-item"><a
                                href="{{ route('tenant.student.assignments.index') }}">{{ __('Assignments') }}</a></li> <a
                            href="{{ route('tenant.student.assignments.index') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-2"></i>Back</a>

                        <li class="breadcrumb-item active">{{ $assignment->title }}</li>
                </div>

                </ol>

            </nav>
            <div class="row g-3">

                <div class="col-md-8">

                    @if (session('success'))
                        <div class="card border-0 shadow-sm h-100">

                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="card-body">

                                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }} <div
                                        class="mb-2 text-muted">Class:
                                        <strong>{{ $assignment->class->name ?? '—' }}</strong> • Subject:
                                        <strong>{{ $assignment->subject->name ?? '—' }}</strong>
                                    </div>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    <div class="mb-2">Teacher: <strong>{{ $assignment->teacher->name ?? '—' }}</strong>
                                    </div>

                                </div>
                                <div class="mb-2">Due:
                                    <strong>{{ optional($assignment->due_at)->format('Y-m-d H:i') ?? '—' }}</strong>
                                    @endif @if ($assignment->due_at && now()->greaterThan($assignment->due_at))

                                        <span class="badge bg-danger ms-2">Past due</span>

                                        @if (session('error'))
                                        @elseif($assignment->due_at && $assignment->due_at->isBetween(now(), now()->copy()->addHours(48)))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <span class="badge bg-warning text-dark ms-2">Due soon</span>

                                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                        @endif

                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>

                            </div>
                            @if ($assignment->attachment_path)
                            @endif
                            <div class="mb-2">

                                <a href="{{ Storage::disk('public')->url($assignment->attachment_path) }}"
                                    target="_blank"><i class="fas fa-paperclip me-2"></i>Download Attachment</a>

                                <div class="row"> </div>

                                <!-- Main Content -->
                    @endif

                    <div class="col-lg-8 mb-4">
                        <h6 class="mt-3">Instructions</h6>

                        <!-- Assignment Details -->
                        @if ($assignment->instructions)
                            <div class="mb-0">{!! $assignment->instructions !!}</div>
                        @else
                            <p class="text-muted mb-0">{{ __('No specific instructions provided.') }}</p>
                        @endif

                        <div class="card shadow-sm mb-4"> </div>

                        <div class="card-header bg-primary text-white"> </div>

                        <h5 class="mb-0">
                    </div>

                    <i class="bi bi-file-earmark-text me-2"></i>{{ $assignment->title }} <div class="col-md-4">

                        </h5>
                        <div class="card border-0 shadow-sm h-100">

                        </div>
                        <div class="card-header bg-white"><strong>Your Submission</strong></div>

                        <div class="card-body">
                            <div class="card-body">

                                <!-- Meta Information -->
                                @if (isset($submission))

                                    <div class="row mb-3">
                                        <div class="mb-2 small text-muted">Submitted:
                                            {{ optional($submission->submitted_at)->format('Y-m-d H:i') ?? '—' }}</div>

                                        <div class="col-md-6">
                                            @if ($submission->attachment_path)
                                                <p class="mb-2">
                                                <div class="mb-2"><a class="btn btn-sm btn-outline-primary"
                                                        href="{{ Storage::disk('public')->url($submission->attachment_path) }}"
                                                        target="_blank"><i class="fas fa-download me-1"></i> Download</a>
                                                </div>

                                                <i class="bi bi-book text-primary me-2"></i>
                                            @endif

                                            <strong>{{ __('Subject:') }}</strong>
                                            {{ $assignment->subject->name ?? 'N/A' }} @if (!is_null($submission->score))
                                                </p>
                                                <div class="mb-2">Score: <strong>{{ $submission->score }}</strong></div>

                                                <p class="mb-2">
                                            @endif

                                            <i class="bi bi-person text-primary me-2"></i>
                                            @if ($submission->feedback)
                                                <strong>{{ __('Teacher:') }}</strong>
                                                {{ $assignment->teacher->name ?? 'N/A' }} <div class="small text-muted">
                                                    Feedback: {{ $submission->feedback }}</div>

                                                </p>
                                            @endif

                                            <p class="mb-2">
                                                @if (is_null($submission->graded_at))
                                                    <i class="bi bi-people text-primary me-2"></i>
                                                    <div class="alert alert-info mt-3 py-2">You can resubmit before grading.
                                                    </div>

                                                    <strong>{{ __('Class:') }}</strong>
                                                    {{ $assignment->class->name ?? 'N/A' }}
                                                @endif

                                            </p>
                                        @else
                                        </div>
                                        <div class="text-muted mb-2">No submission yet.</div>

                                        <div class="col-md-6">
                                @endif

                                <p class="mb-2">

                                    <i class="bi bi-calendar3 text-primary me-2"></i> @php $canResubmit = $assignment->allow_resubmission && (!isset($submission) || is_null($submission->graded_at)); @endphp

                                    <strong>{{ __('Due Date:') }}</strong>
                                    @if ($canResubmit)

                                        @if ($assignment->due_date)
                                            <form method="POST"
                                                action="{{ route('tenant.student.assignments.submit', $assignment) }}"
                                                enctype="multipart/form-data" class="mt-2">

                                                {{ $assignment->due_date->format('M d, Y g:i A') }} @csrf

                                                <br>
                                                <div class="mb-2">

                                                    @if ($daysRemaining !== null && $daysRemaining >= 0)
                                                        <label class="form-label">Notes (optional)</label>

                                                        <span
                                                            class="badge bg-{{ $daysRemaining <= 2 ? 'warning' : 'info' }} ms-4">
                                                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $submission->notes ?? '') }}</textarea>

                                                            {{ $daysRemaining }} {{ __('days left') }}
                                                </div>

                                                </span>
                                                <div class="mb-2">
                                                @elseif($daysOverdue !== null)
                                                    <label class="form-label">Attachment (optional)</label>

                                                    <span class="badge bg-danger ms-4"> <input type="file"
                                                            name="attachment" class="form-control" />

                                                        {{ __('Overdue by') }} {{ abs($daysOverdue) }}
                                                        {{ __('days') }}
                                                </div>

                                                </span> <button class="btn btn-student w-100"><i
                                                        class="fas fa-upload me-2"></i>{{ isset($submission) ? 'Resubmit' : 'Submit' }}</button>
                                        @endif
                                        </form>
                                    @else

                                    @else
                                        {{ __('No deadline') }} <div class="alert alert-secondary mt-2">Resubmissions are
                                            disabled for this assignment.</div>

                                    @endif
                                    @endif

                                </p>
                            </div>

                            <p class="mb-2">
                        </div>

                        <i class="bi bi-star text-primary me-2"></i>
                    </div>

                    <strong>{{ __('Max Marks:') }}</strong> {{ $assignment->max_marks }}
                </div>

                </p>
            </div>

        <p class="mb-2">@endsection
            <i class="bi bi-arrow-repeat text-primary me-2"></i>
            <strong>{{ __('Resubmission:') }}</strong>
            <span class="badge bg-{{ $assignment->allow_resubmission ? 'success' : 'secondary' }}">
                {{ $assignment->allow_resubmission ? __('Allowed') : __('Not Allowed') }}
            </span>
        </p>
    </div>
</div>

<hr>

<!-- Description -->
<h6 class="mb-3">{{ __('Description & Instructions') }}</h6>
<div class="bg-light p-3 rounded">
    @if ($assignment->description)
        <div class="mb-0">{!! $assignment->description !!}</div>
    @else
        <p class="text-muted mb-0">{{ __('No description provided.') }}</p>
    @endif
</div>

<!-- Teacher's Attachment -->
@if ($assignment->attachment_path)
    <div class="mt-3">
        <h6 class="mb-2">{{ __('Assignment File') }}</h6>
        <a href="{{ Storage::url($assignment->attachment_path) }}" target="_blank"
            class="btn btn-outline-primary btn-sm">
            <i class="bi bi-download me-2"></i>{{ __('Download Assignment File') }}
        </a>
    </div>
@endif
</div>
</div>

<!-- Submission Section -->
@if ($submission)
    <!-- Existing Submission -->
    <div class="card shadow-sm border-{{ $submission->isGraded() ? 'success' : 'info' }}">
        <div class="card-header bg-{{ $submission->isGraded() ? 'success' : 'info' }} bg-opacity-10">
            <h5 class="mb-0">
                <i class="bi bi-{{ $submission->isGraded() ? 'check-circle' : 'clock-history' }} me-2"></i>
                {{ $submission->isGraded() ? __('Graded Submission') : __('Your Submission') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>{{ __('Submitted on:') }}</strong>
                {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y g:i A') : 'N/A' }}
                @if ($submission->isLate())
                    <span class="badge bg-warning ms-2">{{ __('Late Submission') }}</span>
                @endif
            </div>

            @if ($submission->notes)
                <div class="mb-3">
                    <h6>{{ __('Your Notes:') }}</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{!! nl2br(e($submission->notes)) !!}</p>
                    </div>
                </div>
            @endif

            @if ($submission->attachment_path)
                <div class="mb-3">
                    <h6>{{ __('Your Attachment:') }}</h6>
                    <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark me-2"></i>{{ __('View/Download Your Submission') }}
                    </a>
                </div>
            @endif

            @if ($submission->isGraded())
                <hr>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <h5 class="mb-3">
                        <i class="bi bi-star-fill text-success me-2"></i>{{ __('Grade & Feedback') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>{{ __('Marks:') }}</strong>
                                <span class="fs-4 text-success">{{ $submission->marks }}</span> /
                                {{ $assignment->max_marks }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ __('Percentage:') }}</strong>
                                <span class="fs-5 text-success">
                                    {{ number_format(($submission->marks / $assignment->max_marks) * 100, 1) }}%
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>{{ __('Graded on:') }}</strong>
                                {{ $submission->graded_at ? $submission->graded_at->format('M d, Y') : 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ __('Graded by:') }}</strong>
                                {{ $submission->gradedBy->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if ($submission->feedback)
                        <hr>
                        <h6>{{ __("Teacher's Feedback:") }}</h6>
                        <div class="bg-white p-3 rounded border">
                            <p class="mb-0">{!! nl2br(e($submission->feedback)) !!}</p>
                        </div>
                    @endif
                </div>
            @else
                <!-- Resubmission Option -->
                @if ($assignment->allow_resubmission && !$assignment->isOverdue())
                    <hr>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('You can resubmit this assignment before it is graded.') }}
                        <button type="button" class="btn btn-warning btn-sm float-end" data-bs-toggle="modal"
                            data-bs-target="#submitModal">
                            <i class="bi bi-arrow-repeat me-2"></i>{{ __('Resubmit Assignment') }}
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>
@else
    <!-- Submission Form -->
    @if (!$assignment->isOverdue() || $assignment->allow_late_submission)
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success bg-opacity-10">
                <h5 class="mb-0">
                    <i class="bi bi-upload me-2"></i>{{ __('Submit Assignment') }}
                </h5>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                    data-bs-target="#submitModal">
                    <i class="bi bi-upload me-2"></i>{{ __('Click to Submit Your Work') }}
                </button>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-danger">
            <div class="card-body text-center">
                <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-danger">{{ __('Assignment Closed') }}</h5>
                <p class="text-muted">
                    {{ __('This assignment is past the due date and no longer accepts submissions.') }}</p>
            </div>
        </div>
    @endif
@endif
</div>

<!-- Sidebar -->
<div class="col-lg-4">
    <!-- Status Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">{{ __('Assignment Status') }}</h6>
        </div>
        <div class="card-body">
            @if ($submission)
                @if ($submission->isGraded())
                    <div class="text-center mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-2 text-success">{{ __('Graded') }}</h5>
                    </div>
                @else
                    <div class="text-center mb-3">
                        <i class="bi bi-clock-history text-info" style="font-size: 3rem;"></i>
                        <h5 class="mt-2 text-info">{{ __('Awaiting Grading') }}</h5>
                    </div>
                @endif
            @elseif($assignment->isOverdue())
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-danger">{{ __('Overdue') }}</h5>
                </div>
            @else
                <div class="text-center mb-3">
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-warning">{{ __('Pending') }}</h5>
                </div>
            @endif

            <hr>

            <ul class="list-unstyled small">
                <li class="mb-2">
                    <strong>{{ __('Total Submissions:') }}</strong> {{ $totalSubmissions }}
                </li>
                <li class="mb-2">
                    <strong>{{ __('Your Status:') }}</strong>
                    @if ($submission)
                        @if ($submission->isGraded())
                            <span class="badge bg-success">{{ __('Graded') }}</span>
                        @else
                            <span class="badge bg-info">{{ __('Submitted') }}</span>
                        @endif
                    @elseif($assignment->isOverdue())
                        <span class="badge bg-danger">{{ __('Not Submitted') }}</span>
                    @else
                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                    @endif
                </li>
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
            <p class="small mb-3">{{ __('Having trouble with this assignment?') }}</p>
            <ul class="small mb-3">
                <li>{{ __('Re-read the instructions carefully') }}</li>
                <li>{{ __('Download any provided files') }}</li>
                <li>{{ __('Contact your teacher if unclear') }}</li>
            </ul>
            @if ($assignment->teacher)
                <p class="small mb-0">
                    <strong>{{ __('Teacher:') }}</strong><br>
                    {{ $assignment->teacher->name }}<br>
                    @if ($assignment->teacher->email)
                        <a href="mailto:{{ $assignment->teacher->email }}">{{ $assignment->teacher->email }}</a>
                    @endif
                </p>
            @endif
        </div>
    </div>
</div>
</div>
</div>

<!-- Submission Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('tenant.student.assignments.submit', $assignment->id) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload me-2"></i>
                        {{ $submission ? __('Resubmit Assignment') : __('Submit Assignment') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>{{ __('Before you submit:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Make sure you have completed all requirements') }}</li>
                            <li>{{ __('Check your file is in the correct format') }}</li>
                            <li>{{ __('Review your notes for any errors') }}</li>
                            @if (!$assignment->allow_resubmission && !$submission)
                                <li class="text-danger">
                                    <strong>{{ __('Note: You cannot resubmit after submitting!') }}</strong>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Notes / Comments') }}</label>
                        <textarea name="notes" class="form-control" rows="5"
                            placeholder="{{ __('Add any notes or comments about your submission...') }}">{{ $submission->notes ?? '' }}</textarea>
                        <small
                            class="text-muted">{{ __('Optional: Explain your work or add any relevant comments') }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Upload File') }}</label>
                        <input type="file" name="attachment" class="form-control"
                            accept=".pdf,.doc,.docx,.txt,.zip,.jpg,.jpeg,.png">
                        <small class="text-muted">
                            {{ __('Accepted formats: PDF, DOC, DOCX, TXT, ZIP, JPG, PNG (Max: 10MB)') }}
                        </small>
                        @if ($submission && $submission->attachment_path)
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ __('Current file:') }}
                                    <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank">
                                        {{ basename($submission->attachment_path) }}
                                    </a>
                                </small>
                            </div>
                        @endif
                    </div>

                    @if ($assignment->isOverdue() && $assignment->allow_late_submission)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ __('Note: This submission will be marked as late.') }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-2"></i>
                        {{ $submission ? __('Resubmit Assignment') : __('Submit Assignment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
