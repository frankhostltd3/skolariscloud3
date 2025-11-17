@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Messages')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-envelope me-2"></i>{{ __('Messages') }}
        </h4>
        <a href="{{ route('tenant.student.messages.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>{{ __('New Message') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-6 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">{{ __('Total Conversations') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $statistics['total_threads'] }}</h4>
                        </div>
                        <div class="text-primary" style="font-size: 1.5rem;">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">{{ __('Unread Messages') }}</h6>
                            <h4 class="mb-0 text-danger">{{ $statistics['unread_messages'] }}</h4>
                        </div>
                        <div class="text-danger" style="font-size: 1.5rem;">
                            <i class="bi bi-envelope-exclamation"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">{{ __('Sent Messages') }}</h6>
                            <h4 class="mb-0 text-success">{{ $statistics['total_sent'] }}</h4>
                        </div>
                        <div class="text-success" style="font-size: 1.5rem;">
                            <i class="bi bi-send-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tenant.student.messages.index') }}" class="row g-2">
                <div class="col-12 col-md-8">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Search messages...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <select name="filter" class="form-select form-select-sm">
                        <option value="">{{ __('All Messages') }}</option>
                        <option value="unread" {{ request('filter') == 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i>{{ __('Search') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($threads->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($threads as $thread)
                        @php
                            $latestMessage = $thread->latestMessage;
                            $isUnread = $latestMessage && $latestMessage->sender_id != auth()->id() && !$latestMessage->is_read;
                        @endphp
                        <a href="{{ route('tenant.student.messages.show', $thread->id) }}" class="list-group-item list-group-item-action message-item {{ $isUnread ? 'unread' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 me-2">{{ $thread->subject }}</h6>
                                        @if($isUnread)
                                            <span class="badge bg-danger">{{ __('New') }}</span>
                                        @endif
                                    </div>
                                    @if($latestMessage)
                                        <p class="text-muted mb-1 small">
                                            <strong>{{ $latestMessage->sender->name }}:</strong>
                                            {{ Str::limit(strip_tags($latestMessage->content), 100) }}
                                        </p>
                                    @endif
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $thread->last_message_at->diffForHumans() }}
                                        @if($thread->messages->count() > 1)
                                            <span class="ms-2"><i class="bi bi-chat-dots me-1"></i>{{ $thread->messages->count() }} {{ __('messages') }}</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="ms-3">
                                    <form action="{{ route('tenant.student.messages.destroy', $thread->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $threads->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">{{ __('No Messages') }}</h5>
                <p class="text-muted">{{ __('You have no messages yet. Start a conversation with your teachers!') }}</p>
                <a href="{{ route('tenant.student.messages.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-2"></i>{{ __('Send Your First Message') }}
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.message-item { transition: all 0.2s ease; border-left: 4px solid transparent !important; }
.message-item:hover { background-color: #f8f9fa; border-left-color: #0d6efd !important; }
.message-item.unread { background-color: #e7f3ff; border-left-color: #dc3545 !important; font-weight: 500; }
</style>
@endsection

