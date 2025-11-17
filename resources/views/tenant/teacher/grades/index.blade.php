@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">My Grades</h1>
            <p class="text-muted mb-0">Manage and view grades you've entered</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.grades.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Grade
            </a>
            <a href="{{ route('tenant.teacher.grades.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.teacher.grades.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select name="class_id" id="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="assessment_type" class="form-label">Assessment Type</label>
                        <select name="assessment_type" id="assessment_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="quiz" {{ $assessmentType == 'quiz' ? 'selected' : '' }}>Quiz</option>
                            <option value="test" {{ $assessmentType == 'test' ? 'selected' : '' }}>Test</option>
                            <option value="exam" {{ $assessmentType == 'exam' ? 'selected' : '' }}>Exam</option>
                            <option value="assignment" {{ $assessmentType == 'assignment' ? 'selected' : '' }}>Assignment</option>
                            <option value="project" {{ $assessmentType == 'project' ? 'selected' : '' }}>Project</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="q" class="form-label">Search Student</label>
                        <input type="text" name="q" id="q" class="form-control" placeholder="Name or email" value="{{ $studentQ }}">
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('tenant.teacher.grades.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Grades Table -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>Grades List
                        <span class="badge bg-primary ms-2">{{ $grades->total() }} total</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($grades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Assessment</th>
                                        <th>Type</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grades as $grade)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($grade->student && $grade->student->photo)
                                                        <img src="{{ asset('storage/' . $grade->student->photo) }}" 
                                                             alt="{{ $grade->student->name }}" 
                                                             class="rounded-circle me-2" 
                                                             width="32" height="32">
                                                    @else
                                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 32px; height: 32px; font-size: 14px;">
                                                            {{ $grade->student ? strtoupper(substr($grade->student->name, 0, 1)) : '?' }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $grade->student->name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $grade->class->name ?? 'N/A' }} {{ $grade->class->section ?? '' }}
                                                </span>
                                            </td>
                                            <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                                            <td>{{ $grade->assessment_name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($grade->assessment_type) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $percentage = $grade->total_marks > 0 ? round(($grade->marks_obtained / $grade->total_marks) * 100, 2) : 0;
                                                    $badgeClass = $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">
                                                    {{ $grade->marks_obtained }}/{{ $grade->total_marks }}
                                                    ({{ $percentage }}%)
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $grade->assessment_date ? $grade->assessment_date->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $grades->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No grades found matching your filters.</p>
                            <p class="text-muted small">Try adjusting your filters or add new grades.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="col-lg-4">
            <!-- Subject Distribution -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Grades by Subject</h6>
                </div>
                <div class="card-body">
                    @if(count($chartLabels) > 0)
                        <canvas id="subjectChart" height="200"></canvas>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-graph-down" style="font-size: 2rem;"></i>
                            <p class="small mt-2 mb-0">No data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assessment Type Distribution -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Assessment Types</h6>
                </div>
                <div class="card-body">
                    @if(count($typeLabels) > 0)
                        <canvas id="typeChart" height="200"></canvas>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-graph-down" style="font-size: 2rem;"></i>
                            <p class="small mt-2 mb-0">No data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total Grades:</span>
                        <span class="fw-bold fs-5">{{ $grades->total() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Classes:</span>
                        <span class="fw-bold">{{ $classes->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Subjects:</span>
                        <span class="fw-bold">{{ $subjects->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Subject Distribution Chart
    @if(count($chartLabels) > 0)
    const subjectCtx = document.getElementById('subjectChart');
    if (subjectCtx) {
        new Chart(subjectCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    data: @json($chartCounts),
                    backgroundColor: [
                        '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                        '#fd7e14', '#20c997', '#0dcaf0', '#d63384', '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Assessment Type Chart
    @if(count($typeLabels) > 0)
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: @json($typeLabels),
                datasets: [{
                    label: 'Count',
                    data: @json($typeValues),
                    backgroundColor: '#0d6efd',
                    borderColor: '#0a58ca',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush
@endsection

