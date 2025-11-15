@extends('tenant.layouts.app')

@section('title', 'Late Quiz Submissions')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Late Quiz Submissions</h1>
        <a
            href="{{ route('admin.reports.late-submissions.export') }}?{{ http_build_query(request()->query()) }}"
            class="btn btn-sm btn-outline-primary">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.late-submissions') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" value="{{ optional($dateFrom)->toDateString() }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" value="{{ optional($dateTo)->toDateString() }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quiz</label>
                    <select name="quiz_id" class="form-select">
                        <option value="">All Quizzes</option>
                        @foreach($quizzes as $q)
                        <option value="{{ $q->id }}" {{ (string)$quizId === (string)$q->id ? 'selected' : '' }}>{{ $q->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <input type="text" name="student_q" value="{{ request('student_q','') }}" class="form-control" placeholder="Name or ID" />
                </div>
                <div class="col-12">
                    <button class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                    <a href="{{ route('admin.reports.late-submissions') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Attempt ID</th>
                            <th>Submitted At</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Quiz</th>
                            <th>Teacher</th>
                            <th>Quiz End</th>
                            <th>Minutes Late</th>
                            <th>Scores (A/M/T)</th>
                            <th>Links</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $a)
                        <tr>
                            <td>#{{ $a->id }}</td>
                            <td>{{ optional($a->submitted_at)->format('Y-m-d H:i:s') }}</td>
                            <td>{{ optional($a->student)->name }}</td>
                            <td>{{ optional($a->quiz->schoolClass ?? null)->name }}</td>
                            <td>{{ optional($a->quiz)->title }}</td>
                            <td>{{ optional($a->quiz->teacher ?? null)->name }}</td>
                            <td>{{ optional($a->quiz->end_at ?? null)->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <span class="badge bg-danger">{{ $a->minutes_late }}</span>
                            </td>
                            <td>{{ (int)$a->score_auto }} / {{ (int)$a->score_manual }} / {{ (int)$a->score_total }}</td>
                            <td class="text-nowrap">
                                <span class="text-muted small">Quiz #{{ $a->quiz->id ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No late submissions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                {{ $attempts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
