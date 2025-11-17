@extends('tenant.layouts.app')

@section('title', __('Notifications'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(!empty($notificationsDisabled))
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <span>{{ __('Notifications database tables are not available yet. Run the tenant notifications migration to enable this module.') }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('Notifications') }}</h1>
                    <p class="text-muted">{{ __('Manage and send notifications to users') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> {{ __('Create Notification') }}
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="bi bi-bell-fill text-primary" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['total'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Total Notifications') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-send-fill text-success" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['sent'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Sent') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['scheduled'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Scheduled') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-dots-fill text-info" style="font-size: 2rem;"></i>
                            <h4 class="mt-2 mb-1">{{ $stats['messages'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('Messages') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">{{ __('Search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search notifications...') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('Sent') }}</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="channel" class="form-label">{{ __('Channel') }}</label>
                            <select class="form-select" id="channel" name="channel">
                                <option value="">{{ __('All Channels') }}</option>
                                <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                                <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>{{ __('SMS') }}</option>
                                <option value="whatsapp" {{ request('channel') == 'whatsapp' ? 'selected' : '' }}>{{ __('WhatsApp') }}</option>
                                <option value="push" {{ request('channel') == 'push' ? 'selected' : '' }}>{{ __('Push') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">{{ __('From Date') }}</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">{{ __('To Date') }}</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Notifications') }}</h5>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Channel') }}</th>
                                        <th>{{ __('Recipients') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Scheduled') }}</th>
                                        <th>{{ __('Created') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $notification->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $notification->channel === 'email' ? 'secondary' : ($notification->channel === 'sms' ? 'success' : ($notification->channel === 'whatsapp' ? 'primary' : 'info')) }}">
                                                    {{ ucfirst($notification->channel) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($notification->recipient_type === 'all')
                                                    <span class="badge bg-info">{{ __('All Users') }}</span>
                                                @elseif($notification->recipient_type === 'role')
                                                    <span class="badge bg-warning">{{ __('Role: ') . $notification->recipient_roles }}</span>
                                                @else
                                                    <span class="badge bg-primary">{{ __('Specific Users') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($notification->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                                        @break
                                                    @case('scheduled')
                                                        <span class="badge bg-warning">{{ __('Scheduled') }}</span>
                                                        @break
                                                    @case('sent')
                                                        <span class="badge bg-success">{{ __('Sent') }}</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">{{ __('Failed') }}</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($notification->scheduled_at)
                                                    {{ $notification->scheduled_at->format('M d, Y H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $notification->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.notifications.show', $notification) }}" class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($notification->status === 'draft')
                                                        <a href="{{ route('admin.notifications.edit', $notification) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.notifications.send', $notification) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to send this notification?') }}')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('Send Now') }}">
                                                                <i class="bi bi-send"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this notification?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $notifications->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">{{ __('No notifications found') }}</h5>
                            <p class="text-muted">{{ __('Create your first notification to get started.') }}</p>
                            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> {{ __('Create Notification') }}
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