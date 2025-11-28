@extends('layouts.tenant.student')

@section('title', 'My Grades')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-trophy me-2"></i>My Grades
                </h2>
                <p class="text-muted mb-0">View your graded assignments and performance</p>
            </div>
            <a href="{{ route('tenant.student.classroom.exercises.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Assignments
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Total Graded</p>
                                <h3 class="mb-0">{{ $stats['total_graded'] }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                <i class="bi bi-check-all text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Average Grade</p>
                                <h3 class="mb-0 text-info">{{ $stats['average_grade'] ?? '-' }}%</h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-2 rounded">
                                <i class="bi bi-graph-up text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Highest Grade</p>
                                <h3 class="mb-0 text-success">{{ $stats['highest_grade'] ?? '-' }}%</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-2 rounded">
                                <i class="bi bi-trophy text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Lowest Grade</p>
                                <h3 class="mb-0 text-danger">{{ $stats['lowest_grade'] ?? '-' }}%</h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-2 rounded">
                                <i class="bi bi-arrow-down-circle text-danger fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graded Assignments List -->
        @if ($submissions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Assignment</th>
                                    <th class="py-3">Subject</th>
                                    <th class="py-3">Submitted</th>
                                    <th class="py-3">Graded</th>
                                    <th class="py-3 text-center">Score</th>
                                    <th class="py-3 text-center">Grade</th>
                                    <th class="pe-4 py-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $submission)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                    <i class="bi bi-file-earmark-text text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $submission->exercise->title }}</h6>
                                                    <small
                                                        class="text-muted">{{ $submission->exercise->class->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $submission->exercise->subject->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $submission->submitted_at->format('M d, Y') }}</span>
                                                @if ($submission->is_late)
                                                    <small class="text-danger">Late Submission</small>
                                                @else
                                                    <small
                                                        class="text-muted">{{ $submission->submitted_at->format('h:i A') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $submission->graded_at->format('M d, Y') }}</span>
                                                <small
                                                    class="text-muted">{{ $submission->graded_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold">{{ $submission->score }}</span>
                                            <span class="text-muted small">/ {{ $submission->exercise->max_score }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $gradeClass = 'bg-secondary';
                                                if ($submission->grade >= 80) {
                                                    $gradeClass = 'bg-success';
                                                } elseif ($submission->grade >= 60) {
                                                    $gradeClass = 'bg-info';
                                                } elseif ($submission->grade >= 50) {
                                                    $gradeClass = 'bg-warning';
                                                } else {
                                                    $gradeClass = 'bg-danger';
                                                }
                                            @endphp
                                            <span class="badge {{ $gradeClass }} px-3 py-2">
                                                {{ $submission->grade }}%
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('tenant.student.classroom.exercises.show', $submission->exercise->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-clipboard-data text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Graded Assignments Yet</h4>
                    <p class="text-muted">Once your teachers grade your submissions, they will appear here.</p>
                    <a href="{{ route('tenant.student.classroom.exercises.index') }}" class="btn btn-primary mt-2">
                        View Assignments
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
