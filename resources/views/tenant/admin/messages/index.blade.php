@extends('tenant.layouts.app')

@section('title', __('Messages'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('Messages') }}</h1>
                    <p class="text-muted">{{ __('Communicate with staff, students, and parents') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.messages.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> {{ __('Start New Conversation') }}
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-dots-fill text-primary" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['total_threads'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Total Conversations') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-envelope-open-fill text-success" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['active_threads'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Active') }}</p>
                        </div>
                                    @if(!empty($messagesDisabled))
                                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                                            <i class="bi bi-chat-square-dots me-2"></i>
                                            <span>{{ __('Messaging tables are not available yet. Run the tenant messaging migrations to enable this module.') }}</span>
                                        </div>
                                    @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['unread_messages'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Unread Messages') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="bi bi-people-fill text-info" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['unique_contacts'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Contacts') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">{{ __('Search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search conversations...') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">{{ __('All Conversations') }}</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>{{ __('Archived') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="participant_role" class="form-label">{{ __('Participant Role') }}</label>
                            <select class="form-select" id="participant_role" name="participant_role">
                                <option value="">{{ __('All Roles') }}</option>
                                <option value="Admin" {{ request('participant_role') == 'Admin' ? 'selected' : '' }}>{{ __('Administrators') }}</option>
                                <option value="Staff" {{ request('participant_role') == 'Staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                                <option value="Student" {{ request('participant_role') == 'Student' ? 'selected' : '' }}>{{ __('Students') }}</option>
                                <option value="Parent" {{ request('participant_role') == 'Parent' ? 'selected' : '' }}>{{ __('Parents') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Message Threads -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Conversations') }}</h5>
                </div>
                <div class="card-body">
                    @if($threads->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($threads as $thread)
                                <a href="{{ route('admin.messages.show', $thread) }}" class="list-group-item list-group-item-action px-3 py-3 {{ $thread->hasUnreadMessages() ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-3">{{ $thread->subject }}</h6>
                                                @if($thread->hasUnreadMessages())
                                                    <span class="badge bg-primary rounded-pill">{{ $thread->unread_count }}</span>
                                                @endif
                                            </div>

                                            <div class="d-flex align-items-center text-muted small mb-2">
                                                <span class="me-3">
                                                    <i class="bi bi-people"></i>
                                                    {{ __('Participants:') }} {{ $thread->participants->count() }}
                                                </span>
                                                <span class="me-3">
                                                    <i class="bi bi-chat-dots"></i>
                                                    {{ $thread->messages->count() }} {{ __('messages') }}
                                                </span>
                                                @if($thread->last_message)
                                                    <span>
                                                        <i class="bi bi-clock"></i>
                                                        {{ $thread->last_message->created_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($thread->last_message)
                                                <p class="mb-1 text-truncate">
                                                    <strong>{{ $thread->last_message->sender->name ?? 'Unknown' }}:</strong>
                                                    {{ Str::limit($thread->last_message->message, 100) }}
                                                </p>
                                            @endif

                                            <!-- Participant avatars/roles -->
                                            <div class="d-flex align-items-center">
                                                @foreach($thread->participants->take(3) as $participant)
                                                    <span class="badge bg-secondary me-1 small">
                                                        <i class="bi bi-person"></i> {{ $participant->role }}
                                                    </span>
                                                @endforeach
                                                @if($thread->participants->count() > 3)
                                                    <span class="badge bg-light text-dark me-1 small">
                                                        +{{ $thread->participants->count() - 3 }} {{ __('more') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-end">
                                            @if($thread->last_message)
                                                <small class="text-muted d-block">
                                                    {{ $thread->last_message->created_at->format('M d, H:i') }}
                                                </small>
                                            @endif
                                            <div class="mt-2">
                                                @if($thread->status === 'active')
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Archived') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $threads->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">{{ __('No conversations found') }}</h5>
                            <p class="text-muted">{{ __('Start a new conversation to begin communicating with users.') }}</p>
                            <a href="{{ route('admin.messages.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> {{ __('Start New Conversation') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush