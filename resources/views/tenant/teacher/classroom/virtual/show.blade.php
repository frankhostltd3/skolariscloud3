@extends('layouts.tenant.teacher')

@section('title', 'Virtual Class Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.teacher.classroom.index') }}">Classroom</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant.teacher.classroom.virtual.index') }}">Virtual Classes</a></li>
                    <li class="breadcrumb-item active">{{ $class->title }}</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="bi bi-camera-video me-2"></i>{{ $class->title }}
            </h2>
        </div>
        <div class="btn-group">
            @if($class->status === 'scheduled')
                <a href="{{ route('tenant.teacher.classroom.virtual.edit', $class) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('tenant.teacher.classroom.virtual.start', $class) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-play-circle"></i> Start Class
                    </button>
                </form>
            @elseif($class->status === 'ongoing')
                <form action="{{ route('tenant.teacher.classroom.virtual.end', $class) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-stop-circle"></i> End Class
                    </button>
                </form>
            @endif
            @if($class->status === 'scheduled')
                <form action="{{ route('tenant.teacher.classroom.virtual.cancel', $class) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this class?')">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-2">Class Information</h5>
                            <span class="badge bg-{{ $class->status === 'ongoing' ? 'success' : ($class->status === 'completed' ? 'secondary' : ($class->status === 'cancelled' ? 'danger' : 'primary')) }} px-3 py-2">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                                {{ ucfirst($class->status) }}
                            </span>
                        </div>
                        @if($class->meeting_url)
                            <a href="{{ $class->meeting_url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Join Meeting
                            </a>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-muted me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Date</small>
                                    <strong>{{ $class->scheduled_at->format('M d, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock text-muted me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Time</small>
                                    <strong>{{ $class->scheduled_at->format('h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-split text-muted me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Duration</small>
                                    <strong>{{ $class->duration }} minutes</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-camera-video text-muted me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Platform</small>
                                    <strong>{{ ucfirst($class->platform) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($class->description)
                        <hr class="my-3">
                        <div>
                            <h6 class="mb-2">Description</h6>
                            <p class="text-muted mb-0">{{ $class->description }}</p>
                        </div>
                    @endif

                    @if($class->meeting_id)
                        <hr class="my-3">
                        <div>
                            <h6 class="mb-2">Meeting Details</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2">
                                    <small class="text-muted">Meeting ID:</small>
                                    <code class="ms-2">{{ $class->meeting_id }}</code>
                                </div>
                                @if($class->meeting_password)
                                    <div>
                                        <small class="text-muted">Password:</small>
                                        <code class="ms-2">{{ $class->meeting_password }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attendance Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person-check me-2"></i>Attendance
                        </h5>
                        @if($class->status === 'ongoing' || $class->status === 'completed')
                            <a href="{{ route('tenant.teacher.classroom.virtual.attendance', $class) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-clipboard-check"></i> Take Attendance
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($attendanceRecords->count() > 0)
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h3 class="mb-1 text-success">{{ $attendanceStats['present'] }}</h3>
                                    <small class="text-muted">Present</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h3 class="mb-1 text-danger">{{ $attendanceStats['absent'] }}</h3>
                                    <small class="text-muted">Absent</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h3 class="mb-1 text-warning">{{ $attendanceStats['late'] }}</h3>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Status</th>
                                        <th>Marked At</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceRecords as $record)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ substr($record->student->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $record->student->name }}</div>
                                                        <small class="text-muted">{{ $record->student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $record->created_at->format('M d, Y h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $record->notes ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($attendanceRecords->hasPages())
                            <div class="mt-3">
                                {{ $attendanceRecords->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No attendance records yet</p>
                            @if($class->status === 'ongoing' || $class->status === 'completed')
                                <a href="{{ route('tenant.teacher.classroom.virtual.attendance', $class) }}" class="btn btn-sm btn-primary mt-2">
                                    Take Attendance Now
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recording Section (if available) -->
            @if($class->recording_url)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-film me-2"></i>Class Recording
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-2">Recording is available for students to review</p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Recorded on {{ $class->updated_at->format('M d, Y') }}
                                </small>
                            </div>
                            <a href="{{ $class->recording_url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-play-circle me-1"></i> View Recording
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Class Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Students</span>
                            <strong class="fs-4">{{ $class->grade->students->count() ?? 0 }}</strong>
                        </div>
                    </div>
                    @if($attendanceRecords->count() > 0)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Attendance Rate</span>
                                <strong class="fs-4 text-success">{{ $attendanceRate }}%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Created</span>
                            <small>{{ $class->created_at->diffForHumans() }}</small>
                        </div>
                        @if($class->status === 'completed')
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Completed</span>
                                <small>{{ $class->updated_at->diffForHumans() }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Class Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Class Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Grade</small>
                        <strong>{{ $class->grade->name }}</strong>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Subject</small>
                        <strong>{{ $class->subject->name }}</strong>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Teacher</small>
                        <strong>{{ $class->teacher->name }}</strong>
                    </div>
                    @if($class->academic_year_id)
                        <div>
                            <small class="text-muted d-block mb-1">Academic Year</small>
                            <strong>{{ $class->academicYear->name ?? 'N/A' }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($class->meeting_url)
                            <a href="{{ $class->meeting_url }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Open Meeting
                            </a>
                        @endif
                        @if($class->status === 'scheduled')
                            <a href="{{ route('tenant.teacher.classroom.virtual.edit', $class) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i> Edit Class
                            </a>
                        @endif
                        <a href="{{ route('tenant.teacher.classroom.materials.index', ['class_id' => $class->id]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-file-earmark-text me-1"></i> Add Materials
                        </a>
                        <a href="{{ route('tenant.teacher.classroom.exercises.index', ['class_id' => $class->id]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-card-checklist me-1"></i> Create Assignment
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-1"></i> Delete Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Virtual Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to delete this virtual class?</p>
                <p class="mb-0"><strong>{{ $class->title }}</strong></p>
                <p class="text-muted small mb-0">All attendance records will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('tenant.teacher.classroom.virtual.destroy', $class) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Class</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
</style>
@endsection
