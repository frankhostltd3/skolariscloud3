@extends('layouts.tenant.student')

@section('title', 'My Virtual Classes')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-camera-video me-2"></i>My Virtual Classes
            </h2>
            <p class="text-muted mb-0">Join live classes and view recordings</p>
        </div>
        <a href="{{ route('tenant.student.classroom.attendance.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-calendar-check me-1"></i> My Attendance
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Classes</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-camera-video text-primary fs-4"></i>
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
                            <p class="text-muted mb-1 small">Upcoming</p>
                            <h3 class="mb-0 text-info">{{ $stats['upcoming'] }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-clock text-info fs-4"></i>
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
                            <p class="text-muted mb-1 small">Attended</p>
                            <h3 class="mb-0 text-success">{{ $stats['attended'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-check-circle text-success fs-4"></i>
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
                            <p class="text-muted mb-1 small">Missed</p>
                            <h3 class="mb-0 text-danger">{{ $stats['missed'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
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
                    <a href="{{ route('tenant.student.classroom.virtual.index', ['filter' => 'all']) }}" 
                       class="btn btn-{{ $filter === 'all' ? 'primary' : 'outline-primary' }}">
                        All
                    </a>
                    <a href="{{ route('tenant.student.classroom.virtual.index', ['filter' => 'upcoming']) }}" 
                       class="btn btn-{{ $filter === 'upcoming' ? 'primary' : 'outline-primary' }}">
                        Upcoming
                    </a>
                    <a href="{{ route('tenant.student.classroom.virtual.index', ['filter' => 'ongoing']) }}" 
                       class="btn btn-{{ $filter === 'ongoing' ? 'primary' : 'outline-primary' }}">
                        Ongoing
                    </a>
                    <a href="{{ route('tenant.student.classroom.virtual.index', ['filter' => 'completed']) }}" 
                       class="btn btn-{{ $filter === 'completed' ? 'primary' : 'outline-primary' }}">
                        Completed
                    </a>
                    <a href="{{ route('tenant.student.classroom.virtual.index', ['filter' => 'missed']) }}" 
                       class="btn btn-{{ $filter === 'missed' ? 'primary' : 'outline-primary' }}">
                        Missed
                    </a>
                </div>

                <form action="{{ route('tenant.student.classroom.virtual.index') }}" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <input type="text" name="search" class="form-control" placeholder="Search classes..." 
                           value="{{ request('search') }}" style="width: 250px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Classes List -->
    @if($classes->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($classes as $class)
                @php
                    $now = \Carbon\Carbon::now();
                    $isOngoing = $class->status === 'ongoing';
                    $isUpcoming = $class->status === 'scheduled' && $now->isBefore($class->start_time);
                    $isCompleted = $class->status === 'completed';
                    $isCancelled = $class->status === 'cancelled';
                    $canJoin = $isOngoing || ($isUpcoming && $now->diffInMinutes($class->start_time, false) <= 15);
                    
                    // Get student's attendance
                    $attendance = $class->attendances->where('student_id', auth()->id())->first();
                @endphp

                <div class="col-12">
                    <div class="card border-0 shadow-sm hover-card {{ $isOngoing ? 'border-start border-success border-5' : '' }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Title & Meta -->
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="bg-{{ $isOngoing ? 'success' : ($isUpcoming ? 'primary' : 'secondary') }} bg-opacity-10 p-3 rounded">
                                            <i class="bi bi-camera-video text-{{ $isOngoing ? 'success' : ($isUpcoming ? 'primary' : 'secondary') }} fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $class->title }}</h5>
                                            <p class="text-muted mb-2 small">
                                                <i class="bi bi-book me-1"></i>{{ $class->subject->name }} • 
                                                <i class="bi bi-diagram-3 ms-2 me-1"></i>{{ $class->class->name }} • 
                                                <i class="bi bi-person ms-2 me-1"></i>{{ $class->teacher->name }}
                                            </p>
                                            @if($class->description)
                                                <p class="text-muted small mb-2">{{ Str::limit($class->description, 150) }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Schedule & Platform -->
                                    <div class="d-flex gap-3 flex-wrap">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $class->start_time->format('M d, Y') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $class->start_time->format('h:i A') }} - {{ $class->end_time->format('h:i A') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-globe me-1"></i>
                                            {{ ucfirst($class->platform) }}
                                        </small>
                                        @if($class->duration)
                                            <small class="text-muted">
                                                <i class="bi bi-hourglass me-1"></i>
                                                {{ $class->duration }} min
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-end">
                                        <!-- Status Badge -->
                                        @if($isCancelled)
                                            <span class="badge bg-secondary px-3 py-2 mb-3">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Cancelled
                                            </span>
                                        @elseif($isOngoing)
                                            <span class="badge bg-success px-3 py-2 mb-3">
                                                <i class="bi bi-broadcast me-1"></i>
                                                Live Now
                                            </span>
                                        @elseif($isUpcoming)
                                            <span class="badge bg-primary px-3 py-2 mb-3">
                                                <i class="bi bi-clock me-1"></i>
                                                Upcoming
                                            </span>
                                        @elseif($isCompleted)
                                            @if($attendance)
                                                @if($attendance->status === 'present')
                                                    <span class="badge bg-success px-3 py-2 mb-3">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Attended
                                                    </span>
                                                @elseif($attendance->status === 'late')
                                                    <span class="badge bg-warning px-3 py-2 mb-3">
                                                        <i class="bi bi-clock me-1"></i>
                                                        Late
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-2 mb-3">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                        Absent
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger px-3 py-2 mb-3">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Missed
                                                </span>
                                            @endif
                                        @endif

                                        <!-- Action Button -->
                                        @if($canJoin && !$isCancelled)
                                            <a href="{{ route('tenant.student.classroom.virtual.show', $class) }}" 
                                               class="btn btn-{{ $isOngoing ? 'success' : 'primary' }} d-block mb-2">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> 
                                                {{ $isOngoing ? 'Join Now' : 'Join Class' }}
                                            </a>
                                            @if($isUpcoming)
                                                <small class="text-muted d-block">
                                                    Starts {{ $class->start_time->diffForHumans() }}
                                                </small>
                                            @endif
                                        @else
                                            <a href="{{ route('tenant.student.classroom.virtual.show', $class) }}" 
                                               class="btn btn-outline-primary d-block">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </a>
                                            @if($isCompleted && $class->recording_url)
                                                <small class="text-success d-block mt-2">
                                                    <i class="bi bi-play-circle me-1"></i>Recording Available
                                                </small>
                                            @endif
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
            {{ $classes->appends(['filter' => $filter, 'search' => request('search')])->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                @if($filter === 'upcoming')
                    <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Upcoming Classes</h4>
                    <p class="text-muted">You don't have any classes scheduled soon.</p>
                @elseif($filter === 'ongoing')
                    <i class="bi bi-camera-video-off text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Live Classes</h4>
                    <p class="text-muted">There are no classes happening right now.</p>
                @elseif($filter === 'missed')
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Missed Classes</h4>
                    <p class="text-muted">Great job! You haven't missed any classes.</p>
                @else
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Classes Found</h4>
                    <p class="text-muted">{{ request('search') ? 'Try a different search term.' : 'There are no virtual classes available at the moment.' }}</p>
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
