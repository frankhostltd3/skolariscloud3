@extends('layouts.dashboard-teacher')

@section('title', 'Schedule Virtual Class')

@section('content')
    @php($classes = $classes ?? collect())
    @php($subjects = $subjects ?? collect())
    @php($classesAvailable = $classesAvailable ?? true)
    @php($subjectsAvailable = $subjectsAvailable ?? true)

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-camera-video me-2 text-primary"></i>Schedule Virtual Class
                </h1>
                <p class="text-muted mb-0">Set up a new online class session</p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.virtual.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Virtual Classes
                </a>
            </div>
        </div>

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (!$classesAvailable || !$subjectsAvailable)
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                Classes or subjects are not yet available for this school. You can review the scheduling form, but the
                submit button remains disabled until both resources are configured.
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('tenant.teacher.classroom.virtual.store') }}" method="POST">
                            @csrf

                            <!-- Basic Information -->
                            <h5 class="border-bottom pb-2 mb-4">
                                <i class="bi bi-info-circle me-2"></i>Basic Information
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="title" class="form-label">Class Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title') }}"
                                        placeholder="e.g., Introduction to Algebra" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Brief description of what will be covered">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" {{ $classesAvailable ? 'required' : 'disabled' }}>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" {{ $subjectsAvailable ? 'required' : 'disabled' }}>
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Platform Settings -->
                            <h5 class="border-bottom pb-2 mb-4 mt-4">
                                <i class="bi bi-gear me-2"></i>Platform Settings
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="platform" class="form-label">Platform <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('platform') is-invalid @enderror" id="platform"
                                        name="platform" required>
                                        <option value="">Select Platform</option>
                                        <option value="zoom">Zoom</option>
                                        <option value="google_meet">Google Meet</option>
                                        <option value="microsoft_teams">Microsoft Teams</option>
                                        <option value="youtube">YouTube Live</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('platform')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="meeting_id" class="form-label">Meeting ID</label>
                                    <input type="text" class="form-control @error('meeting_id') is-invalid @enderror"
                                        id="meeting_id" name="meeting_id" value="{{ old('meeting_id') }}"
                                        placeholder="Auto-generated or enter manually">
                                    @error('meeting_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="meeting_password" class="form-label">Meeting Password</label>
                                    <input type="text"
                                        class="form-control @error('meeting_password') is-invalid @enderror"
                                        id="meeting_password" name="meeting_password"
                                        value="{{ old('meeting_password') }}" placeholder="Optional password">
                                    @error('meeting_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="meeting_url" class="form-label">Meeting URL</label>
                                    <input type="url" class="form-control @error('meeting_url') is-invalid @enderror"
                                        id="meeting_url" name="meeting_url" value="{{ old('meeting_url') }}"
                                        placeholder="https://zoom.us/j/123456789">
                                    @error('meeting_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Schedule -->
                            <h5 class="border-bottom pb-2 mb-4 mt-4">
                                <i class="bi bi-calendar me-2"></i>Schedule
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="scheduled_date" class="form-label">Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control @error('scheduled_date') is-invalid @enderror"
                                        id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}"
                                        required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="scheduled_time" class="form-label">Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time"
                                        class="form-control @error('scheduled_time') is-invalid @enderror"
                                        id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time') }}"
                                        required>
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="duration_minutes" class="form-label">Duration (minutes) <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('duration_minutes') is-invalid @enderror"
                                        id="duration_minutes" name="duration_minutes"
                                        value="{{ old('duration_minutes', 60) }}" min="15" max="300"
                                        required>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Options -->
                            <h5 class="border-bottom pb-2 mb-4 mt-4">
                                <i class="bi bi-toggles me-2"></i>Options
                            </h5>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="auto_record" name="auto_record"
                                    value="1">
                                <label class="form-check-label" for="auto_record">
                                    Auto-record this session
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring"
                                    value="1">
                                <label class="form-check-label" for="is_recurring">
                                    Make this a recurring class
                                </label>
                            </div>

                            <div id="recurring_options" style="display: none;" class="ms-4 mb-3">
                                <label class="form-label">Recurrence Pattern</label>
                                <select class="form-select mb-2" name="recurrence_pattern">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                <input type="date" class="form-control" name="recurrence_end_date"
                                    placeholder="End Date">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="send_notification"
                                    name="send_notification" value="1" checked>
                                <label class="form-check-label" for="send_notification">
                                    Send notification to students
                                </label>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tenant.teacher.classroom.virtual.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary"
                                    {{ !$classesAvailable || !$subjectsAvailable ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle me-2"></i>Schedule Class
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-lightbulb me-2 text-warning"></i>Tips
                        </h5>
                        <ul class="small mb-0">
                            <li class="mb-2">Schedule classes at least 15 minutes in advance</li>
                            <li class="mb-2">Enable auto-recording to save the session for students who miss it</li>
                            <li class="mb-2">Use recurring classes for regular weekly sessions</li>
                            <li class="mb-2">Zoom and Google Meet integration requires API setup</li>
                            <li class="mb-2">Students will receive email and in-app notifications</li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-gear me-2 text-primary"></i>Platform Setup
                        </h5>
                        <p class="small text-muted mb-3">Configure API credentials for automatic meeting creation:</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('tenant.teacher.classroom.integrations.setup', 'zoom') }}"
                                class="btn btn-sm btn-outline-info">
                                <i class="bi bi-camera-video me-2"></i>Setup Zoom
                            </a>
                            <a href="{{ route('tenant.teacher.classroom.integrations.setup', 'google_meet') }}"
                                class="btn btn-sm btn-outline-success">
                                <i class="bi bi-google me-2"></i>Setup Google Meet
                            </a>
                            <a href="{{ route('tenant.teacher.classroom.integrations.setup', 'microsoft_teams') }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-microsoft me-2"></i>Setup Teams
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recurringToggle = document.getElementById('is_recurring');
            const recurringOptions = document.getElementById('recurring_options');

            if (recurringToggle && recurringOptions) {
                recurringToggle.addEventListener('change', function() {
                    recurringOptions.style.display = this.checked ? 'block' : 'none';
                });
            }

            @if (!$classesAvailable || !$subjectsAvailable)
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                    }, {
                        once: true
                    });
                }
            @endif
        });
    </script>
@endpush
