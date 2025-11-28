@extends('layouts.dashboard-teacher')

@section('title', 'Online Exams')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-file-earmark-check me-2 text-primary"></i>Online Exams
                </h1>
                <p class="text-muted mb-0">Set, conduct, and grade secure online examinations</p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.exams.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Exam
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card text-center border-primary h-100">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-check text-primary" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['total'] }}</h4>
                        <small class="text-muted">Total Exams</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success h-100">
                    <div class="card-body">
                        <i class="bi bi-play-circle text-success" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['active'] }}</h4>
                        <small class="text-muted">Active Exams</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info h-100">
                    <div class="card-body">
                        <i class="bi bi-calendar-event text-info" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['scheduled'] }}</h4>
                        <small class="text-muted">Scheduled</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-secondary h-100">
                    <div class="card-body">
                        <i class="bi bi-check-circle text-secondary" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['completed'] }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exams List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Exams</h5>
            </div>
            <div class="card-body">
                @if ($exams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Class & Subject</th>
                                    <th>Schedule</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Workflow</th>
                                    <th>Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exams as $exam)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tenant.teacher.classroom.exams.show', $exam) }}"
                                                class="text-decoration-none fw-bold">
                                                {{ $exam->title }}
                                            </a>
                                            <div class="small text-muted">
                                                @if ($exam->proctored)
                                                    <span class="badge bg-info text-dark me-1"><i
                                                            class="bi bi-camera-video me-1"></i>Proctored</span>
                                                @endif
                                                {{ Str::limit($exam->description, 40) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $exam->class->name }}</div>
                                            <small class="text-muted">{{ $exam->subject->name }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $exam->start_time->format('M d, Y') }}</div>
                                            <small class="text-muted">
                                                {{ $exam->start_time->format('h:i A') }} -
                                                {{ $exam->end_time->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            {{ $exam->duration_minutes }} mins
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($exam->status) {
                                                    'active' => 'success',
                                                    'scheduled' => 'info',
                                                    'completed' => 'secondary',
                                                    'draft' => 'warning',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($exam->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $approvalClass = match ($exam->approval_status) {
                                                    'approved' => 'success',
                                                    'pending_review' => 'info',
                                                    'changes_requested' => 'warning',
                                                    'rejected' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $approvalClass }} text-uppercase">
                                                {{ str_replace('_', ' ', $exam->approval_status ?? 'draft') }}
                                            </span>
                                            <div class="small text-muted mt-1">
                                                {{ ucfirst($exam->activation_mode ?? 'schedule') }} ·
                                                {{ ucfirst($exam->creation_method ?? 'manual') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column small">
                                                <span><i class="bi bi-people me-1"></i>{{ $exam->attempts_count }}
                                                    attempts</span>
                                                <span><i
                                                        class="bi bi-question-circle me-1"></i>{{ $exam->questions_count }}
                                                    questions</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('tenant.teacher.classroom.exams.show', $exam) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('tenant.teacher.classroom.exams.edit', $exam) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('tenant.teacher.classroom.exams.destroy', $exam) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this exam?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $exams->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Exams Found</h5>
                        <p class="text-muted mb-4">Get started by creating your first online exam.</p>
                        <a href="{{ route('tenant.teacher.classroom.exams.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Exam
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Security Features Info -->
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="card-title h6">
                            <i class="bi bi-shield-check me-2 text-success"></i>Security & Anti-Cheating Features
                        </h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-camera-video text-primary me-2"></i>
                                    <span class="small">Webcam Proctoring</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-window-x text-danger me-2"></i>
                                    <span class="small">Tab Switch Detection</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clipboard-x text-warning me-2"></i>
                                    <span class="small">Copy/Paste Blocking</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-shuffle text-info me-2"></i>
                                    <span class="small">Question Shuffling</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
