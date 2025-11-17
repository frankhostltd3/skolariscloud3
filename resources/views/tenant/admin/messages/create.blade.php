@extends('tenant.layouts.app')

@section('title', __('Create Message'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('Start New Conversation') }}</h1>
                    <p class="text-muted">{{ __('Begin a conversation with staff, students, or parents') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Messages') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.messages.store') }}" method="POST" id="messageForm">
                @csrf

                <div class="row">
                    <!-- Main Form -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Conversation Details') }}</h5>
                            </div>
                            <div class="card-body">
                                <!-- Subject -->
                                <div class="mb-3">
                                    <label for="subject" class="form-label">{{ __('Subject') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="{{ __('Enter conversation subject...') }}">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Initial Message -->
                                <div class="mb-3">
                                    <label for="message" class="form-label">{{ __('Initial Message') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" required placeholder="{{ __('Type your message here...') }}">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('Maximum 1000 characters') }}</div>
                                </div>

                                <!-- Message Type -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Message Type') }}</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="message_type" id="type_general" value="general" {{ old('message_type', 'general') == 'general' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_general">
                                                    <i class="bi bi-chat-dots text-primary"></i> {{ __('General') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="message_type" id="type_urgent" value="urgent" {{ old('message_type') == 'urgent' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_urgent">
                                                    <i class="bi bi-exclamation-triangle text-warning"></i> {{ __('Urgent') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="message_type" id="type_announcement" value="announcement" {{ old('message_type') == 'announcement' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_announcement">
                                                    <i class="bi bi-megaphone text-info"></i> {{ __('Announcement') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Priority') }}</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="priority" id="priority_normal" value="normal" {{ old('priority', 'normal') == 'normal' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="priority_normal">
                                                    <i class="bi bi-dash-circle text-secondary"></i> {{ __('Normal') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="priority" id="priority_high" value="high" {{ old('priority') == 'high' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="priority_high">
                                                    <i class="bi bi-arrow-up-circle text-warning"></i> {{ __('High') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="priority" id="priority_urgent" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="priority_urgent">
                                                    <i class="bi bi-exclamation-circle text-danger"></i> {{ __('Urgent') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
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
                                <!-- Recipient Selection Type -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Send To') }} <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_individual" value="individual" {{ old('recipient_type', 'individual') == 'individual' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_individual">
                                            {{ __('Individual Users') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_role" value="role" {{ old('recipient_type') == 'role' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_role">
                                            {{ __('By Role') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_class" value="class" {{ old('recipient_type') == 'class' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_class">
                                            {{ __('By Class') }}
                                        </label>
                                    </div>
                                    @error('recipient_type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Individual User Selection -->
                                <div class="mb-3" id="individual_group" style="{{ old('recipient_type', 'individual') == 'individual' ? '' : 'display: none;' }}">
                                    <label class="form-label">{{ __('Select Recipients') }}</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        @foreach(['Admin' => 'Administrators', 'Staff' => 'Staff', 'Student' => 'Students', 'Parent' => 'Parents'] as $role => $label)
                                            <div class="mb-2">
                                                <strong class="text-muted">{{ $label }}</strong>
                                                <div class="mt-1">
                                                    @php
                                                        $users = \App\Models\User::whereHas('roles', function($q) use ($role) {
                                                            $q->where('name', $role);
                                                        })->take(5)->get();
                                                    @endphp
                                                    @foreach($users as $user)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" name="recipients[]" id="user_{{ $user->id }}" value="{{ $user->id }}" {{ in_array($user->id, old('recipients', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="user_{{ $user->id }}">
                                                                {{ $user->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    @if($users->count() >= 5)
                                                        <small class="text-muted">{{ __('And ') . (\App\Models\User::whereHas('roles', function($q) use ($role) { $q->where('name', $role); })->count() - 5) . __(' more...') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('recipients')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role Selection -->
                                <div class="mb-3" id="role_group" style="{{ old('recipient_type') == 'role' ? '' : 'display: none;' }}">
                                    <label class="form-label">{{ __('Select Roles') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_admin" value="Admin" {{ in_array('Admin', old('recipient_roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_admin">
                                            {{ __('Administrators') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_staff" value="Staff" {{ in_array('Staff', old('recipient_roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_staff">
                                            {{ __('Staff') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_student" value="Student" {{ in_array('Student', old('recipient_roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_student">
                                            {{ __('Students') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_roles[]" id="role_parent" value="Parent" {{ in_array('Parent', old('recipient_roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_parent">
                                            {{ __('Parents') }}
                                        </label>
                                    </div>
                                    @error('recipient_roles')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Class Selection -->
                                <div class="mb-3" id="class_group" style="{{ old('recipient_type') == 'class' ? '' : 'display: none;' }}">
                                    <label class="form-label">{{ __('Select Classes') }}</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        @php
                                            $classes = \App\Models\SchoolClass::with('streams')->take(10)->get();
                                        @endphp
                                        @foreach($classes as $class)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="recipient_classes[]" id="class_{{ $class->id }}" value="{{ $class->id }}" {{ in_array($class->id, old('recipient_classes', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="class_{{ $class->id }}">
                                                    {{ $class->name }}
                                                    @if($class->streams->count() > 0)
                                                        <small class="text-muted">({{ $class->streams->pluck('name')->join(', ') }})</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('recipient_classes')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preview -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Preview') }}</label>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong id="preview_subject">{{ old('subject', __('Conversation Subject')) }}</strong>
                                            <span class="badge bg-primary ms-2" id="preview_type">{{ __('General') }}</span>
                                        </div>
                                        <div id="preview_message">{{ old('message', __('Your message will appear here...')) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-send"></i> {{ __('Start Conversation') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="document.getElementById('messageForm').reset(); updatePreview(); toggleRecipients();">
                                    <i class="bi bi-arrow-repeat"></i> {{ __('Reset Form') }}
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
        const subject = document.getElementById('subject').value || '{{ __("Conversation Subject") }}';
        const message = document.getElementById('message').value || '{{ __("Your message will appear here...") }}';
        const messageType = document.querySelector('input[name="message_type"]:checked');

        document.getElementById('preview_subject').textContent = subject;
        document.getElementById('preview_message').textContent = message;

        // Update type badge
        const typeBadge = document.getElementById('preview_type');
        if (messageType) {
            const typeText = messageType.value.charAt(0).toUpperCase() + messageType.value.slice(1);
            typeBadge.textContent = typeText;
        }
    }

    // Show/hide recipient options
    function toggleRecipients() {
        const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
        const individualGroup = document.getElementById('individual_group');
        const roleGroup = document.getElementById('role_group');
        const classGroup = document.getElementById('class_group');

        individualGroup.style.display = recipientType === 'individual' ? 'block' : 'none';
        roleGroup.style.display = recipientType === 'role' ? 'block' : 'none';
        classGroup.style.display = recipientType === 'class' ? 'block' : 'none';
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time preview
        document.getElementById('subject').addEventListener('input', updatePreview);
        document.getElementById('message').addEventListener('input', updatePreview);
        document.querySelectorAll('input[name="message_type"]').forEach(radio => {
            radio.addEventListener('change', updatePreview);
        });

        // Recipients toggle
        document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
            radio.addEventListener('change', toggleRecipients);
        });

        // Initialize states
        toggleRecipients();
        updatePreview();
    });
</script>
@endpush