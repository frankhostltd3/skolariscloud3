@extends('layouts.tenant.student')

@section('title', 'My Classroom')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-house-door me-2"></i>My Classroom
            </h2>
            <p class="text-muted mb-0">Welcome back! Here's what's happening in your classes.</p>
        </div>
        <div>
            <span class="badge bg-primary px-3 py-2">
                <i class="bi bi-calendar-check me-1"></i>
                {{ now()->format('l, M d, Y') }}
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Virtual Classes</p>
                            <h3 class="mb-0">{{ $stats['attended_classes'] }}/{{ $stats['total_classes'] }}</h3>
                            <small class="text-muted">Attended</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-camera-video text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Learning Materials</p>
                            <h3 class="mb-0">{{ $stats['accessed_materials'] }}/{{ $stats['total_materials'] }}</h3>
                            <small class="text-muted">Accessed</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-file-earmark-text text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Assignments</p>
                            <h3 class="mb-0">{{ $stats['submitted_assignments'] }}/{{ $stats['total_assignments'] }}</h3>
                            <small class="text-muted">Submitted</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-card-checklist text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Average Grade</p>
                            <h3 class="mb-0">
                                @if($stats['average_grade'])
                                    {{ $stats['average_grade'] }}%
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </h3>
                            <small class="text-muted">{{ $stats['graded_assignments'] }} graded</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-trophy text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Upcoming Classes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-event me-2"></i>Upcoming Virtual Classes
                        </h5>
                        <a href="{{ route('tenant.student.classroom.virtual.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingClasses->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingClasses as $class)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $class->title }}</h6>
                                            <p class="text-muted mb-2 small">
                                                <i class="bi bi-person me-1"></i>{{ $class->teacher->name }} • 
                                                <i class="bi bi-book ms-2 me-1"></i>{{ $class->subject->name }}
                                            </p>
                                            <div class="d-flex align-items-center gap-3">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $class->scheduled_at->format('M d, Y') }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $class->scheduled_at->format('h:i A') }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    {{ $class->duration }} min
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $class->status === 'ongoing' ? 'success' : 'primary' }} mb-2">
                                                {{ ucfirst($class->status) }}
                                            </span>
                                            @if($class->status === 'ongoing' || $class->scheduled_at->diffInMinutes(now(), false) >= -15)
                                                <a href="{{ route('tenant.student.classroom.virtual.join', $class) }}" 
                                                   class="btn btn-sm btn-success d-block">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Join
                                                </a>
                                            @else
                                                <a href="{{ route('tenant.student.classroom.virtual.show', $class) }}" 
                                                   class="btn btn-sm btn-outline-primary d-block">
                                                    Details
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No upcoming classes scheduled</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pending Assignments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-circle me-2"></i>Pending Assignments
                        </h5>
                        <a href="{{ route('tenant.student.classroom.exercises.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($pendingAssignments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingAssignments as $assignment)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $assignment->title }}</h6>
                                            <p class="text-muted mb-2 small">
                                                <i class="bi bi-book me-1"></i>{{ $assignment->subject->name }} • 
                                                <i class="bi bi-diagram-3 ms-2 me-1"></i>{{ $assignment->class->name }}
                                            </p>
                                            <div class="d-flex align-items-center gap-3">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-check me-1"></i>
                                                    Due: {{ $assignment->due_date->format('M d, Y h:i A') }}
                                                </small>
                                                @if($assignment->due_date->isPast())
                                                    <span class="badge bg-danger">Overdue</span>
                                                @elseif($assignment->due_date->diffInDays(now()) <= 2)
                                                    <span class="badge bg-warning">Due Soon</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('tenant.student.classroom.exercises.show', $assignment) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-arrow-right me-1"></i>Submit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">All caught up! No pending assignments.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Materials -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>Recent Learning Materials
                        </h5>
                        <a href="{{ route('tenant.student.classroom.materials.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentMaterials->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentMaterials->take(5) as $material)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="bi bi-{{ $material->type === 'document' ? 'file-earmark-pdf' : ($material->type === 'video' ? 'play-circle' : ($material->type === 'youtube' ? 'youtube' : ($material->type === 'link' ? 'link-45deg' : 'file-earmark'))) }} text-primary"></i>
                                                <h6 class="mb-0">{{ $material->title }}</h6>
                                            </div>
                                            <p class="text-muted mb-2 small">
                                                <i class="bi bi-book me-1"></i>{{ $material->subject->name }} • 
                                                <i class="bi bi-person ms-2 me-1"></i>{{ $material->teacher->name }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $material->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('tenant.student.classroom.materials.show', $material) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No learning materials available yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Links -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.student.classroom.virtual.today') }}" class="btn btn-outline-primary">
                            <i class="bi bi-calendar-day me-2"></i>Today's Classes
                        </a>
                        <a href="{{ route('tenant.student.classroom.materials.recent') }}" class="btn btn-outline-success">
                            <i class="bi bi-clock-history me-2"></i>Recent Materials
                        </a>
                        <a href="{{ route('tenant.student.classroom.exercises.grades') }}" class="btn btn-outline-info">
                            <i class="bi bi-trophy me-2"></i>My Grades
                        </a>
                        <a href="{{ route('tenant.student.classroom.virtual.attendance') }}" class="btn btn-outline-warning">
                            <i class="bi bi-person-check me-2"></i>My Attendance
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enrolled Classes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>My Classes
                    </h5>
                </div>
                <div class="card-body">
                    @if($enrolledClasses->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($enrolledClasses as $enrollment)
                                <a href="{{ route('tenant.student.classroom.classes.show', $enrollment->school_class_id) }}" 
                                   class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $enrollment->schoolClass->name }}</h6>
                                            <small class="text-muted">{{ $enrollment->academicYear->name ?? 'Current Year' }}</small>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No enrolled classes</p>
                    @endif
                </div>
            </div>

            <!-- Recent Submissions -->
            @if($submittedAssignments->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-check2-circle me-2"></i>Recent Submissions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($submittedAssignments as $submission)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 small">{{ Str::limit($submission->exercise->title, 30) }}</h6>
                                            <small class="text-muted">
                                                {{ $submission->submitted_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($submission->grade !== null)
                                                <span class="badge bg-success">{{ $submission->grade }}%</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.list-group-item {
    border-left: none;
    border-right: none;
}
.list-group-item:first-child {
    border-top: none;
}
.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
