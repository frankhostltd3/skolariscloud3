@extends('layouts.dashboard-teacher')

@section('title', 'Grade Exam Submission')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-journal-check me-2 text-primary"></i>Grade Submission
                </h1>
                <p class="text-muted mb-0">Assess an individual learner's responses and leave feedback.</p>
            </div>
            <a href="{{ route('tenant.teacher.classroom.exams.grading') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Grading Queue
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-secondary mb-0" role="alert">
                    <i class="bi bi-layout-text-sidebar me-2"></i>
                    The detailed grading interface is still being developed. This stub keeps navigation intact while the
                    feature set is finalized.
                </div>
            </div>
        </div>
    </div>
@endsection
