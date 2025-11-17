@extends('layouts.tenant.student')

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-card-checklist me-2"></i>My Assignments
            </h2>
            <p class="text-muted mb-0">View and submit your assignments</p>
        </div>
        <a href="{{ route('tenant.student.classroom.exercises.grades') }}" class="btn btn-outline-primary">
            <i class="bi bi-trophy me-1"></i> View Grades
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-card-checklist text-primary fs-4"></i>
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
                            <p class="text-muted mb-1 small">Pending</p>
                            <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="bi bi-clock text-warning fs-4"></i>
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
                            <p class="text-muted mb-1 small">Submitted</p>
                            <h3 class="mb-0 text-info">{{ $stats['submitted'] }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-check-circle text-info fs-4"></i>
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
                            <p class="text-muted mb-1 small">Graded</p>
                            <h3 class="mb-0 text-success">{{ $stats['graded'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-trophy text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="btn-group" role="group">
                    <a href="{{ route('tenant.student.classroom.exercises.index', ['filter' => 'all']) }}" 
                       class="btn btn-{{ $filter === 'all' ? 'primary' : 'outline-primary' }}">
                        All
                    </a>
                    <a href="{{ route('tenant.student.classroom.exercises.index', ['filter' => 'pending']) }}" 
                       class="btn btn-{{ $filter === 'pending' ? 'primary' : 'outline-primary' }}">
                        Pending
                    </a>
                    <a href="{{ route('tenant.student.classroom.exercises.index', ['filter' => 'submitted']) }}" 
                       class="btn btn-{{ $filter === 'submitted' ? 'primary' : 'outline-primary' }}">
                        Submitted
                    </a>
                    <a href="{{ route('tenant.student.classroom.exercises.index', ['filter' => 'graded']) }}" 
                       class="btn btn-{{ $filter === 'graded' ? 'primary' : 'outline-primary' }}">
                        Graded
                    </a>
                    <a href="{{ route('tenant.student.classroom.exercises.index', ['filter' => 'overdue']) }}" 
                       class="btn btn-{{ $filter === 'overdue' ? 'primary' : 'outline-primary' }}">
                        Overdue
                    </a>
                </div>

                <form action="{{ route('tenant.student.classroom.exercises.index') }}" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <input type="text" name="search" class="form-control" placeholder="Search assignments..." 
                           value="{{ request('search') }}" style="width: 250px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    @if($exercises->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($exercises as $exercise)
                <div class="col-12">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Title & Meta -->
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                                            <i class="bi bi-file-earmark-text text-primary fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $exercise->title }}</h5>
                                            <p class="text-muted mb-2 small">
                                                <i class="bi bi-book me-1"></i>{{ $exercise->subject->name }} • 
                                                <i class="bi bi-diagram-3 ms-2 me-1"></i>{{ $exercise->class->name }} • 
                                                <i class="bi bi-person ms-2 me-1"></i>{{ $exercise->teacher->name }}
                                            </p>
                                            @if($exercise->description)
                                                <p class="text-muted small mb-2">{{ Str::limit($exercise->description, 150) }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Due Date & Status -->
                                    <div class="d-flex gap-3 flex-wrap">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Due: {{ $exercise->due_date->format('M d, Y h:i A') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-star me-1"></i>
                                            Max Score: {{ $exercise->max_score }}
                                        </small>
                                        @if($exercise->allow_late_submission && $exercise->late_penalty_percent)
                                            <small class="text-muted">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Late Penalty: {{ $exercise->late_penalty_percent }}% per day
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-end">
                                        <!-- Status Badge -->
                                        @if($exercise->student_submission)
                                            @if($exercise->student_submission->grade !== null)
                                                <span class="badge bg-success px-3 py-2 mb-3">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Graded: {{ $exercise->student_submission->grade }}%
                                                </span>
                                            @else
                                                <span class="badge bg-info px-3 py-2 mb-3">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    Submitted - Pending Review
                                                </span>
                                            @endif
                                            @if($exercise->student_submission->is_late)
                                                <span class="badge bg-warning px-3 py-2 mb-3 ms-2">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Late
                                                </span>
                                            @endif
                                        @else
                                            @if(\Carbon\Carbon::now()->isAfter($exercise->due_date))
                                                <span class="badge bg-danger px-3 py-2 mb-3">
                                                    <i class="bi bi-exclamation-circle me-1"></i>
                                                    Overdue
                                                </span>
                                            @elseif($exercise->due_date->diffInDays(\Carbon\Carbon::now()) <= 2)
                                                <span class="badge bg-warning px-3 py-2 mb-3">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Due Soon
                                                </span>
                                            @else
                                                <span class="badge bg-primary px-3 py-2 mb-3">
                                                    <i class="bi bi-circle me-1"></i>
                                                    Pending
                                                </span>
                                            @endif
                                        @endif

                                        <!-- Action Button -->
                                        @if($exercise->student_submission)
                                            <a href="{{ route('tenant.student.classroom.exercises.show', $exercise) }}" 
                                               class="btn btn-outline-primary d-block">
                                                <i class="bi bi-eye me-1"></i> View Submission
                                            </a>
                                            @if($exercise->student_submission->grade !== null)
                                                <small class="text-muted d-block mt-2">
                                                    Graded {{ $exercise->student_submission->graded_at->diffForHumans() }}
                                                </small>
                                            @else
                                                <small class="text-muted d-block mt-2">
                                                    Submitted {{ $exercise->student_submission->submitted_at->diffForHumans() }}
                                                </small>
                                            @endif
                                        @else
                                            <a href="{{ route('tenant.student.classroom.exercises.show', $exercise) }}" 
                                               class="btn btn-primary d-block">
                                                <i class="bi bi-arrow-right me-1"></i> Submit Now
                                            </a>
                                            <small class="text-muted d-block mt-2">
                                                @if(\Carbon\Carbon::now()->isAfter($exercise->due_date))
                                                    {{ \Carbon\Carbon::now()->diffInDays($exercise->due_date) }} days overdue
                                                @else
                                                    {{ \Carbon\Carbon::now()->diffInDays($exercise->due_date) }} days remaining
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $exercises->appends(['filter' => $filter, 'search' => request('search')])->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                @if($filter === 'pending')
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Pending Assignments</h4>
                    <p class="text-muted">You're all caught up! No assignments need your attention.</p>
                @elseif($filter === 'overdue')
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Overdue Assignments</h4>
                    <p class="text-muted">Great job! You haven't missed any deadlines.</p>
                @else
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Assignments Found</h4>
                    <p class="text-muted">{{ request('search') ? 'Try a different search term.' : 'There are no assignments available at the moment.' }}</p>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
