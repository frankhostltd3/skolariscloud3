@extends('layouts.dashboard-teacher')

@section('title', 'Create Discussion')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-chat-left-text me-2 text-primary"></i>Create New Discussion
            </h1>
            <p class="text-muted mb-0">Start a discussion with your class</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.discussions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Discussions
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.teacher.classroom.discussions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Discussion Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Discussion Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Discussion Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="e.g., Chapter 5 Discussion: Photosynthesis" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="8" 
                                      placeholder="Write your discussion post..." required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Provide clear context and questions for discussion</small>
                        </div>

                        <!-- Class and Subject -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">Subject (Optional)</label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" 
                                        id="subject_id" name="subject_id">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Discussion Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="general" {{ old('type', 'general') == 'general' ? 'selected' : '' }}>
                                    💬 General Discussion
                                </option>
                                <option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>
                                    ❓ Question
                                </option>
                                <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>
                                    📢 Announcement
                                </option>
                                <option value="poll" {{ old('type') == 'poll' ? 'selected' : '' }}>
                                    📊 Poll
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attachments -->
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Attachments (Optional)</label>
                            <input type="file" class="form-control @error('attachments') is-invalid @enderror" 
                                   id="attachments" name="attachments[]" multiple 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.gif">
                            <small class="text-muted">Max 5 files, 10MB each. Supported: PDF, DOC, PPT, Images</small>
                            @error('attachments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Discussion Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_pinned" 
                                       name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_pinned">
                                    📌 Pin to Top
                                </label>
                            </div>
                            <small class="text-muted d-block">Keep this discussion at the top of the list</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_replies" 
                                       name="allow_replies" value="1" {{ old('allow_replies', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_replies">
                                    💬 Allow Replies
                                </label>
                            </div>
                            <small class="text-muted d-block">Students can reply to this discussion</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="requires_approval" 
                                       name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_approval">
                                    ✅ Moderate Replies
                                </label>
                            </div>
                            <small class="text-muted d-block">Approve replies before they appear</small>
                        </div>

                        <div class="mb-0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_locked" 
                                       name="is_locked" value="1" {{ old('is_locked') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_locked">
                                    🔒 Lock Discussion
                                </label>
                            </div>
                            <small class="text-muted d-block">Prevent new replies (read-only)</small>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-lightbulb me-2 text-warning"></i>Discussion Tips
                        </h6>
                        <ul class="small mb-0">
                            <li>Ask open-ended questions</li>
                            <li>Encourage critical thinking</li>
                            <li>Moderate discussions regularly</li>
                            <li>Pin important announcements</li>
                            <li>Mark best answers for questions</li>
                            <li>Use polls for quick feedback</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('tenant.teacher.classroom.discussions.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Discussion
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide poll options based on type
    document.getElementById('type')?.addEventListener('change', function() {
        if (this.value === 'poll') {
            // Future: Add poll options UI
            console.log('Poll type selected - will add poll options UI');
        }
    });

    // Validate file uploads
    document.getElementById('attachments')?.addEventListener('change', function() {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const maxFiles = 5;
        
        if (this.files.length > maxFiles) {
            alert(`Maximum ${maxFiles} files allowed`);
            this.value = '';
            return;
        }
        
        for (let i = 0; i < this.files.length; i++) {
            if (this.files[i].size > maxSize) {
                alert(`File "${this.files[i].name}" is too large. Max size is 10MB.`);
                this.value = '';
                return;
            }
        }
    });
</script>
@endpush
