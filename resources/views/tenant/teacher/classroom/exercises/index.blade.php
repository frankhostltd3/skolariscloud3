@extends('layouts.dashboard-teacher')

@section('title', 'Assignments & Exercises')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Assignments & Exercises
                </h1>
                <p class="text-muted mb-0">Create assignments and review student submissions</p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.exercises.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Assignment
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
                        <i class="bi bi-clipboard-check text-primary" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['total'] }}</h4>
                        <small class="text-muted">Total Assignments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success h-100">
                    <div class="card-body">
                        <i class="bi bi-hourglass-split text-success" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['active'] }}</h4>
                        <small class="text-muted">Active Assignments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-warning h-100">
                    <div class="card-body">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['pending_grading'] }}</h4>
                        <small class="text-muted">Pending Grading</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info h-100">
                    <div class="card-body">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['total_submissions'] }}</h4>
                        <small class="text-muted">Total Submissions</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Assignments</h5>
            </div>
            <div class="card-body">
                @if ($exercises->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Class & Subject</th>
                                    <th>Due Date</th>
                                    <th>Submissions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exercises as $exercise)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}"
                                                class="text-decoration-none fw-bold">
                                                {{ $exercise->title }}
                                            </a>
                                            <div class="small text-muted">{{ Str::limit($exercise->description, 50) }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $exercise->class->name }}</div>
                                            <small class="text-muted">{{ $exercise->subject->name }}</small>
                                        </td>
                                        <td>
                                            <div class="{{ $exercise->is_overdue ? 'text-danger' : '' }}">
                                                {{ $exercise->due_date->format('M d, Y') }}
                                            </div>
                                            <small class="text-muted">{{ $exercise->due_date->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $exercise->submissions_count > 0 ? ($exercise->graded_submissions_count / $exercise->submissions_count) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $exercise->graded_submissions_count }}/{{ $exercise->submissions_count }}
                                                </small>
                                            </div>
                                            @if ($exercise->pending_submissions_count > 0)
                                                <a href="{{ route('tenant.teacher.classroom.exercises.submissions', ['exercise' => $exercise->id, 'filter' => 'pending']) }}"
                                                    class="badge bg-warning text-dark text-decoration-none">
                                                    {{ $exercise->pending_submissions_count }} to grade
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($exercise->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Closed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('tenant.teacher.classroom.exercises.edit', $exercise) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('tenant.teacher.classroom.exercises.submissions', $exercise) }}"
                                                    class="btn btn-sm btn-outline-info" title="Submissions">
                                                    <i class="bi bi-people"></i>
                                                </a>
                                                <a href="{{ route('tenant.teacher.classroom.exercises.analytics', $exercise) }}"
                                                    class="btn btn-sm btn-outline-success" title="Analytics">
                                                    <i class="bi bi-graph-up"></i>
                                                </a>
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-dark dropdown-toggle"
                                                        data-bs-toggle="dropdown" title="More Actions">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('tenant.teacher.classroom.exercises.export', ['exercise' => $exercise, 'format' => 'csv']) }}">
                                                                <i class="bi bi-download me-2"></i>Export CSV
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('tenant.teacher.classroom.exercises.export', ['exercise' => $exercise, 'format' => 'pdf']) }}">
                                                                <i class="bi bi-file-pdf me-2"></i>Export PDF
                                                            </a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('tenant.teacher.classroom.exercises.duplicate', $exercise) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="bi bi-files me-2"></i>Duplicate
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        @if ($exercise->status === 'active')
                                                            <li>
                                                                <form
                                                                    action="{{ route('tenant.teacher.classroom.exercises.archive', $exercise) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="bi bi-archive me-2"></i>Archive
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <form
                                                                    action="{{ route('tenant.teacher.classroom.exercises.reopen', $exercise) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i
                                                                            class="bi bi-arrow-counterclockwise me-2"></i>Reopen
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $exercises->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Assignments Found</h5>
                        <p class="text-muted mb-4">Get started by creating your first assignment.</p>
                        <a href="{{ route('tenant.teacher.classroom.exercises.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Assignment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
