@extends('tenant.layouts.app')

@section('title', __('Edit Notification'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('Edit Notification') }}</h1>
                    <p class="text-muted">{{ __('Modify notification details and recipients') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.notifications.show', $notification) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Notification') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.notifications.update', $notification) }}" method="POST" id="notificationForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Main Form -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Notification Details') }}</h5>
                            </div>
                            <div class="card-body">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $notification->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="mb-3">
                                    <label for="message" class="form-label">{{ __('Message') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message', $notification->message) }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('Maximum 500 characters') }}</div>
                                </div>

                                <!-- Channel Selection -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Delivery Channel') }} <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="channel" id="channel_email" value="email" {{ old('channel', $notification->channel) == 'email' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="channel_email">
                                                    <i class="bi bi-envelope-fill text-secondary"></i> {{ __('Email') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="channel" id="channel_sms" value="sms" {{ old('channel', $notification->channel) == 'sms' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="channel_sms">
                                                    <i class="bi bi-phone-fill text-success"></i> {{ __('SMS') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="channel" id="channel_whatsapp" value="whatsapp" {{ old('channel', $notification->channel) == 'whatsapp' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="channel_whatsapp">
                                                    <i class="bi bi-whatsapp text-primary"></i> {{ __('WhatsApp') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="channel" id="channel_push" value="push" {{ old('channel', $notification->channel) == 'push' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="channel_push">
                                                    <i class="bi bi-bell-fill text-info"></i> {{ __('Push') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('channel')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Scheduling -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Scheduling') }}</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="schedule_type" id="send_now" value="now" {{ old('schedule_type', $notification->scheduled_at ? 'scheduled' : 'now') == 'now' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="send_now">
                                                    {{ __('Send Immediately') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="schedule_type" id="schedule_later" value="scheduled" {{ old('schedule_type', $notification->scheduled_at ? 'scheduled' : 'now') == 'scheduled' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="schedule_later">
                                                    {{ __('Schedule for Later') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Scheduled Date/Time -->
                                <div class="mb-3" id="scheduled_datetime_group" style="{{ old('schedule_type', $notification->scheduled_at ? 'scheduled' : 'now') == 'scheduled' ? '' : 'display: none;' }}">
                                    <label for="scheduled_at" class="form-label">{{ __('Scheduled Date & Time') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $notification->scheduled_at ? $notification->scheduled_at->format('Y-m-d\TH:i') : '') }}" {{ old('schedule_type', $notification->scheduled_at ? 'scheduled' : 'now') == 'scheduled' ? 'required' : '' }}>
                                    @error('scheduled_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients Sidebar -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Recipients') }}</h5>
                            </div>
                            <div class="card-body">
                                <!-- Recipient Type -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Send To') }} <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_all" value="all" {{ old('recipient_type', $notification->recipient_type) == 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_all">
                                            {{ __('All Users') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_role" value="role" {{ old('recipient_type', $notification->recipient_type) == 'role' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_role">
                                            {{ __('Specific Roles') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_users" value="users" {{ old('recipient_type', $notification->recipient_type) == 'users' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_users">
                                            {{ __('Specific Users') }}
                                        </label>
                                    </div>
                                    @error('recipient_type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role Selection -->
                                <div class="mb-3" id="roles_group" style="{{ old('recipient_type', $notification->recipient_type) == 'role' ? '' : 'display: none;' }}">
                                    <label class="form-label">{{ __('Select Roles') }}</label>
                                    @php
                                        $selectedRoles = old('recipient_roles', explode(',', $notification->recipient_roles ?? ''));
                                    @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_admin" value="Admin" {{ in_array('Admin', $selectedRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_admin">
                                            {{ __('Administrators') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_staff" value="Staff" {{ in_array('Staff', $selectedRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_staff">
                                            {{ __('Staff') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_student" value="Student" {{ in_array('Student', $selectedRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_student">
                                            {{ __('Students') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_parent" value="Parent" {{ in_array('Parent', $selectedRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_parent">
                                            {{ __('Parents') }}
                                        </label>
                                    </div>
                                    @error('recipient_roles')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- User Selection (placeholder for future implementation) -->
                                <div class="mb-3" id="users_group" style="{{ old('recipient_type', $notification->recipient_type) == 'users' ? '' : 'display: none;' }}">
                                    <label class="form-label">{{ __('Select Users') }}</label>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> {{ __('User selection will be implemented in the next version.') }}
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Preview') }}</label>
                                    <div class="border rounded p-3 bg-light">
                                        <div id="preview_title" class="fw-bold">{{ old('title', $notification->title) }}</div>
                                        <div id="preview_message" class="mt-2">{{ old('message', $notification->message) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-save"></i> {{ __('Update Notification') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="window.history.back();">
                                    <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update preview in real-time
    function updatePreview() {
        const title = document.getElementById('title').value || '{{ __("Notification Title") }}';
        const message = document.getElementById('message').value || '{{ __("Your notification message will appear here...") }}';

        document.getElementById('preview_title').textContent = title;
        document.getElementById('preview_message').textContent = message;
    }

    // Show/hide scheduling options
    function toggleScheduling() {
        const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
        const scheduledGroup = document.getElementById('scheduled_datetime_group');
        const scheduledInput = document.getElementById('scheduled_at');

        if (scheduleType === 'scheduled') {
            scheduledGroup.style.display = 'block';
            scheduledInput.required = true;
        } else {
            scheduledGroup.style.display = 'none';
            scheduledInput.required = false;
        }
    }

    // Show/hide recipient options
    function toggleRecipients() {
        const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
        const rolesGroup = document.getElementById('roles_group');
        const usersGroup = document.getElementById('users_group');

        rolesGroup.style.display = recipientType === 'role' ? 'block' : 'none';
        usersGroup.style.display = recipientType === 'users' ? 'block' : 'none';
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time preview
        document.getElementById('title').addEventListener('input', updatePreview);
        document.getElementById('message').addEventListener('input', updatePreview);

        // Scheduling toggle
        document.querySelectorAll('input[name="schedule_type"]').forEach(radio => {
            radio.addEventListener('change', toggleScheduling);
        });

        // Recipients toggle
        document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
            radio.addEventListener('change', toggleRecipients);
        });

        // Initialize states
        toggleScheduling();
        toggleRecipients();
        updatePreview();
    });
</script>
@endpush