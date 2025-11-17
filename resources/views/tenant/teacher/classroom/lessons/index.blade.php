@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Lesson Plans')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-journal-text me-2 text-primary"></i>Lesson Plans
            </h1>
            <p class="text-muted mb-0">Create and manage your teaching lesson plans</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.lessons.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Lesson Plan
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
                <i class="bi bi-list me-2"></i>All Plans
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button">
                <i class="bi bi-file-text me-2"></i>Drafts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button">
                <i class="bi bi-check-circle me-2"></i>Published
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button">
                <i class="bi bi-bookmark me-2"></i>Templates
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="all">
            @if($lessonPlans->count() > 0)
                <div class="row g-4">
                    @foreach($lessonPlans as $plan)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-header bg-{{ $plan->status === 'published' ? 'success' : ($plan->status === 'completed' ? 'info' : 'secondary') }} text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">{{ $plan->subject->name ?? 'N/A' }}</span>
                                        <span class="badge bg-white text-dark">{{ $plan->status_label }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $plan->title }}</h5>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-calendar me-1"></i>{{ $plan->lesson_date ? $plan->lesson_date->format('M d, Y') : 'No date' }}
                                    </p>
                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-people me-1"></i>{{ $plan->class->name ?? 'N/A' }}
                                    </p>
                                    @if($plan->is_template)
                                        <span class="badge bg-primary mb-2">
                                            <i class="bi bi-bookmark me-1"></i>Template
                                        </span>
                                    @endif
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('tenant.teacher.classroom.lessons.show', $plan) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('tenant.teacher.classroom.lessons.edit', $plan) }}" class="btn btn-sm btn-outline-secondary flex-fill">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $lessonPlans->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Lesson Plans Yet</h5>
                        <p class="text-muted mb-4">Create your first lesson plan to get started</p>
                        <a href="{{ route('tenant.teacher.classroom.lessons.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Lesson Plan
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="tab-pane fade" id="draft">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-text text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Draft Lesson Plans</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="published">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Published Lesson Plans</h5>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="templates">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bookmark text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Templates Saved</h5>
                    <p class="text-muted">Save lesson plans as templates for reuse</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

