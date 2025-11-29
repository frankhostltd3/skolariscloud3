@extends('tenant.layouts.app')

@section('title', $thread->title)

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('tenant.forum.index') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Back to Forum
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <!-- Main Thread -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h3 card-title mb-1">{{ $thread->title }}</h1>
                                <div class="text-muted small">
                                    Posted by <strong>{{ $thread->author->name }}</strong>
                                    on {{ $thread->created_at->format('M d, Y h:i A') }}
                                    @if ($thread->context_type)
                                        &bull; <span class="badge bg-info">{{ ucfirst($thread->context_type) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-flag me-2"></i>Report</a>
                                    </li>
                                    @if (auth()->user()->hasRole('Admin') || auth()->id() == $thread->moderator_id)
                                        <li>
                                            <form action="{{ route('tenant.forum.status.update', $thread->slug) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status"
                                                    value="{{ $thread->status == 'active' ? 'closed' : 'active' }}">
                                                <button class="dropdown-item" type="submit">
                                                    <i
                                                        class="bi bi-{{ $thread->status == 'active' ? 'lock' : 'unlock' }} me-2"></i>
                                                    {{ $thread->status == 'active' ? 'Close Thread' : 'Re-open Thread' }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    @if (auth()->id() == $thread->user_id || auth()->user()->hasRole('Admin'))
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i>Delete</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="card-text fs-5 mb-4">
                            {!! nl2br(e($thread->content)) !!}
                        </div>

                        <div class="d-flex gap-2">
                            <a href="https://wa.me/?text={{ urlencode('Check out this discussion: ' . route('tenant.forum.show', $thread->slug)) }}"
                                target="_blank" class="btn btn-success btn-sm">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <a href="mailto:?subject={{ urlencode($thread->title) }}&body={{ urlencode('Check out this discussion: ' . route('tenant.forum.show', $thread->slug)) }}"
                                class="btn btn-secondary btn-sm">
                                <i class="bi bi-envelope"></i> Email
                            </a>
                            <a href="sms:?body={{ urlencode('Check out this discussion: ' . route('tenant.forum.show', $thread->slug)) }}"
                                class="btn btn-info btn-sm text-white">
                                <i class="bi bi-chat-dots"></i> SMS
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Replies -->
                <h4 class="mb-3">Replies ({{ $thread->posts->count() }})</h4>

                @foreach ($thread->posts as $post)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ $post->author->name }}</strong>
                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="card-text">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Reply Form -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        Leave a Reply
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.forum.reply', $thread->slug) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="replyContent" class="form-label mb-0">Your Reply</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="aiReplyAssistBtn">
                                        <i class="bi bi-stars"></i> AI Assist
                                    </button>
                                </div>
                                <textarea class="form-control" id="replyContent" name="content" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Reply</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <!-- Sidebar info (Moderators, Stats) -->
                <div class="card mb-3">
                    <div class="card-header">Stats</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Views
                            <span class="badge bg-primary rounded-pill">{{ $thread->views_count }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Replies
                            <span class="badge bg-primary rounded-pill">{{ $thread->posts->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assist Modal (Reused) -->
    <div class="modal fade" id="aiAssistModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-stars text-primary"></i> AI Assistant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="aiPrompt" class="form-label">What should I write about?</label>
                        <textarea class="form-control" id="aiPrompt" rows="3" placeholder="E.g., Draft a polite disagreement..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <select class="form-select" id="aiModel">
                            <option value="gemini-pro-3">Gemini 3 Pro (Preview)</option>
                            <option value="gpt-4">GPT-4</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="generateAiContent">Generate</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const aiAssistBtn = document.getElementById('aiReplyAssistBtn');
                const aiModal = new bootstrap.Modal(document.getElementById('aiAssistModal'));
                const generateBtn = document.getElementById('generateAiContent');
                const contentArea = document.getElementById('replyContent');

                aiAssistBtn.addEventListener('click', () => aiModal.show());

                generateBtn.addEventListener('click', function() {
                    const prompt = document.getElementById('aiPrompt').value;
                    const model = document.getElementById('aiModel').value;

                    if (!prompt) return;

                    this.disabled = true;
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';

                    fetch('{{ route('tenant.forum.ai-assist') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                prompt,
                                model
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            contentArea.value = data.response;
                            aiModal.hide();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to generate content');
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = 'Generate';
                        });
                });
            });
        </script>
    @endpush
@endsection
