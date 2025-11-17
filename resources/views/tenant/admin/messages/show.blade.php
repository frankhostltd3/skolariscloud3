@extends('tenant.layouts.app')

@section('title', __('Message Details'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $thread->subject }}</h1>
                    <p class="text-muted">{{ __('Conversation with') }} {{ $thread->participants->count() }} {{ __('participants') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Messages') }}
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Conversation Thread -->
                <div class="col-lg-8">
                    <!-- Thread Status -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-{{ $thread->status === 'active' ? 'success' : 'secondary' }} me-2">
                                        {{ ucfirst($thread->status) }}
                                    </span>
                                    <span class="badge bg-{{ $thread->priority === 'urgent' ? 'danger' : ($thread->priority === 'high' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($thread->priority) }} {{ __('Priority') }}
                                    </span>
                                    <span class="badge bg-info">
                                        {{ ucfirst($thread->message_type) }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    {{ __('Started') }} {{ $thread->created_at->format('M d, Y \a\t H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Messages') }}</h5>
                        </div>
                        <div class="card-body" id="messagesContainer" style="max-height: 600px; overflow-y: auto;">
                            @if($thread->messages->count() > 0)
                                @foreach($thread->messages as $message)
                                    <div class="message-item mb-4 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
                                        <div class="d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                            <div class="message-bubble {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" style="max-width: 70%;">
                                                <div class="d-flex align-items-center mb-2">
                                                    <strong class="me-2">{{ $message->sender->name ?? 'Unknown' }}</strong>
                                                    <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                                        {{ $message->created_at->format('M d, H:i') }}
                                                    </small>
                                                </div>
                                                <div class="message-content">
                                                    {{ nl2br(e($message->message)) }}
                                                </div>
                                                @if($message->attachments && count($message->attachments) > 0)
                                                    <div class="mt-2">
                                                        <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                                            <i class="bi bi-paperclip"></i> {{ count($message->attachments) }} {{ __('attachments') }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-chat-dots text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">{{ __('No messages in this conversation yet.') }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Reply Form -->
                        @if($thread->status === 'active')
                            <div class="card-footer">
                                <form action="{{ route('admin.messages.reply', $thread) }}" method="POST" id="replyForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="reply_message" class="form-label">{{ __('Your Reply') }}</label>
                                        <textarea class="form-control" id="reply_message" name="message" rows="3" required placeholder="{{ __('Type your reply...') }}"></textarea>
                                        @error('message')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleFormatting()">
                                                <i class="bi bi-type"></i> {{ __('Formatting') }}
                                            </button>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send"></i> {{ __('Send Reply') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="card-footer bg-light">
                                <div class="text-center text-muted">
                                    <i class="bi bi-archive"></i> {{ __('This conversation has been archived and cannot be replied to.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Participants Sidebar -->
                <div class="col-lg-4">
                    <!-- Participants -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Participants') }} ({{ $thread->participants->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($thread->participants as $participant)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-circle bg-{{ ['Admin' => 'primary', 'Staff' => 'success', 'Student' => 'info', 'Parent' => 'warning'][$participant->role] ?? 'secondary' }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold">{{ $participant->name }}</div>
                                                <div class="text-muted small">
                                                    <span class="badge bg-{{ ['Admin' => 'primary', 'Staff' => 'success', 'Student' => 'info', 'Parent' => 'warning'][$participant->role] ?? 'secondary' }}">
                                                        {{ $participant->role }}
                                                    </span>
                                                    @if($participant->email)
                                                        <br>{{ $participant->email }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Thread Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($thread->status === 'active')
                                <form action="{{ route('admin.messages.update', $thread) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="archived">
                                    <button type="submit" class="btn btn-warning w-100" onclick="return confirm('{{ __('Are you sure you want to archive this conversation?') }}')">
                                        <i class="bi bi-archive"></i> {{ __('Archive Conversation') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.messages.update', $thread) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Are you sure you want to reactivate this conversation?') }}')">
                                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('Reactivate Conversation') }}
                                    </button>
                                </form>
                            @endif

                            <hr>

                            <form action="{{ route('admin.messages.destroy', $thread) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this conversation? This action cannot be undone.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> {{ __('Delete Conversation') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Thread Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Statistics') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="text-primary mb-1">{{ $thread->messages->count() }}</h5>
                                        <small class="text-muted">{{ __('Messages') }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-success mb-1">{{ $thread->participants->count() }}</h5>
                                    <small class="text-muted">{{ __('Participants') }}</small>
                                </div>
                            </div>

                            <hr>

                            <div class="small text-muted">
                                <div class="mb-1">
                                    <strong>{{ __('Started:') }}</strong> {{ $thread->created_at->format('M d, Y H:i') }}
                                </div>
                                @if($thread->last_message)
                                    <div class="mb-1">
                                        <strong>{{ __('Last Message:') }}</strong> {{ $thread->last_message->created_at->diffForHumans() }}
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ __('Status:') }}</strong>
                                    <span class="badge bg-{{ $thread->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($thread->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-scroll to bottom of messages
    function scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        container.scrollTop = container.scrollHeight;
    }

    // Toggle formatting help
    function toggleFormatting() {
        // This could show/hide formatting help
        alert('Formatting options:\n- **bold**\n- *italic*\n- [link](url)');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();

        // Auto-scroll on new messages (if implemented with real-time updates)
        const observer = new MutationObserver(scrollToBottom);
        observer.observe(document.getElementById('messagesContainer'), {
            childList: true,
            subtree: true
        });
    });
</script>
@endpush