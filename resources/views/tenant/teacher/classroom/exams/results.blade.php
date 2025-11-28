@extends('layouts.dashboard-teacher')

@section('title', 'Exam Results')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-bar-chart-line me-2 text-info"></i>Exam Results
                </h1>
                <p class="text-muted mb-0">Review performance analytics for completed exams.</p>
            </div>
            <a href="{{ route('tenant.teacher.classroom.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Exams
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info mb-0" role="alert">
                    <i class="bi bi-graph-up me-2"></i>
                    Detailed exam analytics will appear here when implemented. The placeholder avoids missing view errors.
                </div>
            </div>
        </div>
    </div>
@endsection
