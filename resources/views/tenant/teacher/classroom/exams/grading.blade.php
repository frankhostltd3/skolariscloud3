@extends('layouts.dashboard-teacher')

@section('title', 'Exam Grading')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-clipboard-check me-2 text-success"></i>Exam Grading
                </h1>
                <p class="text-muted mb-0">Grade submissions and track student progress for online exams.</p>
            </div>
            <a href="{{ route('tenant.teacher.classroom.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Exams
            </a>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-info mb-0" role="alert">
                            <i class="bi bi-gear me-2"></i>
                            Detailed grading workflows are coming soon. This placeholder view keeps the menu link
                            functional.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
