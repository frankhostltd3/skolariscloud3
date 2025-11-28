@extends('layouts.dashboard-teacher')

@section('title', 'Virtual Classes')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-camera-video me-2 text-primary"></i>Virtual Classes
                </h1>
                <p class="text-muted mb-0">Schedule and manage online classes via Zoom, Google Meet, or YouTube</p>
            </div>
            <div>
                @if (empty($tableMissing))
                    <a href="{{ route('tenant.teacher.classroom.virtual.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Schedule New Class
                    </a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (!empty($tableMissing))
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                Virtual class scheduling is not available for this school yet. Please ask your administrator to run the
                latest tenant migrations to create the required tables.
            </div>
        @endif

        @if (empty($tableMissing))
            <!-- Filter & Tabs -->
            <div class="card mb-4">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab"
                                data-bs-target="#upcoming" type="button" role="tab">
                                <i class="bi bi-calendar-check me-2"></i>Upcoming
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live"
                                type="button" role="tab">
                                <i class="bi bi-broadcast me-2"></i>Live Now
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed"
                                type="button" role="tab">
                                <i class="bi bi-check-circle me-2"></i>Completed
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                                type="button" role="tab">
                                <i class="bi bi-list me-2"></i>All Classes
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Upcoming Tab -->
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                    <div class="row g-4">
                        @php
                            $upcomingClasses = $virtualClasses
                                ->where('status', 'scheduled')
                                ->where('scheduled_at', '>', now());
                        @endphp

                        @forelse($upcomingClasses as $class)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">{{ $class->title }}</h5>
                                        <small>{{ $class->class->name ?? 'N/A' }} •
                                            {{ $class->subject->name ?? 'N/A' }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-camera-video me-1"></i>{{ ucfirst($class->platform) }}
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-calendar me-1"></i>{{ ucfirst($class->status) }}
                                            </span>
                                        </div>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <strong>Date:</strong> {{ $class->scheduled_at->format('M d, Y') }}
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-clock me-2"></i>
                                            <strong>Time:</strong> {{ $class->scheduled_at->format('h:i A') }}
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-hourglass-split me-2"></i>
                                            <strong>Duration:</strong> {{ $class->duration_minutes }} minutes
                                        </p>
                                        <p class="text-muted mb-3">
                                            <i class="bi bi-people me-2"></i>
                                            <strong>Students:</strong> {{ $class->attendances_count ?? 0 }} enrolled
                                        </p>
                                        <hr>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('tenant.teacher.classroom.virtual.show', $class) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>View Details
                                            </a>
                                            <a href="{{ route('tenant.teacher.classroom.virtual.edit', $class) }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                            <form action="{{ route('tenant.teacher.classroom.virtual.start', $class) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="bi bi-play-circle me-2"></i>Start Class
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>Starts
                                            {{ $class->scheduled_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 text-muted">No Upcoming Virtual Classes</h5>
                                        <p class="text-muted mb-4">Schedule your first online class to get started</p>
                                        <a href="{{ route('tenant.teacher.classroom.virtual.create') }}"
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>Schedule First Class
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Live Now Tab -->
                <div class="tab-pane fade" id="live" role="tabpanel">
                    <div class="row g-4">
                        @php
                            $liveClasses = $virtualClasses->where('status', 'live');
                        @endphp

                        @forelse($liveClasses as $class)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-white text-danger me-2 pulse">LIVE</span>
                                            <h5 class="mb-0">{{ $class->title }}</h5>
                                        </div>
                                        <small>{{ $class->class->name ?? 'N/A' }} •
                                            {{ $class->subject->name ?? 'N/A' }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-camera-video me-1"></i>{{ ucfirst($class->platform) }}
                                            </span>
                                        </div>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-clock me-2"></i>
                                            <strong>Started:</strong>
                                            {{ $class->starts_at ? $class->starts_at->diffForHumans() : 'Just now' }}
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-hourglass-split me-2"></i>
                                            <strong>Duration:</strong> {{ $class->duration_minutes }} minutes
                                        </p>
                                        @if ($class->meeting_url)
                                            <p class="text-muted mb-3">
                                                <i class="bi bi-link-45deg me-2"></i>
                                                <a href="{{ $class->meeting_url }}" target="_blank"
                                                    class="text-decoration-none">Join Meeting</a>
                                            </p>
                                        @endif
                                        <hr>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('tenant.teacher.classroom.virtual.show', $class) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>View Details
                                            </a>
                                            <form action="{{ route('tenant.teacher.classroom.virtual.end', $class) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm w-100"
                                                    onclick="return confirm('Are you sure you want to end this class?')">
                                                    <i class="bi bi-stop-circle me-2"></i>End Class
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-broadcast text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 text-muted">No Live Classes</h5>
                                        <p class="text-muted mb-0">No classes are currently in progress</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Tab -->
                <div class="tab-pane fade" id="completed" role="tabpanel">
                    <div class="row g-4">
                        @php
                            $completedClasses = $virtualClasses->where('status', 'completed');
                        @endphp

                        @forelse($completedClasses as $class)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">{{ $class->title }}</h5>
                                        <small>{{ $class->class->name ?? 'N/A' }} •
                                            {{ $class->subject->name ?? 'N/A' }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-camera-video me-1"></i>{{ ucfirst($class->platform) }}
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Completed
                                            </span>
                                        </div>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <strong>Date:</strong> {{ $class->scheduled_at->format('M d, Y') }}
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-clock me-2"></i>
                                            <strong>Duration:</strong> {{ $class->duration_minutes }} minutes
                                        </p>
                                        <p class="text-muted mb-3">
                                            <i class="bi bi-people me-2"></i>
                                            <strong>Attendance:</strong> {{ $class->attendances_count ?? 0 }} students
                                        </p>
                                        @if ($class->recording_url)
                                            <p class="text-muted mb-3">
                                                <i class="bi bi-film me-2"></i>
                                                <a href="{{ $class->recording_url }}" target="_blank"
                                                    class="text-decoration-none">View Recording</a>
                                            </p>
                                        @endif
                                        <hr>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('tenant.teacher.classroom.virtual.show', $class) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>View Details
                                            </a>
                                            <a href="{{ route('tenant.teacher.classroom.virtual.attendance', $class) }}"
                                                class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-people me-2"></i>View Attendance
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>Ended
                                            {{ $class->ends_at ? $class->ends_at->diffForHumans() : 'recently' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-check-circle text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 text-muted">No Completed Classes</h5>
                                        <p class="text-muted mb-0">Completed virtual classes will appear here</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- All Classes Tab -->
                <div class="tab-pane fade" id="all" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Class</th>
                                            <th>Platform</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($virtualClasses as $class)
                                            <tr>
                                                <td>
                                                    <strong>{{ $class->title }}</strong><br>
                                                    <small class="text-muted">{{ $class->subject->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>{{ $class->class->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($class->platform) }}</span>
                                                </td>
                                                <td>
                                                    {{ $class->scheduled_at->format('M d, Y') }}<br>
                                                    <small
                                                        class="text-muted">{{ $class->scheduled_at->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if ($class->status === 'live')
                                                        <span class="badge bg-danger">Live</span>
                                                    @elseif($class->status === 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($class->status === 'cancelled')
                                                        <span class="badge bg-secondary">Cancelled</span>
                                                    @else
                                                        <span class="badge bg-primary">Scheduled</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('tenant.teacher.classroom.virtual.show', $class) }}"
                                                            class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('tenant.teacher.classroom.virtual.edit', $class) }}"
                                                            class="btn btn-outline-secondary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('tenant.teacher.classroom.virtual.destroy', $class) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this virtual class?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger"
                                                                title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    No virtual classes scheduled yet. <a
                                                        href="{{ route('tenant.teacher.classroom.virtual.create') }}">Schedule
                                                        one now</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($virtualClasses->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $virtualClasses->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-4 mt-4">
                <div class="col-md-3">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <i class="bi bi-calendar-check text-primary" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0">{{ $stats['scheduled'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">Scheduled</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0">{{ $stats['completed'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <i class="bi bi-people text-info" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0">{{ $stats['total_participants'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">Total Participants</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0">{{ $stats['total_hours'] ?? 0 }}h</h3>
                            <p class="text-muted mb-0 small">Total Hours</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-hdd-network text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Virtual class tables missing</h5>
                    <p class="text-muted mb-0">Once your administrator runs the necessary migrations you will be able to
                        schedule online classes here.</p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <style>
        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush
