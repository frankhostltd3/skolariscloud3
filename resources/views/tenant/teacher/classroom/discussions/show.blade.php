@extends('layouts.dashboard-teacher')

@section('title', $discussion->title)

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-chat-left-text me-2 text-primary"></i>{{ $discussion->title }}
                </h1>
                <p class="text-muted mb-0">
                    {{ $discussion->class->name }}
                    @if ($discussion->subject)
                        • {{ $discussion->subject->name }}
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.discussions.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
                <div class="btn-group">
                    <a href="{{ route('tenant.teacher.classroom.discussions.edit', $discussion) }}"
                        class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <form action="{{ route('tenant.teacher.classroom.discussions.destroy', $discussion) }}" method="POST"
                        class="d-inline" onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Discussion Post -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-white text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;">
                                {{ substr($discussion->teacher->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 text-white">{{ $discussion->teacher->name }}</h6>
                                <small class="text-white-50">{{ $discussion->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        <div>
                            @if ($discussion->is_pinned)
                                <span class="badge bg-warning text-dark me-1"><i
                                        class="bi bi-pin-angle-fill me-1"></i>Pinned</span>
                            @endif
                            @if ($discussion->is_locked)
                                <span class="badge bg-danger me-1"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                            @endif
                            <span class="badge bg-light text-primary">{{ $discussion->type_label }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="discussion-content mb-4">
                            {!! nl2br(e($discussion->content)) !!}
                        </div>

                        @if ($discussion->attachments)
                            <div class="attachments mt-4 pt-3 border-top">
                                <h6 class="text-muted mb-2"><i class="bi bi-paperclip me-2"></i>Attachments</h6>
                                <div class="row g-2">
                                    @foreach ($discussion->attachments as $attachment)
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body p-2 d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-file-earmark-text fs-4 text-secondary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate">
                                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                                            class="text-decoration-none text-dark stretched-link">
                                                            {{ $attachment['name'] }}
                                                        </a>
                                                        <div class="small text-muted">
                                                            {{ round($attachment['size'] / 1024, 1) }} KB</div>
                                                    </div>
                                                    <div>
                                                        <i class="bi bi-download text-muted"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-chat-dots me-1"></i> {{ $discussion->replies->count() }} Replies
                        </div>
                        <div>
                            <form action="{{ route('tenant.teacher.classroom.discussions.toggle-lock', $discussion) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted">
                                    <i class="bi bi-{{ $discussion->is_locked ? 'unlock' : 'lock' }} me-1"></i>
                                    {{ $discussion->is_locked ? 'Unlock' : 'Lock' }}
                                </button>
                            </form>
                            <form action="{{ route('tenant.teacher.classroom.discussions.toggle-pin', $discussion) }}"
                                method="POST" class="d-inline ms-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted">
                                    <i class="bi bi-pin-angle{{ $discussion->is_pinned ? '-fill' : '' }} me-1"></i>
                                    {{ $discussion->is_pinned ? 'Unpin' : 'Pin' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Replies Section -->
                <h5 class="mb-3">Replies</h5>

                @forelse($discussion->replies as $reply)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-sm bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">
                                    {{ substr($reply->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $reply->user->name }}</h6>
                                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="reply-content">
                                {!! nl2br(e($reply->content)) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-chat-square-dots display-4 mb-3 d-block"></i>
                        <p>No replies yet. Be the first to start the conversation!</p>
                    </div>
                @endforelse

                <!-- Reply Form -->
                @if (!$discussion->is_locked && $discussion->allow_replies)
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Post a Reply</h6>
                            <form action="{{ route('tenant.teacher.classroom.discussions.reply', $discussion) }}"
                                method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="content" rows="3" placeholder="Write your reply..." required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Post Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif($discussion->is_locked)
                    <div class="alert alert-secondary mt-4">
                        <i class="bi bi-lock-fill me-2"></i>This discussion is locked. New replies are disabled.
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Participants</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;">
                                {{ substr($discussion->teacher->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $discussion->teacher->name }}</div>
                                <small class="text-muted">Teacher (Host)</small>
                            </div>
                        </div>
                        <!-- Placeholder for student participants -->
                        <p class="text-muted small mb-0">
                            <i class="bi bi-people me-1"></i> Visible to {{ $discussion->class->name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
