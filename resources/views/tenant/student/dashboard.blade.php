@extends('layouts.tenant.student')

@section('title', 'Student Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    @php $studentAvatar = auth()->user()->profile_photo_url; @endphp
                    <div class="d-flex align-items-center gap-3">
                        @if ($studentAvatar)
                            <img src="{{ $studentAvatar }}" alt="{{ auth()->user()->name }}" class="rounded-circle border"
                                width="64" height="64" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white border-3"
                                style="width: 64px; height: 64px; font-size: 28px; background: linear-gradient(135deg, var(--student-secondary) 0%, var(--student-accent) 100%);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Welcome back, {{ auth()->user()->name ?? 'Student' }}! 📚
                            </h2>
                            <p class="text-muted mb-0">Here's your academic overview for today</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">{{ date('l, F j, Y') }}</div>
                        <div class="fw-bold" style="color: var(--student-primary);">{{ date('g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($registrationTimeline))
            <div class="row mb-4">
                <div class="col-12">
                    <x-registration.pipeline context="student" :stages="$registrationTimeline['stages']" :summary="$registrationTimeline['summary']" :mode="$registrationTimeline['mode'] ?? null"
                        :next-action="$registrationTimeline['next_action'] ?? null" />
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Total Subjects</h6>
                                <h3 class="mb-0">{{ $stats['total_subjects'] }}</h3>
                                <small class="opacity-75">This semester</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-book fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Enrolled Class</h6>
                                <h3 class="mb-0 small">{{ $stats['enrolled_class'] }}</h3>
                                <small class="opacity-75">Current class</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Total Grades</h6>
                                <h3 class="mb-0">{{ $stats['total_grades'] }}</h3>
                                <small class="opacity-75">Recorded grades</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-star fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Average Grade</h6>
                                <h3 class="mb-0">{{ number_format($averageGrade, 1) }}%</h3>
                                <small class="opacity-75">Overall performance</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                        @if ($averageGrade > 0)
                            <div class="progress mt-2">
                                <div class="progress-bar" style="width: {{ $averageGrade }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming + Continue -->
        <div class="row mb-4">
            <!-- Upcoming -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-hourglass-start me-2"></i>Upcoming</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar-day text-primary me-2"></i>
                                        <strong>Next Class Today</strong>
                                    </div>
                                    @if ($upcoming['next_class'])
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $upcoming['next_class']->subject->name ?? 'Subject' }}</div>
                                                <small
                                                    class="text-muted">{{ $upcoming['next_class']->class->name ?? 'Class' }}
                                                    @if ($upcoming['next_class']->stream)
                                                        • {{ $upcoming['next_class']->stream->name }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-primary">{{ \Illuminate\Support\Str::of($upcoming['next_class']->starts_at)->substr(0, 5) }}
                                                    -
                                                    {{ \Illuminate\Support\Str::of($upcoming['next_class']->ends_at)->substr(0, 5) }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted">No more classes today</div>
                                    @endif
                                    <div class="mt-3 text-end">
                                        <a href="{{ route('tenant.student.schedule.index') }}"
                                            class="btn btn-sm btn-outline-primary">View timetable</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-tasks text-primary me-2"></i>
                                        <strong>Upcoming Assignments</strong>
                                    </div>
                                    @forelse($upcoming['assignments'] as $a)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="fw-semibold">{{ $a->title }}</div>
                                                <small class="text-muted">{{ $a->subject->name ?? 'Subject' }} • Due
                                                    {{ optional($a->due_date)->format('M j, g:i A') }}</small>
                                            </div>
                                            <a href="{{ route('tenant.student.classroom.exercises.index') }}"
                                                class="btn btn-sm btn-outline-primary">Open</a>
                                        </div>
                                    @empty
                                        <div class="text-muted">No pending assignments</div>
                                    @endforelse
                                    <div class="mt-3 text-end">
                                        <a href="{{ route('tenant.student.classroom.exercises.index') }}"
                                            class="btn btn-sm btn-outline-primary">View all assignments</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-clipboard-list text-primary me-2"></i>
                                        <strong>Scheduled Quizzes</strong>
                                    </div>
                                    @forelse($upcoming['quizzes'] as $q)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="fw-semibold">{{ $q->title }}</div>
                                                @php $from = $q->available_from; @endphp
                                                <small class="text-muted">
                                                    @if ($from)
                                                        Opens {{ $from->format('M j, g:i A') }}
                                                    @else
                                                        Available
                                                    @endif
                                                </small>
                                            </div>
                                            <a href="{{ route('tenant.student.quizzes.index') }}"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    @empty
                                        <div class="text-muted">No upcoming quizzes</div>
                                    @endforelse
                                    <div class="mt-3 text-end">
                                        <a href="{{ route('tenant.student.quizzes.index') }}"
                                            class="btn btn-sm btn-outline-primary">View all quizzes</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-file-signature text-primary me-2"></i>
                                        <strong>Scheduled Exams</strong>
                                    </div>
                                    @forelse($upcoming['exams'] as $e)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="fw-semibold">{{ $e->title }}</div>
                                                <small class="text-muted">
                                                    @if ($e->start_time)
                                                        Starts {{ $e->start_time->format('M j, g:i A') }}
                                                    @else
                                                        {{ optional($e->exam_date)->format('M j') }}
                                                    @endif
                                                </small>
                                            </div>
                                            <a href="{{ route('tenant.student.quizzes.index') }}"
                                                class="btn btn-sm btn-outline-primary">Details</a>
                                        </div>
                                    @empty
                                        <div class="text-muted">No scheduled exams</div>
                                    @endforelse
                                    <div class="mt-3 text-end">
                                        <a href="{{ route('tenant.student.quizzes.index') }}"
                                            class="btn btn-sm btn-outline-primary">View all exams</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continue + Unread -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-play-circle me-2"></i>Continue where you left off</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                <strong>Last Material</strong>
                            </div>
                            @if ($continue['material'])
                                <div class="small">
                                    <div class="fw-semibold">{{ $continue['material']->title }}</div>
                                    <div class="text-muted">{{ $continue['material']->subject->name ?? 'Subject' }} •
                                        {{ $continue['material']->class->name ?? 'Class' }}</div>
                                    <a href="{{ route('tenant.student.classroom.materials.show', $continue['material']->id) }}"
                                        class="btn btn-sm btn-outline-primary mt-2">Resume</a>
                                </div>
                            @else
                                <div class="text-muted">No recent material</div>
                            @endif
                        </div>
                        <hr>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-pen text-primary me-2"></i>
                                <strong>Quiz in progress</strong>
                            </div>
                            @if ($continue['quiz_attempt'])
                                <div class="small">
                                    <div class="fw-semibold">{{ $continue['quiz_attempt']->quiz->title }}</div>
                                    <a href="{{ route('tenant.student.quizzes.take', $continue['quiz_attempt']->quiz) }}"
                                        class="btn btn-sm btn-outline-primary mt-2">Resume</a>
                                </div>
                            @else
                                <div class="text-muted">No active quiz attempts</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>At a glance</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <div><i class="fas fa-envelope-open-text text-primary me-2"></i>Unread Messages</div>
                            <div><span class="badge bg-primary">{{ $unreadCounts['messages'] ?? 0 }}</span></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div><i class="fas fa-bell text-primary me-2"></i>Unread Notifications</div>
                            <div><span class="badge bg-primary">{{ $unreadCounts['notifications'] ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- My Subjects -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>My Subjects
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($mySubjects->count() > 0)
                            <div class="row">
                                @foreach ($mySubjects as $subject)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <div class="me-3">
                                                <i class="fas fa-book-open fa-2x"
                                                    style="color: var(--student-primary);"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $subject->name }}</h6>
                                                <small class="text-muted">{{ $subject->code }}</small>
                                                <div class="small">
                                                    <span class="badge bg-primary">{{ $subject->credit_hours ?? 3 }}
                                                        Credits</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-book-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No subjects enrolled yet</p>
                                @if (!$enrollment)
                                    <small class="text-muted">Please contact administration for class enrollment</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Grades -->
            <div class="col-xl-4 col-lg-5">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('tenant.student.assignments.index') }}" class="btn btn-student">
                                <i class="fas fa-plus me-2"></i>Submit Assignment
                            </a>
                            <a href="{{ route('tenant.finance.payments.pay') }}" class="btn btn-student">
                                <i class="fas fa-credit-card me-2"></i>Pay Fees Online
                            </a>
                            <a href="{{ route('tenant.student.attendance.index') }}" class="btn btn-student">
                                <i class="fas fa-calendar-check me-2"></i>View Attendance
                            </a>
                            <a href="{{ route('tenant.student.academic') }}" class="btn btn-student">
                                <i class="fas fa-star me-2"></i>Check Grades
                            </a>
                            <a href="{{ route('tenant.student.schedule.index') }}" class="btn btn-student">
                                <i class="fas fa-download me-2"></i>Download Timetable
                            </a>
                            <a href="{{ route('tenant.student.library.index') }}" class="btn btn-student">
                                <i class="fas fa-book me-2"></i>Visit Library
                            </a>
                            <a href="{{ route('tenant.student.forums.index') }}" class="btn btn-student">
                                <i class="fas fa-comments me-2"></i>Discussion Forums
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Grades -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Recent Grades
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($recentGrades->count() > 0)
                            @foreach ($recentGrades as $grade)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-bold">{{ $grade->subject->name }}</div>
                                        <small class="text-muted">{{ $grade->created_at->format('M j, Y') }}</small>
                                        @if ($grade->semester)
                                            <div><small class="badge bg-info">{{ $grade->semester->name }}</small></div>
                                        @endif
                                    </div>
                                    <div>
                                        <span
                                            class="badge
                                @if ($grade->grade >= 90) bg-success
                                @elseif($grade->grade >= 80) bg-primary
                                @elseif($grade->grade >= 70) bg-warning
                                @else bg-danger @endif
                                fs-6">{{ $grade->grade }}%</span>
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr class="my-2">
                                @endif
                            @endforeach

                            <div class="text-center mt-3">
                                <a href="{{ route('tenant.student.academic') }}"
                                    class="btn btn-outline-primary btn-sm">View All Grades</a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-star fa-2x text-muted mb-2"></i>
                                <p class="text-muted small mb-0">No grades recorded yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Info & Performance Overview -->
        <div class="row mt-4">
            <!-- Class Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Class Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($enrollment && $enrollment->classroom)
                            <div class="mb-3">
                                <h6 class="fw-bold">{{ $enrollment->classroom->name }}</h6>
                                <p class="text-muted mb-2">
                                    {{ $enrollment->classroom->description ?? 'Class description not available' }}</p>

                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="mb-0 text-primary">{{ $enrollment->classroom->capacity ?? 'N/A' }}
                                            </h4>
                                            <small class="text-muted">Capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="mb-0 text-success">{{ $enrollment->classroom->enrollments->count() }}
                                        </h4>
                                        <small class="text-muted">Enrolled</small>
                                    </div>
                                </div>
                            </div>

                            @if ($enrollment->classroom->teacher)
                                <div class="p-3 bg-light rounded">
                                    <h6 class="mb-1">Class Teacher</h6>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie me-2 text-primary"></i>
                                        <span class="fw-bold">{{ $enrollment->classroom->teacher->name }}</span>
                                    </div>
                                    <small class="text-muted">{{ $enrollment->classroom->teacher->email }}</small>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Not enrolled in any class</p>
                                <small class="text-muted">Please contact administration for enrollment</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Performance Overview -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Grade Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (array_sum($gradeDistribution) > 0)
                            <!-- Average Grade -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Average Grade</span>
                                    <span
                                        class="badge
                                @if ($averageGrade >= 90) bg-success
                                @elseif($averageGrade >= 80) bg-primary
                                @elseif($averageGrade >= 70) bg-warning
                                @else bg-danger @endif
                                fs-6">{{ number_format($averageGrade, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar
                                @if ($averageGrade >= 90) bg-success
                                @elseif($averageGrade >= 80) bg-primary
                                @elseif($averageGrade >= 70) bg-warning
                                @else bg-danger @endif"
                                        style="width: {{ $averageGrade }}%"></div>
                                </div>
                            </div>

                            <!-- Grade Distribution -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-3">Grade Distribution</h6>

                                @foreach (['A' => ['min' => 90, 'color' => 'success'], 'B' => ['min' => 80, 'color' => 'primary'], 'C' => ['min' => 70, 'color' => 'info'], 'D' => ['min' => 60, 'color' => 'warning'], 'F' => ['min' => 0, 'color' => 'danger']] as $letter => $config)
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Grade {{ $letter }} ({{ $config['min'] }}%+)</small>
                                            <small class="fw-bold">{{ $gradeDistribution[$letter] ?? 0 }} grades</small>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-{{ $config['color'] }}"
                                                style="width: {{ array_sum($gradeDistribution) > 0 ? (($gradeDistribution[$letter] ?? 0) / array_sum($gradeDistribution)) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No grades recorded yet</p>
                                <small class="text-muted">Grades will appear here once recorded</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookstore Widget -->
        <div class="row">
            <div class="col-12">
                @include('tenant.components.bookstore.student-widget')
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .progress-sm {
            height: 4px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--student-primary);
        }

        .badge {
            font-size: 0.75em;
        }

        .card-body .btn {
            transition: all 0.3s ease;
        }

        .card-body .btn:hover {
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Auto-refresh time
        setInterval(function() {
            const now = new Date();
            const timeElement = document.querySelector('.fw-bold[style*="color: var(--student-primary)"]');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            }
        }, 60000);

        // Add some interactivity to progress bars
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                    bar.style.transition = 'width 1s ease-in-out';
                }, 100);
            });
        });
    </script>
@endsection
