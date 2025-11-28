@extends('layouts.dashboard-teacher')

@section('title', __('Class Details') . ' - ' . $class->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $class->name }}</h1>
            <p class="text-muted mb-0">
                {{ $class->code ?? '' }} 
                @if($class->academicYear)
                    | {{ $class->academicYear->name ?? '' }}
                @endif
            </p>
        </div>
        <a href="{{ route('tenant.teacher.classes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Classes') }}
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Students -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('Total Students') }}</h6>
                            <h3 class="mb-0">{{ $attendanceStats['total_students'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('Subjects') }}</h6>
                            <h3 class="mb-0">{{ $class->subjects->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded p-3">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('Attendance Rate') }}</h6>
                            <h3 class="mb-0">{{ number_format($attendanceStats['attendance_rate'], 1) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Grades -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                <i class="bi bi-trophy fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('Recent Grades') }}</h6>
                            <h3 class="mb-0">{{ $recentGrades->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Subjects Taught -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-book-half me-2"></i>{{ __('Subjects I Teach in This Class') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($class->subjects->count() > 0)
                        <div class="row g-3">
                            @foreach($class->subjects as $subject)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                                                <i class="bi bi-journal-text"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $subject->name }}</h6>
                                                <small class="text-muted">{{ $subject->code ?? '' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No subjects assigned yet.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Recent Grades -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>{{ __('Recent Grades') }}
                        </h5>
                        <a href="{{ route('tenant.teacher.classes.grades', $class) }}" class="btn btn-sm btn-outline-primary">
                            {{ __('View All') }} <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentGrades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Student') }}</th>
                                        <th>{{ __('Subject') }}</th>
                                        <th>{{ __('Score') }}</th>
                                        <th>{{ __('Grade') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentGrades as $grade)
                                        <tr>
                                            <td>{{ $grade->student->name ?? 'N/A' }}</td>
                                            <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                                            <td>{{ $grade->score ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $grade->grade_letter === 'A' ? 'success' : ($grade->grade_letter === 'F' ? 'danger' : 'warning') }}">
                                                    {{ $grade->grade_letter ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $grade->assessment_date ? $grade->assessment_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No grades recorded yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Students with Low Grades -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>{{ __('Students Needing Attention') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($studentsWithLowGrades->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($studentsWithLowGrades->take(5) as $student)
                                <div class="list-group-item px-0 border-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $student->name }}</h6>
                                            <small class="text-muted">
                                                {{ __('Avg:') }} {{ number_format($student->avg_percentage ?? 0, 1) }}%
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($studentsWithLowGrades->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('tenant.teacher.classes.students', $class) }}" class="btn btn-sm btn-outline-secondary">
                                    {{ __('View All') }}
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('All students are performing well!') }}</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>{{ __('Quick Actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.teacher.classes.students', $class) }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>{{ __('View Students') }}
                        </a>
                        <a href="{{ route('tenant.teacher.classes.grades', $class) }}" class="btn btn-outline-success">
                            <i class="bi bi-clipboard-data me-2"></i>{{ __('Manage Grades') }}
                        </a>
                        <a href="{{ route('tenant.teacher.attendance.take') }}" class="btn btn-outline-info">
                            <i class="bi bi-calendar-check me-2"></i>{{ __('Mark Attendance') }}
                        </a>
                        @if($class->class_teacher_id === Auth::id())
                            <a href="{{ route('tenant.teacher.timetable.edit', $class) }}" class="btn btn-outline-warning">
                                <i class="bi bi-gear me-2"></i>{{ __('Manage Timetable') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
