@extends('layouts.dashboard-teacher')

@section('title', 'Edit Virtual Class')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.teacher.classroom.index') }}">Classroom</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant.teacher.classroom.virtual.index') }}">Virtual
                            Classes</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.teacher.classroom.virtual.show', $class) }}">{{ $class->title }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>Edit Virtual Class
            </h2>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($class->status !== 'scheduled')
            <div class="alert alert-warning">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> This class is {{ $class->status }}. You can only edit scheduled classes before they
                start.
            </div>
        @endif

        <div class="row g-4">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('tenant.teacher.classroom.virtual.update', $class) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Basic Information -->
                            <h5 class="mb-3">
                                <i class="bi bi-info-circle me-2"></i>Basic Information
                            </h5>

                            <div class="mb-3">
                                <label for="title" class="form-label">Class Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $class->title) }}"
                                    placeholder="e.g., Introduction to Algebra" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="Brief description of what will be covered in this class">{{ old('description', $class->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $schoolClass)
                                            <option value="{{ $schoolClass->id }}"
                                                {{ old('class_id', $class->grade_id) == $schoolClass->id ? 'selected' : '' }}>
                                                {{ $schoolClass->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $class->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Platform Settings -->
                            <h5 class="mb-3">
                                <i class="bi bi-camera-video me-2"></i>Platform Settings
                            </h5>

                            <div class="mb-3">
                                <label for="platform" class="form-label">Meeting Platform <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('platform') is-invalid @enderror" id="platform"
                                    name="platform" required>
                                    <option value="">Select Platform</option>
                                    <option value="zoom"
                                        {{ old('platform', $class->platform) == 'zoom' ? 'selected' : '' }}>
                                        Zoom
                                    </option>
                                    <option value="google_meet"
                                        {{ old('platform', $class->platform) == 'google_meet' ? 'selected' : '' }}>
                                        Google Meet
                                    </option>
                                    <option value="microsoft_teams"
                                        {{ old('platform', $class->platform) == 'microsoft_teams' ? 'selected' : '' }}>
                                        Microsoft Teams
                                    </option>
                                    <option value="youtube"
                                        {{ old('platform', $class->platform) == 'youtube' ? 'selected' : '' }}>
                                        YouTube Live
                                    </option>
                                    <option value="other"
                                        {{ old('platform', $class->platform) == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                                @error('platform')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meeting_url" class="form-label">Meeting URL</label>
                                <input type="url" class="form-control @error('meeting_url') is-invalid @enderror"
                                    id="meeting_url" name="meeting_url"
                                    value="{{ old('meeting_url', $class->meeting_url) }}"
                                    placeholder="https://zoom.us/j/123456789">
                                <small class="form-text text-muted">Students will use this link to join the class</small>
                                @error('meeting_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="meeting_id" class="form-label">Meeting ID</label>
                                    <input type="text" class="form-control @error('meeting_id') is-invalid @enderror"
                                        id="meeting_id" name="meeting_id"
                                        value="{{ old('meeting_id', $class->meeting_id) }}" placeholder="123 456 789">
                                    @error('meeting_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="meeting_password" class="form-label">Meeting Password</label>
                                    <input type="text"
                                        class="form-control @error('meeting_password') is-invalid @enderror"
                                        id="meeting_password" name="meeting_password"
                                        value="{{ old('meeting_password', $class->meeting_password) }}"
                                        placeholder="Optional">
                                    @error('meeting_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Schedule Settings -->
                            <h5 class="mb-3">
                                <i class="bi bi-calendar-event me-2"></i>Schedule
                            </h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="scheduled_date" class="form-label">Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control @error('scheduled_date') is-invalid @enderror"
                                        id="scheduled_date" name="scheduled_date"
                                        value="{{ old('scheduled_date', $class->scheduled_at->format('Y-m-d')) }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="scheduled_time" class="form-label">Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time"
                                        class="form-control @error('scheduled_time') is-invalid @enderror"
                                        id="scheduled_time" name="scheduled_time"
                                        value="{{ old('scheduled_time', $class->scheduled_at->format('H:i')) }}" required>
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="duration_minutes" class="form-label">Duration (minutes) <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('duration_minutes') is-invalid @enderror"
                                    id="duration_minutes" name="duration_minutes" required>
                                    <option value="">Select Duration</option>
                                    <option value="15"
                                        {{ old('duration_minutes', $class->duration) == 15 ? 'selected' : '' }}>15 minutes
                                    </option>
                                    <option value="30"
                                        {{ old('duration_minutes', $class->duration) == 30 ? 'selected' : '' }}>30 minutes
                                    </option>
                                    <option value="45"
                                        {{ old('duration_minutes', $class->duration) == 45 ? 'selected' : '' }}>45 minutes
                                    </option>
                                    <option value="60"
                                        {{ old('duration_minutes', $class->duration) == 60 ? 'selected' : '' }}>1 hour
                                    </option>
                                    <option value="90"
                                        {{ old('duration_minutes', $class->duration) == 90 ? 'selected' : '' }}>1.5 hours
                                    </option>
                                    <option value="120"
                                        {{ old('duration_minutes', $class->duration) == 120 ? 'selected' : '' }}>2 hours
                                    </option>
                                    <option value="180"
                                        {{ old('duration_minutes', $class->duration) == 180 ? 'selected' : '' }}>3 hours
                                    </option>
                                </select>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Additional Settings -->
                            <h5 class="mb-3">
                                <i class="bi bi-gear me-2"></i>Additional Settings
                            </h5>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_record" name="auto_record"
                                        value="1" {{ old('auto_record', $class->auto_record) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_record">
                                        <strong>Auto-record class</strong>
                                        <small class="d-block text-muted">Automatically record this virtual class</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center pt-3">
                                <a href="{{ route('tenant.teacher.classroom.virtual.show', $class) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-check-circle me-1"></i> Update Class
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Current Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Current Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Status</small>
                            <span
                                class="badge bg-{{ $class->status === 'ongoing' ? 'success' : ($class->status === 'completed' ? 'secondary' : ($class->status === 'cancelled' ? 'danger' : 'primary')) }} px-3 py-2">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                                {{ ucfirst($class->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Created</small>
                            <strong>{{ $class->created_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        @if ($class->updated_at != $class->created_at)
                            <div>
                                <small class="text-muted d-block mb-1">Last Updated</small>
                                <strong>{{ $class->updated_at->format('M d, Y h:i A') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-lightbulb text-warning me-2"></i>Tips
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">Test your meeting link before the scheduled time</li>
                            <li class="mb-2">Send reminder notifications to students 15 minutes before class</li>
                            <li class="mb-2">Prepare materials and share them before the class</li>
                            <li class="mb-2">Record the session for students who miss the live class</li>
                            <li class="mb-0">Take attendance during or after the class</li>
                        </ul>
                    </div>
                </div>

                <!-- Warning Card -->
                @if ($class->status !== 'scheduled')
                    <div class="card border-danger mb-4">
                        <div class="card-header bg-danger text-white py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Important
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">
                                This class is <strong>{{ $class->status }}</strong>.
                                Changes may not affect the class as it has already
                                {{ $class->status === 'ongoing' ? 'started' : 'ended' }}.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Platform Help -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-question-circle me-2"></i>Platform Help
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="d-block mb-1">Zoom</strong>
                            <small class="text-muted">
                                Meeting URL format: https://zoom.us/j/[meeting-id]
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong class="d-block mb-1">Google Meet</strong>
                            <small class="text-muted">
                                Meeting URL format: https://meet.google.com/xxx-xxxx-xxx
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong class="d-block mb-1">Microsoft Teams</strong>
                            <small class="text-muted">
                                Copy the full Teams meeting link from your invitation
                            </small>
                        </div>
                        <div>
                            <strong class="d-block mb-1">YouTube Live</strong>
                            <small class="text-muted">
                                Use your YouTube Live streaming URL
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            });
        });
    </script>
@endsection
