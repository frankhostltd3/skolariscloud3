@extends('tenant.layouts.app')

@section('title', 'Create Notification')

@include('components.wysiwyg')

@push('styles')
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        .priority-option {
            cursor: pointer;
            transition: all 0.2s;
        }

        .priority-option:hover {
            transform: translateY(-2px);
        }

        .priority-option.active {
            border-color: var(--bs-primary) !important;
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }

        .preview-container {
            background-image: radial-gradient(#e9ecef 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">Create Notification</h4>
                        <p class="text-muted mb-0">Compose and send announcements to your school community.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Back to History
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Form Column -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-body p-4">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.notifications.store') }}" method="POST"
                                    id="notificationForm">
                                    @csrf

                                    <!-- Quick Templates -->
                                    <div class="d-flex justify-content-end mb-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle text-secondary"
                                                type="button" id="templateDropdown" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick
                                                Templates
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="templateDropdown">
                                                <li><a class="dropdown-item template-item" href="#"
                                                        data-title="School Closure Notice" data-type="danger"
                                                        data-message="<p>Dear Parents and Students,</p><p>Please be advised that the school will be closed on <strong>[Date]</strong> due to <strong>[Reason]</strong>.</p><p>Classes will resume on [Return Date].</p>">
                                                        🚨 School Closure</a></li>
                                                <li><a class="dropdown-item template-item" href="#"
                                                        data-title="Exam Schedule Released" data-type="warning"
                                                        data-message="<p>Attention Students,</p><p>The examination schedule for the upcoming term has been released. Please check your student portal for details.</p>">
                                                        📝 Exam Schedule</a></li>
                                                <li><a class="dropdown-item template-item" href="#"
                                                        data-title="New Term Welcome" data-type="info"
                                                        data-message="<p>Welcome back to a new term! We are excited to start this journey with you.</p><p>Please ensure all registration requirements are met by Friday.</p>">
                                                        👋 Welcome Message</a></li>
                                                <li><a class="dropdown-item template-item" href="#"
                                                        data-title="Sports Day Announcement" data-type="success"
                                                        data-message="<p>We are thrilled to announce that our annual Sports Day will be held on <strong>[Date]</strong>!</p><p>All parents are invited to attend and cheer for their children.</p>">
                                                        🏆 Sports Day</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <div class="mb-4">
                                        <label for="title"
                                            class="form-label fw-bold text-secondary text-uppercase small">Subject</label>
                                        <input type="text" class="form-control form-control-lg" id="title"
                                            name="title" placeholder="e.g., Important Update: Term Dates"
                                            value="{{ old('title') }}" required>
                                    </div>

                                    <!-- Message Body -->
                                    <div class="mb-4">
                                        <label for="message"
                                            class="form-label fw-bold text-secondary text-uppercase small">Message
                                            Content</label>
                                        <textarea class="form-control wysiwyg-editor" id="message" name="message" data-placeholder="Type your message here..."
                                            required>{{ old('message') }}</textarea>
                                    </div>

                                    <div class="row g-4">
                                        <!-- Priority Selection -->
                                        <div class="col-12">
                                            <label
                                                class="form-label fw-bold text-secondary text-uppercase small mb-3">Priority
                                                Level</label>
                                            <div class="row g-3">
                                                <div class="col-md-3 col-6">
                                                    <label class="card h-100 border priority-option" id="opt-info">
                                                        <div class="card-body text-center p-3">
                                                            <input type="radio" name="type" value="info"
                                                                class="d-none"
                                                                {{ old('type', 'info') == 'info' ? 'checked' : '' }}>
                                                            <i class="bi bi-info-circle fs-3 text-primary mb-2 d-block"></i>
                                                            <span class="fw-bold d-block">Info</span>
                                                            <small class="text-muted" style="font-size: 0.75rem;">General
                                                                updates</small>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <label class="card h-100 border priority-option" id="opt-success">
                                                        <div class="card-body text-center p-3">
                                                            <input type="radio" name="type" value="success"
                                                                class="d-none"
                                                                {{ old('type') == 'success' ? 'checked' : '' }}>
                                                            <i
                                                                class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                                                            <span class="fw-bold d-block">Success</span>
                                                            <small class="text-muted" style="font-size: 0.75rem;">Positive
                                                                news</small>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <label class="card h-100 border priority-option" id="opt-warning">
                                                        <div class="card-body text-center p-3">
                                                            <input type="radio" name="type" value="warning"
                                                                class="d-none"
                                                                {{ old('type') == 'warning' ? 'checked' : '' }}>
                                                            <i
                                                                class="bi bi-exclamation-triangle fs-3 text-warning mb-2 d-block"></i>
                                                            <span class="fw-bold d-block">Warning</span>
                                                            <small class="text-muted"
                                                                style="font-size: 0.75rem;">Important</small>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <label class="card h-100 border priority-option" id="opt-danger">
                                                        <div class="card-body text-center p-3">
                                                            <input type="radio" name="type" value="danger"
                                                                class="d-none"
                                                                {{ old('type') == 'danger' ? 'checked' : '' }}>
                                                            <i
                                                                class="bi bi-exclamation-octagon fs-3 text-danger mb-2 d-block"></i>
                                                            <span class="fw-bold d-block">Urgent</span>
                                                            <small class="text-muted" style="font-size: 0.75rem;">Critical
                                                                alerts</small>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Channels -->
                                        <div class="col-12">
                                            <label class="form-label fw-bold text-secondary text-uppercase small">Delivery
                                                Channels</label>
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <div class="d-flex flex-wrap gap-4">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="channels[]" value="database" id="channel_database"
                                                                checked>
                                                            <label class="form-check-label fw-medium"
                                                                for="channel_database">
                                                                <i class="bi bi-bell-fill text-primary me-1"></i> In-App
                                                                Notification
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="channels[]" value="mail" id="channel_mail"
                                                                {{ in_array('mail', old('channels', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-medium" for="channel_mail">
                                                                <i class="bi bi-envelope-fill text-danger me-1"></i> Email
                                                                Blast
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-switch opacity-50"
                                                            title="SMS configuration required">
                                                            <input class="form-check-input" type="checkbox" disabled>
                                                            <label class="form-check-label fw-medium text-muted">
                                                                <i class="bi bi-chat-dots-fill me-1"></i> SMS <span
                                                                    class="badge bg-secondary ms-1"
                                                                    style="font-size: 0.6rem;">SOON</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-secondary-subtle">

                                    <!-- Audience Selection -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-secondary text-uppercase small mb-3">Target
                                            Audience</label>
                                        <div class="btn-group w-100" role="group" aria-label="Recipient Selection">
                                            <input type="radio" class="btn-check" name="recipients" id="recipient_all"
                                                value="all" {{ old('recipients', 'all') == 'all' ? 'checked' : '' }}
                                                autocomplete="off">
                                            <label class="btn btn-outline-primary py-2" for="recipient_all">
                                                <i class="bi bi-people-fill me-1"></i> Everyone
                                            </label>

                                            <input type="radio" class="btn-check" name="recipients"
                                                id="recipient_role" value="role"
                                                {{ old('recipients') == 'role' ? 'checked' : '' }} autocomplete="off">
                                            <label class="btn btn-outline-primary py-2" for="recipient_role">
                                                <i class="bi bi-person-badge-fill me-1"></i> By Role
                                            </label>

                                            <input type="radio" class="btn-check" name="recipients"
                                                id="recipient_users" value="users"
                                                {{ old('recipients') == 'users' ? 'checked' : '' }} autocomplete="off">
                                            <label class="btn btn-outline-primary py-2" for="recipient_users">
                                                <i class="bi bi-person-check-fill me-1"></i> Specific Users
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Role Selector -->
                                    <div id="role_selector"
                                        class="mb-4 {{ old('recipients') == 'role' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                        <label for="role_id" class="form-label fw-bold">Select Role</label>
                                        <select class="form-select form-select-lg" id="role_id" name="role_id">
                                            <option value="">-- Choose a Role --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- User Selector (Tom Select) -->
                                    <div id="user_selector"
                                        class="mb-4 {{ old('recipients') == 'users' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                        <label for="user_ids" class="form-label fw-bold">Select Users</label>
                                        <select id="user_ids" name="user_ids[]" multiple
                                            placeholder="Search for users..." autocomplete="off">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ collect(old('user_ids'))->contains($user->id) ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }}) -
                                                    {{ $user->user_type->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text"><i class="bi bi-info-circle"></i> Type to search by name
                                            or email.</div>
                                    </div>

                                    <!-- Submit Actions -->
                                    <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                                        <button type="button" class="btn btn-light text-muted"
                                            onclick="window.history.back()">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                            <i class="bi bi-send-fill me-2"></i> Send Notification
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Column -->
                    <div class="col-lg-4 d-none d-lg-block">
                        <div class="sticky-top" style="top: 2rem; z-index: 1;">
                            <div class="card border-0 shadow-sm bg-white">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 fw-bold text-uppercase text-secondary small"><i
                                            class="bi bi-eye me-2"></i> Live Preview</h6>
                                </div>
                                <div class="card-body preview-container p-4 rounded-bottom">
                                    <!-- Notification Card Preview -->
                                    <div class="card shadow-sm border-0 mb-3 overflow-hidden" id="preview_card"
                                        style="border-left: 5px solid var(--bs-primary) !important;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0 text-dark" id="preview_title">Notification Title
                                                </h6>
                                                <span class="badge bg-primary rounded-pill" id="preview_badge">Info</span>
                                            </div>
                                            <div class="d-flex align-items-center text-muted small mb-3">
                                                <i class="bi bi-clock me-1"></i> Just now
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-person-circle me-1"></i> Admin
                                            </div>
                                            <div class="text-secondary" id="preview_message"
                                                style="font-size: 0.95rem; line-height: 1.5;">
                                                Your message content will appear here...
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <small class="text-muted d-block mb-2">This is how recipients will see it</small>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-light text-dark border"><i
                                                    class="bi bi-laptop me-1"></i> Web</span>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-phone me-1"></i>
                                                Mobile</span>
                                            <span class="badge bg-light text-dark border"><i
                                                    class="bi bi-envelope me-1"></i> Email</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Tom Select JS -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.initWysiwygEditors('#message');

                const userSelectInput = document.getElementById('user_ids');
                if (userSelectInput) {
                    new TomSelect('#user_ids', {
                        plugins: ['remove_button', 'checkbox_options'],
                        create: false,
                        sortField: {
                            field: 'text',
                            direction: 'asc'
                        }
                    });
                }

                const recipientRadios = document.querySelectorAll('input[name="recipients"]');
                const roleSelector = document.getElementById('role_selector');
                const userSelector = document.getElementById('user_selector');
                const titleInput = document.getElementById('title');
                const priorityOptions = document.querySelectorAll('.priority-option');
                const previewTitle = document.getElementById('preview_title');
                const previewMessage = document.getElementById('preview_message');
                const previewBadge = document.getElementById('preview_badge');
                const previewCard = document.getElementById('preview_card');
                const messageFieldId = 'message';

                const toggleSelectors = () => {
                    const selected = document.querySelector('input[name="recipients"]:checked');
                    if (!selected) {
                        return;
                    }

                    if (selected.value === 'role') {
                        roleSelector.classList.remove('d-none');
                        userSelector.classList.add('d-none');
                    } else if (selected.value === 'users') {
                        roleSelector.classList.add('d-none');
                        userSelector.classList.remove('d-none');
                    } else {
                        roleSelector.classList.add('d-none');
                        userSelector.classList.add('d-none');
                    }
                };

                recipientRadios.forEach(radio => radio.addEventListener('change', toggleSelectors));
                toggleSelectors();

                priorityOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        priorityOptions.forEach(opt => opt.classList.remove('active'));
                        this.classList.add('active');
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio) {
                            radio.checked = true;
                        }
                        updatePreview();
                    });
                });

                const checkedPriority = document.querySelector('input[name="type"]:checked');
                if (checkedPriority) {
                    checkedPriority.closest('.priority-option')?.classList.add('active');
                }

                const getMessageContent = () => window.getWysiwygContent(messageFieldId) || '';

                const updatePreview = () => {
                    previewTitle.textContent = titleInput.value || 'Notification Title';

                    const messageContent = getMessageContent();
                    const strippedContent = messageContent
                        .replace(/<[^>]*>/g, ' ')
                        .replace(/&nbsp;/g, ' ')
                        .trim();
                    previewMessage.innerHTML = strippedContent ? messageContent :
                        'Your message content will appear here...';

                    const typeRadio = document.querySelector('input[name="type"]:checked');
                    const type = typeRadio ? typeRadio.value : 'info';
                    let badgeClass = 'bg-primary';
                    let borderColor = 'var(--bs-primary)';
                    let badgeText = 'Info';

                    if (type === 'success') {
                        badgeClass = 'bg-success';
                        borderColor = 'var(--bs-success)';
                        badgeText = 'Success';
                    } else if (type === 'warning') {
                        badgeClass = 'bg-warning text-dark';
                        borderColor = 'var(--bs-warning)';
                        badgeText = 'Warning';
                    } else if (type === 'danger') {
                        badgeClass = 'bg-danger';
                        borderColor = 'var(--bs-danger)';
                        badgeText = 'Urgent';
                    }

                    previewBadge.className = 'badge rounded-pill';
                    previewBadge.classList.add(...badgeClass.split(' '));
                    previewBadge.textContent = badgeText;
                    previewCard.style.borderLeft = `5px solid ${borderColor}`;
                };

                titleInput.addEventListener('input', updatePreview);

                const syncEditorPreview = () => {
                    const editor = window.skolarisEditors[messageFieldId];
                    if (editor) {
                        editor.model.document.on('change:data', updatePreview);
                    } else {
                        setTimeout(syncEditorPreview, 150);
                    }
                };
                syncEditorPreview();

                document.querySelectorAll('.template-item').forEach(item => {
                    item.addEventListener('click', function(event) {
                        event.preventDefault();
                        const templateTitle = this.dataset.title;
                        const templateMessage = this.dataset.message;
                        const templateType = this.dataset.type;

                        titleInput.value = templateTitle || titleInput.value;
                        window.setWysiwygContent(messageFieldId, templateMessage || '');

                        const typeRadio = document.querySelector(
                            `input[name="type"][value="${templateType}"]`);
                        if (typeRadio) {
                            typeRadio.checked = true;
                            priorityOptions.forEach(opt => opt.classList.remove('active'));
                            typeRadio.closest('.priority-option')?.classList.add('active');
                        }

                        updatePreview();
                    });
                });

                updatePreview();
            });
        </script>
    @endpush
@endsection
