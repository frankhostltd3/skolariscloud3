@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Online Exams')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-file-earmark-check me-2 text-primary"></i>Online Exams
            </h1>
            <p class="text-muted mb-0">Set, conduct, and grade secure online examinations</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.exams.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Exam
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                <i class="bi bi-list me-2"></i>All Exams
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                <i class="bi bi-calendar-event me-2"></i>Upcoming
                <span class="badge bg-primary ms-1">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">
                <i class="bi bi-play-circle me-2"></i>Active
                <span class="badge bg-success ms-1">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="grading-tab" data-bs-toggle="tab" data-bs-target="#grading" type="button">
                <i class="bi bi-pencil-square me-2"></i>Pending Grading
                <span class="badge bg-warning ms-1">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                <i class="bi bi-check-circle me-2"></i>Completed
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="all">
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Exams Created</h5>
                    <p class="text-muted mb-4">Create your first online exam with proctoring features</p>
                    <a href="{{ route('tenant.teacher.classroom.exams.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Exam
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="upcoming">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Upcoming Exams</h5>
                    <p class="text-muted">Schedule exams for future dates</p>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="active">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-play-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Active Exams</h5>
                    <p class="text-muted">Active exams will appear here with live monitoring</p>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="grading">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-pencil-square text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Exams Pending Grading</h5>
                    <p class="text-muted">Exams requiring manual grading will appear here</p>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="completed">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Completed Exams</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mt-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-file-earmark-check text-primary" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Total Exams</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Total Attempts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Violations Detected</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-percent text-info" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0%</h4>
                    <small class="text-muted">Avg. Score</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Features Info -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-shield-check me-2 text-success"></i>Security & Anti-Cheating Features
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-camera-video text-primary me-2"></i>
                                <span class="small">Webcam Proctoring</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-window-x text-danger me-2"></i>
                                <span class="small">Tab Switch Detection</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-clipboard-x text-warning me-2"></i>
                                <span class="small">Copy/Paste Blocking</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-shuffle text-info me-2"></i>
                                <span class="small">Question Shuffling</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

