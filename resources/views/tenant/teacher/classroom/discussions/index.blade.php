@extends('layouts.dashboard-teacher')

@section('title', 'Class Discussions')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-chat-left-text me-2 text-primary"></i>Class Discussions
            </h1>
            <p class="text-muted mb-0">Moderate discussions and engage with students</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.discussions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Start New Discussion
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
                <i class="bi bi-list me-2"></i>All Discussions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">
                <i class="bi bi-chat-dots me-2"></i>Active
                <span class="badge bg-success ms-1">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button">
                <i class="bi bi-question-circle me-2"></i>Questions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button">
                <i class="bi bi-megaphone me-2"></i>Announcements
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pinned-tab" data-bs-toggle="tab" data-bs-target="#pinned" type="button">
                <i class="bi bi-pin-angle me-2"></i>Pinned
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="all">
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-left-text-fill text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Discussions Yet</h5>
                    <p class="text-muted mb-4">Start a discussion to engage with your students</p>
                    <a href="{{ route('tenant.teacher.classroom.discussions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Start First Discussion
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="active">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Active Discussions</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="questions">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-question-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Student Questions</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="announcements">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-megaphone text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Announcements</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pinned">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-pin-angle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Pinned Discussions</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mt-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-chat-left-text text-primary" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Total Discussions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-chat-dots text-success" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Active Threads</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-reply text-info" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Total Replies</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-people text-warning" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Participants</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
