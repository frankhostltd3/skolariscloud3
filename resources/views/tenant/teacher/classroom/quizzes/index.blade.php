@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Quizzes')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-question-circle me-2 text-primary"></i>Quizzes
            </h1>
            <p class="text-muted mb-0">Create interactive quizzes with auto-grading</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.quizzes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Quiz
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
                <i class="bi bi-list me-2"></i>All Quizzes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">
                <i class="bi bi-play-circle me-2"></i>Active
                <span class="badge bg-success ms-1">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button">
                <i class="bi bi-file-text me-2"></i>Drafts
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
                    <i class="bi bi-question-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Quizzes Created</h5>
                    <p class="text-muted mb-4">Create your first quiz to assess student understanding</p>
                    <a href="{{ route('tenant.teacher.classroom.quizzes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Quiz
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="active">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-play-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Active Quizzes</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="draft">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-text text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Draft Quizzes</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="completed">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Completed Quizzes</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mt-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-question-circle text-primary" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Total Quizzes</small>
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
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-percent text-info" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0%</h4>
                    <small class="text-muted">Avg. Score</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-clipboard-check text-warning" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Questions Bank</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

