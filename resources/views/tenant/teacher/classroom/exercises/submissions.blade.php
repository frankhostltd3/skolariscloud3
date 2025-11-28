@extends('layouts.dashboard-teacher')

@section('title', 'Assignment Submissions - ' . $exercise->title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-list-check me-2 text-primary"></i>Assignment Submissions
            </h1>
            <p class="text-muted mb-0">
                <strong>{{ $exercise->title }}</strong> - {{ $exercise->class->name }} ({{ $exercise->subject->name }})
            </p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Assignment
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Students</p>
                            <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Submitted</p>
                            <h3 class="mb-0 text-success">{{ $stats['submitted'] }}</h3>
                            <small class="text-muted">
                                {{ $stats['total_students'] > 0 ? round(($stats['submitted'] / $stats['total_students']) * 100) : 0 }}%
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pending Grading</p>
                            <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                            <small class="text-muted">{{ $stats['graded'] }} graded</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Not Submitted</p>
                            <h3 class="mb-0 text-danger">{{ $stats['total_students'] - $stats['submitted'] }}</h3>
                            <small class="text-muted">Missing</small>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-x-circle fs-4 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="btn-group" role="group" aria-label="Filter submissions">
                        <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}" 
                           class="btn btn-outline-primary {{ !request('filter') ? 'active' : '' }}">
                            All ({{ $stats['submitted'] }})
                        </a>
                        <a href="{{ route('tenant.teacher.classroom.exercises.submissions', ['exercise' => $exercise, 'filter' => 'pending']) }}" 
                           class="btn btn-outline-warning {{ request('filter') == 'pending' ? 'active' : '' }}">
                            Pending ({{ $stats['pending'] }})
                        </a>
                        <a href="{{ route('tenant.teacher.classroom.exercises.submissions', ['exercise' => $exercise, 'filter' => 'graded']) }}" 
                           class="btn btn-outline-success {{ request('filter') == 'graded' ? 'active' : '' }}">
                            Graded ({{ $stats['graded'] }})
                        </a>
                        <a href="{{ route('tenant.teacher.classroom.exercises.submissions', ['exercise' => $exercise, 'filter' => 'late']) }}" 
                           class="btn btn-outline-danger {{ request('filter') == 'late' ? 'active' : '' }}">
                            Late
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkGradeModal" 
                                {{ $stats['pending'] == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-lightning me-2"></i>Bulk Grade
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    @if($submissions->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Submissions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 5%">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th style="width: 25%">Student</th>
                            <th style="width: 15%">Submitted</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 15%">Score</th>
                            <th style="width: 20%">Feedback</th>
                            <th style="width: 10%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                        <tr id="submission-{{ $submission->id }}" class="{{ $submission->is_late ? 'table-danger bg-opacity-10' : '' }}">
                            <td>
                                <input type="checkbox" class="form-check-input submission-checkbox" 
                                       value="{{ $submission->id }}" 
                                       {{ $submission->score !== null ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($submission->student->first_name, 0, 1) }}{{ substr($submission->student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $submission->student->full_name }}</div>
                                        <small class="text-muted">{{ $submission->student->student_id ?? $submission->student->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>{{ $submission->submitted_at->format('M j, Y g:i A') }}</small>
                                @if($submission->is_late)
                                    <br><span class="badge bg-danger small">Late Submission</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->score !== null)
                                    <span class="badge bg-success">Graded</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->score !== null)
                                    <div class="d-flex align-items-center">
                                        <strong class="me-2">{{ $submission->score }}/{{ $exercise->max_score }}</strong>
                                        @php
                                            $percentage = ($submission->score / $exercise->max_score) * 100;
                                            $badgeClass = $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ round($percentage) }}%</span>
                                    </div>
                                    @if($submission->graded_at)
                                        <small class="text-muted">{{ $submission->graded_at->format('M j, Y') }}</small>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#gradeModal{{ $submission->id }}">
                                        <i class="bi bi-pencil me-1"></i>Grade
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if($submission->teacher_feedback)
                                    <small class="text-muted">{{ Str::limit($submission->teacher_feedback, 40) }}</small>
                                @else
                                    <small class="text-muted fst-italic">No feedback yet</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewModal{{ $submission->id }}"
                                            title="View Submission">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($submission->score !== null)
                                    <button type="button" class="btn btn-outline-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#gradeModal{{ $submission->id }}"
                                            title="Edit Grade">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- View Submission Modal -->
                        <div class="modal fade" id="viewModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-file-text me-2"></i>{{ $submission->student->full_name }}'s Submission
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Submitted:</strong> {{ $submission->submitted_at->format('F j, Y g:i A') }}
                                            @if($submission->is_late)
                                                <span class="badge bg-danger ms-2">Late</span>
                                            @endif
                                        </div>

                                        @if($submission->submission_text)
                                        <div class="mb-3">
                                            <strong>Text Answer:</strong>
                                            <div class="p-3 bg-light rounded mt-2">
                                                {!! nl2br(e($submission->submission_text)) !!}
                                            </div>
                                        </div>
                                        @endif

                                        @if($submission->file_path)
                                        <div class="mb-3">
                                            <strong>Attached File:</strong>
                                            <div class="mt-2">
                                                <a href="{{ route('tenant.teacher.classroom.exercises.download', $submission) }}" 
                                                   class="btn btn-outline-primary">
                                                    <i class="bi bi-download me-2"></i>{{ $submission->file_name ?? 'Download File' }}
                                                </a>
                                            </div>
                                        </div>
                                        @endif

                                        @if($submission->score !== null)
                                        <hr>
                                        <div class="mb-3">
                                            <strong>Score:</strong> {{ $submission->score }}/{{ $exercise->max_score }} 
                                            ({{ round(($submission->score / $exercise->max_score) * 100) }}%)
                                        </div>
                                        @if($submission->teacher_feedback)
                                        <div class="mb-3">
                                            <strong>Teacher Feedback:</strong>
                                            <div class="p-3 bg-light rounded mt-2">
                                                {!! nl2br(e($submission->teacher_feedback)) !!}
                                            </div>
                                        </div>
                                        @endif
                                        <div>
                                            <small class="text-muted">
                                                Graded by {{ $submission->gradedBy->full_name ?? 'Teacher' }} on 
                                                {{ $submission->graded_at->format('F j, Y g:i A') }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        @if($submission->score === null)
                                        <button type="button" class="btn btn-primary" 
                                                data-bs-dismiss="modal"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#gradeModal{{ $submission->id }}">
                                            <i class="bi bi-pencil me-2"></i>Grade Now
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Submission Modal -->
                        <div class="modal fade" id="gradeModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.teacher.classroom.exercises.grade', ['exercise' => $exercise, 'submission' => $submission]) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="bi bi-pencil-square me-2"></i>Grade Submission
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <p class="mb-2"><strong>Student:</strong> {{ $submission->student->full_name }}</p>
                                                <p class="mb-0 text-muted small">
                                                    Submitted: {{ $submission->submitted_at->format('M j, Y g:i A') }}
                                                    @if($submission->is_late)
                                                        <span class="badge bg-danger ms-2">Late</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="score{{ $submission->id }}" class="form-label">
                                                    Score <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control @error('score') is-invalid @enderror" 
                                                           id="score{{ $submission->id }}" 
                                                           name="score" 
                                                           value="{{ old('score', $submission->score) }}" 
                                                           min="0" 
                                                           max="{{ $exercise->max_score }}" 
                                                           step="0.01" 
                                                           required>
                                                    <span class="input-group-text">/ {{ $exercise->max_score }}</span>
                                                </div>
                                                @error('score')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Maximum score: {{ $exercise->max_score }} points
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="feedback{{ $submission->id }}" class="form-label">Teacher Feedback</label>
                                                <textarea class="form-control" 
                                                          id="feedback{{ $submission->id }}" 
                                                          name="feedback" 
                                                          rows="4" 
                                                          placeholder="Provide constructive feedback to the student...">{{ old('feedback', $submission->teacher_feedback) }}</textarea>
                                                <small class="form-text text-muted">
                                                    Help the student understand their strengths and areas for improvement
                                                </small>
                                            </div>

                                            @if($submission->is_late && $exercise->late_penalty_percent > 0)
                                            <div class="alert alert-warning">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                <strong>Note:</strong> This is a late submission. 
                                                A {{ $exercise->late_penalty_percent }}% penalty will be applied automatically.
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle me-2"></i>Submit Grade
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($submissions->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-4 text-muted mb-3"></i>
            <h5>No Submissions Found</h5>
            @if(request('filter'))
                <p class="text-muted">No submissions match the selected filter.</p>
                <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}" class="btn btn-primary">
                    View All Submissions
                </a>
            @else
                <p class="text-muted">Students haven't submitted their work yet.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Assignment Info Sidebar (Optional - for reference) -->
    <div class="card border-0 shadow-sm mt-4 bg-light">
        <div class="card-body">
            <h6 class="mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Assignment Details</h6>
            <ul class="list-unstyled small mb-0">
                <li class="mb-2"><strong>Due Date:</strong> {{ $exercise->due_date->format('F j, Y g:i A') }}</li>
                <li class="mb-2"><strong>Max Score:</strong> {{ $exercise->max_score }} points</li>
                <li class="mb-2"><strong>Submission Type:</strong> {{ ucfirst($exercise->submission_type) }}</li>
                <li class="mb-2">
                    <strong>Late Submissions:</strong> 
                    {{ $exercise->allow_late_submission ? 'Allowed' : 'Not Allowed' }}
                    @if($exercise->allow_late_submission && $exercise->late_penalty_percent > 0)
                        ({{ $exercise->late_penalty_percent }}% penalty)
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Bulk Grade Modal -->
<div class="modal fade" id="bulkGradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.teacher.classroom.exercises.bulk-grade', $exercise) }}" method="POST" id="bulkGradeForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-lightning me-2"></i>Bulk Grade Submissions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="selectedCount" class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Select submissions from the table to grade them all at once.
                    </div>

                    <div class="mb-3">
                        <label for="bulkScore" class="form-label">Score for All Selected <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="bulkScore" name="score" 
                                   min="0" max="{{ $exercise->max_score }}" step="0.01" required>
                            <span class="input-group-text">/ {{ $exercise->max_score }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bulkFeedback" class="form-label">Feedback for All</label>
                        <textarea class="form-control" id="bulkFeedback" name="feedback" rows="3" 
                                  placeholder="This feedback will be applied to all selected submissions..."></textarea>
                    </div>

                    <input type="hidden" name="submission_ids" id="submissionIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="bulkGradeBtn" disabled>
                        <i class="bi bi-check-circle me-2"></i>Grade Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

@media print {
    .btn, .modal, .card-header, .pagination {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const submissionCheckboxes = document.querySelectorAll('.submission-checkbox:not(:disabled)');
    const bulkGradeBtn = document.getElementById('bulkGradeBtn');
    const selectedCount = document.getElementById('selectedCount');
    const submissionIds = document.getElementById('submissionIds');
    
    selectAllCheckbox?.addEventListener('change', function() {
        submissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkGradeButton();
    });
    
    submissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkGradeButton);
    });
    
    function updateBulkGradeButton() {
        const checkedBoxes = document.querySelectorAll('.submission-checkbox:checked:not(:disabled)');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkGradeBtn.disabled = false;
            selectedCount.innerHTML = `<i class="bi bi-check-circle me-2"></i><strong>${count}</strong> submission(s) selected`;
            selectedCount.classList.remove('alert-info');
            selectedCount.classList.add('alert-success');
            
            // Collect IDs
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            submissionIds.value = ids.join(',');
        } else {
            bulkGradeBtn.disabled = true;
            selectedCount.innerHTML = '<i class="bi bi-info-circle me-2"></i>Select submissions from the table to grade them all at once.';
            selectedCount.classList.remove('alert-success');
            selectedCount.classList.add('alert-info');
            submissionIds.value = '';
        }
    }
    
    // Form validation for bulk grade
    document.getElementById('bulkGradeForm')?.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.submission-checkbox:checked:not(:disabled)').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one submission to grade.');
            return false;
        }
        
        if (!confirm(`Are you sure you want to grade ${checkedCount} submission(s) with the same score and feedback?`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@endsection
