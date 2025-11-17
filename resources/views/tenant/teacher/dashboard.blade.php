@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  @php $teacherAvatar = auth()->user()->profile_photo_url; @endphp
  <div class="d-flex align-items-center gap-3">
    @if($teacherAvatar)
      <img src="{{ $teacherAvatar }}" 
           alt="{{ auth()->user()->name }}" 
           class="rounded-circle border" 
           width="64" 
           height="64"
           style="object-fit: cover;">
    @else
      <div class="rounded-circle bg-primary bg-opacity-10 border border-primary d-flex align-items-center justify-content-center fw-bold text-primary" 
           style="width: 64px; height: 64px; font-size: 28px;">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
    @endif
    <div>
      <h1 class="h4 fw-semibold mb-1">Welcome back, {{ auth()->user()->name ?? 'Teacher' }}!</h1>
      <p class="text-muted small mb-0">
        <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
        <span class="ms-3"><i class="bi bi-clock me-1"></i>{{ now()->format('h:i A') }}</span>
      </p>
    </div>
  </div>
  <span class="badge bg-primary-subtle text-primary border border-primary">Teacher Portal</span>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="small text-secondary">My Classes</div>
          <div class="rounded-circle bg-primary bg-opacity-10 p-2">
            <i class="bi bi-journal-bookmark-fill text-primary"></i>
          </div>
        </div>
        <div class="display-6 fw-semibold mb-0">{{ $stats['classes'] ?? 0 }}</div>
        <small class="text-muted">Active classes</small>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="small text-secondary">Total Students</div>
          <div class="rounded-circle bg-success bg-opacity-10 p-2">
            <i class="bi bi-people-fill text-success"></i>
          </div>
        </div>
        <div class="display-6 fw-semibold mb-0">{{ $stats['students'] ?? 0 }}</div>
        <small class="text-muted">Under my supervision</small>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="small text-secondary">Assignments</div>
          <div class="rounded-circle bg-warning bg-opacity-10 p-2">
            <i class="bi bi-clipboard-check-fill text-warning"></i>
          </div>
        </div>
        <div class="display-6 fw-semibold mb-0">{{ $stats['assignments'] ?? 0 }}</div>
        <small class="text-muted">Pending review</small>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="small text-secondary">Attendance</div>
          <div class="rounded-circle bg-info bg-opacity-10 p-2">
            <i class="bi bi-check-circle-fill text-info"></i>
          </div>
        </div>
        <div class="display-6 fw-semibold mb-0">{{ $stats['attendance'] ?? 0 }}%</div>
        <small class="text-muted">
          @if(($stats['attendance'] ?? 0) >= 90)
            <span class="text-success"><i class="bi bi-arrow-up"></i> Excellent</span>
          @elseif(($stats['attendance'] ?? 0) >= 75)
            <span class="text-info"><i class="bi bi-dash-circle"></i> Good</span>
          @else
            <span class="text-danger"><i class="bi bi-arrow-down"></i> Needs attention</span>
          @endif
        </small>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="card shadow-sm mb-4 border-0">
  <div class="card-body">
    <h2 class="h6 fw-semibold mb-3">
      <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick actions
    </h2>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('tenant.teacher.classes.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-journal-bookmark me-1"></i>My Classes ({{ $stats['classes'] ?? 0 }})
      </a>
      <a href="{{ route('tenant.teacher.subjects.index') }}" class="btn btn-outline-success btn-sm">
        <i class="bi bi-book me-1"></i>My Subjects ({{ $stats['subjects'] ?? 0 }})
      </a>
      <a href="{{ route('tenant.teacher.students.index') }}" class="btn btn-outline-info btn-sm">
        <i class="bi bi-people me-1"></i>My Students ({{ $stats['students'] ?? 0 }})
      </a>
      <a href="{{ route('tenant.modules.attendance.index') }}" class="btn btn-outline-warning btn-sm">
        <i class="bi bi-clipboard-check me-1"></i>Mark Attendance
      </a>
      <a href="{{ route('tenant.teacher.grades.index') }}" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-award me-1"></i>Enter Grades
      </a>
      <a href="{{ route('tenant.teacher.timetable.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-calendar-week me-1"></i>View Timetable
      </a>
    </div>
  </div>
</div>

<!-- Allocated Classes & Subjects -->
<div class="card shadow-sm mb-4 border-0">
  <div class="card-header bg-white border-0">
    <h5 class="mb-0 fw-semibold">
      <i class="bi bi-diagram-3 text-primary me-2"></i>My Allocated Classes & Subjects
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <h6 class="fw-semibold mb-2"><i class="bi bi-journal-bookmark text-primary me-1"></i>Classes</h6>
        @if(isset($allClasses) && $allClasses->count())
          <ul class="list-group list-group-flush mb-3">
            @foreach($allClasses as $class)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                  <strong>{{ $class->name }}</strong>
                  @if($class->stream)
                    <span class="badge bg-info ms-2">{{ $class->stream->name }}</span>
                  @endif
                  <span class="text-muted small ms-2">Year: {{ $class->academicYear->name ?? '-' }}</span>
                </span>
                <span class="badge bg-secondary">{{ $class->students->count() }} students</span>
              </li>
            @endforeach
          </ul>
        @else
          <div class="alert alert-info mb-0">No classes allocated.</div>
        @endif
      </div>
  <div class="col-md-6">
        <h6 class="fw-semibold mb-2"><i class="bi bi-book text-success me-1"></i>Subjects</h6>
        @if(isset($allSubjects) && $allSubjects->count())
          <ul class="list-group list-group-flush mb-3">
            @foreach($allSubjects as $subject)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                  <strong>{{ $subject->name }}</strong>
                  @if($subject->pivot && $subject->pivot->class_id)
                    <small class="text-muted">({{ $subject->pivot->class->name ?? 'Class' }})</small>
                  @endif
                </span>
                <span class="badge bg-success">{{ $subject->code }}</span>
              </li>
            @endforeach
          </ul>
        @else
          <div class="alert alert-info mb-0">No subjects allocated.</div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Today's Timetable -->
@if(isset($todaySchedule) && $todaySchedule->count())
<div class="card shadow-sm mb-4 border-0">
  <div class="card-header bg-info text-white border-0">
    <h5 class="mb-0 fw-semibold">
      <i class="bi bi-calendar-check me-2"></i>Today's Schedule - {{ now()->format('l, F j, Y') }}
    </h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th><i class="bi bi-clock me-1"></i>Time</th>
            <th><i class="bi bi-book me-1"></i>Subject</th>
            <th><i class="bi bi-diagram-3 me-1"></i>Class</th>
            <th><i class="bi bi-door-open me-1"></i>Room</th>
          </tr>
        </thead>
        <tbody>
          @foreach($todaySchedule as $schedule)
          <tr>
            <td class="fw-medium">
              {{ $schedule->start_time }} - {{ $schedule->end_time }}
            </td>
            <td>
              <strong>{{ $schedule->subject->name ?? 'N/A' }}</strong>
              <small class="text-muted d-block">{{ $schedule->subject->code ?? '' }}</small>
            </td>
            <td>
              {{ $schedule->class->name ?? 'N/A' }}
              @if($schedule->stream)
                <br><small class="text-muted">{{ $schedule->stream->name }}</small>
              @endif
            </td>
            <td>{{ $schedule->room ?? 'TBD' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <a href="{{ route('tenant.teacher.timetable.index') }}" class="btn btn-outline-info btn-sm">
        <i class="bi bi-calendar-week me-1"></i>View Full Timetable
      </a>
    </div>
  </div>
</div>
@endif

<!-- Students Under Supervision -->
@if(isset($allClasses) && $allClasses->count())
<div class="card shadow-sm mb-4 border-0">
  <div class="card-header bg-success text-white border-0">
    <h5 class="mb-0 fw-semibold">
      <i class="bi bi-people me-2"></i>My Students ({{ $stats['students'] ?? 0 }} total)
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      @foreach($allClasses as $class)
        @if($class->students->count() > 0)
          <div class="col-md-6 mb-3">
            <div class="card border">
              <div class="card-header bg-light">
                <h6 class="mb-0">
                  <i class="bi bi-diagram-3 me-1"></i>{{ $class->name }}
                  @if($class->stream)
                    <span class="badge bg-info ms-1">{{ $class->stream->name }}</span>
                  @endif
                  <span class="badge bg-secondary float-end">{{ $class->students->count() }} students</span>
                </h6>
              </div>
              <div class="card-body p-2">
                <div class="row g-1">
                  @foreach($class->students->take(6) as $student)
                    <div class="col-6">
                      <small class="text-truncate d-block">
                        <i class="bi bi-person-circle me-1"></i>{{ $student->first_name }} {{ $student->last_name }}
                      </small>
                    </div>
                  @endforeach
                  @if($class->students->count() > 6)
                    <div class="col-12">
                      <small class="text-muted">... and {{ $class->students->count() - 6 }} more</small>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endif
      @endforeach
    </div>
    @if($stats['students'] > 0)
      <div class="mt-3">
        <a href="{{ route('tenant.teacher.students.index') }}" class="btn btn-outline-success btn-sm">
          <i class="bi bi-people me-1"></i>View All Students
        </a>
      </div>
    @endif
  </div>
</div>
@endif

<!-- Today's Schedule & Recent Activity -->
<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-calendar-check text-primary me-2"></i>Today's Schedule
                </h5>
                <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
            </div>
            <div class="card-body">
                @if(isset($todaySchedule) && $todaySchedule->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-clock me-1"></i>Time</th>
                                    <th><i class="bi bi-book me-1"></i>Subject</th>
                                    <th><i class="bi bi-diagram-3 me-1"></i>Class</th>
                                    <th><i class="bi bi-door-open me-1"></i>Room</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaySchedule as $schedule)
                                @php
                                    $now = now();
                                    $startTime = \Carbon\Carbon::parse($schedule->start_time);
                                    $endTime = \Carbon\Carbon::parse($schedule->end_time);
                                    $isOngoing = $now->between($startTime, $endTime);
                                    $isPast = $now->greaterThan($endTime);
                                    $isUpcoming = $now->lessThan($startTime);
                                @endphp
                                <tr class="{{ $isOngoing ? 'table-primary' : ($isPast ? 'text-muted' : '') }}">
                                    <td class="fw-medium">
                                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                    </td>
                                    <td>
                                        <strong>{{ $schedule->subject->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $schedule->schoolClass->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $schedule->room ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if($isOngoing)
                                            <span class="badge bg-success">
                                                <i class="bi bi-broadcast"></i> Ongoing
                                            </span>
                                        @elseif($isPast)
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-check-circle"></i> Completed
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="bi bi-clock-history"></i> Upcoming
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="text-muted mb-1 fw-medium">No classes scheduled for today</p>
                        <small class="text-muted">Enjoy your free day or check your timetable</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-bell-fill text-warning me-2"></i>Recent Activity
                </h5>
                <small class="text-muted">Latest updates</small>
            </div>
            <div class="card-body">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="activity-list">
                        @foreach($recentActivities as $activity)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                    <i class="bi bi-circle-fill text-primary" style="font-size: 0.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small mb-1">{{ $activity->title }}</div>
                                <p class="text-muted small mb-1">{{ $activity->description }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="text-muted mb-1 fw-medium">No recent activity</p>
                        <small class="text-muted">Activity will appear here</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection