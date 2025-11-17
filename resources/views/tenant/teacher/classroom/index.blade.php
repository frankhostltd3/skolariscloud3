@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Online Classroom')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-laptop me-2 text-primary"></i>Online Classroom
            </h1>
            <p class="text-muted mb-0">Manage virtual classes, materials, assignments, and more</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickActionModal">
                <i class="bi bi-plus-circle me-2"></i>Quick Action
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Virtual Classes</h6>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> 0 upcoming</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-camera-video-fill" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-primary-subtle">
                    <a href="{{ route('tenant.teacher.classroom.virtual.index') }}" class="text-primary text-decoration-none d-block">
                        View all <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Learning Materials</h6>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <small class="text-info"><i class="bi bi-eye"></i> 0 views today</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-file-earmark-text-fill" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success-subtle">
                    <a href="{{ route('tenant.teacher.classroom.materials.index') }}" class="text-success text-decoration-none d-block">
                        Manage materials <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Assignments</h6>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <small class="text-danger"><i class="bi bi-clock"></i> 0 pending grading</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-clipboard-check-fill" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning-subtle">
                    <a href="{{ route('tenant.teacher.classroom.exercises.index') }}" class="text-warning text-decoration-none d-block">
                        View assignments <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Quizzes & Exams</h6>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <small class="text-primary"><i class="bi bi-people"></i> 0 attempts</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-list-check" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-info-subtle">
                    <a href="{{ route('tenant.teacher.classroom.quizzes.index') }}" class="text-info text-decoration-none d-block">
                        Manage quizzes <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Features Grid -->
    <div class="row g-4 mb-4">
        <!-- Virtual Classes -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-camera-video me-2"></i>Virtual Classes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Schedule and conduct live online classes via Zoom, Google Meet, or YouTube Live</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.virtual.create') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle me-2"></i>Schedule Class
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.virtual.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-list-ul me-2"></i>View All
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Zoom Integration</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Google Meet Support</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Auto Recording</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Attendance Tracking</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lesson Plans -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-journal-text me-2"></i>Lesson Plans
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create structured lesson plans with objectives, activities, and assessments</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.lessons.create') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-plus-circle me-2"></i>New Lesson Plan
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.lessons.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-folder me-2"></i>My Plans
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Template Library</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Attach Resources</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Share with Students</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Track Progress</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Materials -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Learning Materials
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Share notes, documents, videos, and other learning resources with students</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.materials.create') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Material
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.materials.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-folder2-open me-2"></i>Library
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Documents & PDFs</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> YouTube Videos</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Images & Presentations</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Track Downloads</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exercises & Assignments -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Exercises & Assignments
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create assignments, collect submissions, and provide feedback to students</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.exercises.create') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-plus-circle me-2"></i>New Assignment
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.exercises.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-list-task me-2"></i>All Assignments
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Due Date Management</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> File Submissions</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Grading & Feedback</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Late Submission Tracking</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quizzes -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>Quizzes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create interactive quizzes with auto-grading and instant feedback</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.quizzes.create') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-plus-circle me-2"></i>Create Quiz
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.quizzes.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-list-check me-2"></i>My Quizzes
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Multiple Question Types</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Auto-Grading</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Time Limits</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Question Randomization</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discussions -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Class Discussions
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Foster engagement through moderated class discussions and forums</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.discussions.create') }}" class="btn btn-outline-dark w-100">
                                <i class="bi bi-plus-circle me-2"></i>Start Discussion
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('tenant.teacher.classroom.discussions.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-chat-square-text me-2"></i>All Discussions
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Threaded Replies</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Moderation Tools</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Pin Important Topics</small>
                        <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Rich Text & Attachments</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Online Exams -->
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard2-check me-2"></i>Online Exams
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create, conduct, and grade online exams with advanced proctoring and security features</p>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('tenant.teacher.classroom.exams.create') }}" class="btn btn-outline-danger w-100">
                                <i class="bi bi-plus-circle me-2"></i>Create Exam
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('tenant.teacher.classroom.exams.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-calendar-event me-2"></i>Scheduled Exams
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('tenant.teacher.classroom.exams.grading') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-pen me-2"></i>Grade Exams
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('tenant.teacher.classroom.exams.results') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-bar-chart me-2"></i>View Results
                            </a>
                        </div>
                    </div>
                    <div class="mt-3 row">
                        <div class="col-md-3">
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Multiple Question Types</small>
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Question Banks</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Auto & Manual Grading</small>
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Detailed Analytics</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Proctoring Options</small>
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Anti-Cheating Features</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> Time Management</small>
                            <small class="text-muted d-block"><i class="bi bi-check-circle text-success me-1"></i> PDF Report Generation</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>Recent Activity
            </h5>
        </div>
        <div class="card-body">
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">No recent activity. Start by creating your first online classroom content!</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Modal -->
<div class="modal fade" id="quickActionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <a href="{{ route('tenant.teacher.classroom.virtual.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-camera-video text-primary me-2"></i>Schedule Virtual Class
                    </a>
                    <a href="{{ route('tenant.teacher.classroom.materials.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-earmark-arrow-up text-success me-2"></i>Upload Learning Material
                    </a>
                    <a href="{{ route('tenant.teacher.classroom.exercises.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-clipboard-check text-warning me-2"></i>Create Assignment
                    </a>
                    <a href="{{ route('tenant.teacher.classroom.quizzes.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-question-circle text-info me-2"></i>Create Quiz
                    </a>
                    <a href="{{ route('tenant.teacher.classroom.lessons.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-journal-text text-secondary me-2"></i>New Lesson Plan
                    </a>
                    <a href="{{ route('tenant.teacher.classroom.discussions.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-chat-dots text-dark me-2"></i>Start Discussion
                    </a>
                    <a href="{{ route('tenant.teacher.classroom.exams.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-clipboard2-check text-danger me-2"></i>Create Online Exam
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endpush

