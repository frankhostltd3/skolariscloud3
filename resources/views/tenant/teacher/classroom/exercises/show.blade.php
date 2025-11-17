@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Assignment Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>{{ $exercise->title }}
            </h1>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar me-1"></i>Due: {{ $exercise->due_date->format('F j, Y g:i A') }}
                @if($exercise->is_overdue)
                    <span class="badge bg-danger ms-2">Overdue</span>
                @else
                    <span class="badge bg-success ms-2">Active</span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}" class="btn btn-primary me-2">
                <i class="bi bi-list-check me-2"></i>View All Submissions
                @if($stats['pending'] > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $stats['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('tenant.teacher.classroom.exercises.edit', $exercise) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('tenant.teacher.classroom.exercises.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
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
                            <p class="text-muted mb-1 small">Submissions</p>
                            <h3 class="mb-0">{{ $stats['submitted'] }}</h3>
                            <small class="text-muted">
                                {{ $stats['total_students'] > 0 ? round(($stats['submitted'] / $stats['total_students']) * 100) : 0 }}% submitted
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
                            <h3 class="mb-0 {{ $stats['pending'] > 0 ? 'text-warning' : 'text-success' }}">
                                {{ $stats['pending'] }}
                            </h3>
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
                            <p class="text-muted mb-1 small">Average Score</p>
                            <h3 class="mb-0">
                                @if($stats['average_score'])
                                    {{ number_format($stats['average_score'], 1) }}
                                @else
                                    N/A
                                @endif
                            </h3>
                            <small class="text-muted">Out of {{ $exercise->max_score }}</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-bar-chart fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission Status Bar -->
    @if($stats['total_students'] > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="mb-3">Submission Progress</h6>
            <div class="progress" style="height: 30px;">
                @php
                    $submittedPercent = ($stats['submitted'] / $stats['total_students']) * 100;
                    $notSubmittedPercent = 100 - $submittedPercent;
                @endphp
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: {{ $submittedPercent }}%" 
                     aria-valuenow="{{ $submittedPercent }}" aria-valuemin="0" aria-valuemax="100">
                    {{ $stats['submitted'] }} Submitted ({{ round($submittedPercent) }}%)
                </div>
                <div class="progress-bar bg-secondary" role="progressbar" 
                     style="width: {{ $notSubmittedPercent }}%" 
                     aria-valuenow="{{ $notSubmittedPercent }}" aria-valuemin="0" aria-valuemax="100">
                    {{ $stats['total_students'] - $stats['submitted'] }} Pending
                </div>
            </div>
            
            <!-- On-time vs Late -->
            @if($stats['submitted'] > 0)
            <div class="row mt-3">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        On Time: <strong>{{ $stats['on_time'] }}</strong> 
                        ({{ round(($stats['on_time'] / $stats['submitted']) * 100) }}%)
                    </small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-exclamation-circle text-danger me-1"></i>
                        Late: <strong>{{ $stats['late'] }}</strong> 
                        ({{ round(($stats['late'] / $stats['submitted']) * 100) }}%)
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Assignment Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Assignment Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p class="mb-0">{{ $exercise->description ?? 'No description provided.' }}</p>
                    </div>

                    @if($exercise->instructions)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Instructions</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($exercise->instructions)) !!}
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Class</h6>
                            <p class="mb-0">
                                <i class="bi bi-mortarboard me-2"></i>{{ $exercise->class->name }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Subject</h6>
                            <p class="mb-0">
                                <i class="bi bi-book me-2"></i>{{ $exercise->subject->name }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Due Date</h6>
                            <p class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>{{ $exercise->due_date->format('F j, Y g:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Maximum Score</h6>
                            <p class="mb-0">
                                <i class="bi bi-trophy me-2"></i>{{ $exercise->max_score }} points
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Submission Type</h6>
                            <p class="mb-0">
                                <i class="bi bi-upload me-2"></i>{{ ucfirst($exercise->submission_type) }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Late Submission</h6>
                            <p class="mb-0">
                                @if($exercise->allow_late_submission)
                                    <i class="bi bi-check-circle text-success me-2"></i>Allowed
                                    @if($exercise->late_penalty_percent > 0)
                                        <span class="text-muted small">({{ $exercise->late_penalty_percent }}% penalty)</span>
                                    @endif
                                @else
                                    <i class="bi bi-x-circle text-danger me-2"></i>Not Allowed
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Submissions -->
            @if($recentSubmissions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Submissions</h5>
                    <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Submitted At</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSubmissions as $submission)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ substr($submission->student->first_name, 0, 1) }}{{ substr($submission->student->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $submission->student->full_name }}</div>
                                                <small class="text-muted">{{ $submission->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $submission->submitted_at->format('M j, Y g:i A') }}</small>
                                        @if($submission->submitted_at->gt($exercise->due_date))
                                            <br><span class="badge bg-danger small">Late</span>
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
                                            <strong>{{ $submission->score }}</strong> / {{ $exercise->max_score }}
                                        @else
                                            <span class="text-muted">Not graded</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                    <h5>No Submissions Yet</h5>
                    <p class="text-muted">Students haven't submitted their work yet.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}" class="btn btn-primary">
                            <i class="bi bi-list-check me-2"></i>View All Submissions
                        </a>
                        <a href="{{ route('tenant.teacher.classroom.exercises.edit', $exercise) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Assignment
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>Delete Assignment
                        </button>
                    </div>
                </div>
            </div>

            <!-- Assignment Info -->
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Assignment Info</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <strong>Created:</strong><br>
                            {{ $exercise->created_at->format('F j, Y g:i A') }}
                        </li>
                        <li class="mb-2">
                            <strong>Last Updated:</strong><br>
                            {{ $exercise->updated_at->format('F j, Y g:i A') }}
                        </li>
                        <li class="mb-0">
                            <strong>Teacher:</strong><br>
                            {{ $exercise->teacher->full_name }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Status Alert -->
            @if($exercise->is_overdue && $stats['submitted'] < $stats['total_students'])
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Overdue!</strong><br>
                {{ $stats['total_students'] - $stats['submitted'] }} student(s) have not submitted yet.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this assignment?</p>
                @if($stats['submitted'] > 0)
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This assignment has {{ $stats['submitted'] }} submission(s). 
                    You cannot delete it. Please archive it instead.
                </div>
                @else
                <p class="text-muted small mb-0">This action cannot be undone.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if($stats['submitted'] == 0)
                <form action="{{ route('tenant.teacher.classroom.exercises.destroy', $exercise) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Assignment</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endpush
@endsection

