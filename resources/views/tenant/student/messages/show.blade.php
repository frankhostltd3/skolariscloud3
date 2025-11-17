@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', $thread->subject)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('tenant.student.messages.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Messages') }}
        </a>
        <h4 class="mb-0">
            <i class="bi bi-chat-dots me-2"></i>{{ $thread->subject }}
        </h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4" style="height: 500px; overflow-y: auto;">
                <div class="card-body">
                    @foreach($messages as $message)
                        @php
                            $isSender = $message->sender_id == auth()->id();
                        @endphp
                        <div class="message-bubble {{ $isSender ? 'message-sent' : 'message-received' }} mb-3">
                            <div class="message-header">
                                <strong>{{ $message->sender->name }}</strong>
                                <small class="text-muted ms-2">{{ $message->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <div class="message-content">
                                {!! nl2br(e($message->content)) !!}
                            </div>
                            @if($message->hasAttachments())
                                <div class="message-attachments mt-2">
                                    @foreach($message->attachments as $attachment)
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-paperclip me-1"></i>{{ $attachment['name'] }} ({{ number_format($attachment['size'] / 1024, 2) }} KB)
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-reply me-2"></i>{{ __('Reply') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.student.messages.reply', $thread->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="4" placeholder="{{ __('Type your message here...') }}" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label small">{{ __('Attach File (Optional)') }}</label>
                            <input type="file" name="attachment" id="attachment" class="form-control form-control-sm @error('attachment') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                            <small class="text-muted">{{ __('Max 10MB. Allowed: PDF, DOC, DOCX, JPG, PNG, GIF') }}</small>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tenant.student.messages.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>{{ __('Send Reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-bubble { padding: 12px 16px; border-radius: 12px; max-width: 70%; }
.message-sent { background-color: #0d6efd; color: white; margin-left: auto; }
.message-received { background-color: #f1f3f5; color: #212529; margin-right: auto; }
.message-header { font-size: 0.85rem; margin-bottom: 5px; }
.message-content { font-size: 0.95rem; }
.message-sent .message-header { color: rgba(255,255,255,0.8); }
</style>
@endsection

