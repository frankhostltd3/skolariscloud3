@extends('tenant.layouts.app')

@section('title', __('Notification Details'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('Notification Details') }}</h1>
                    <p class="text-muted">{{ __('View notification details and delivery status') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Notifications') }}
                    </a>
                    @if($notification->status === 'draft')
                        <a href="{{ route('admin.notifications.edit', $notification) }}" class="btn btn-primary ms-2">
                            <i class="bi bi-pencil"></i> {{ __('Edit') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Notification Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $notification->title }}</h5>
                            <span class="badge bg-{{ $notification->status === 'sent' ? 'success' : ($notification->status === 'scheduled' ? 'warning' : ($notification->status === 'failed' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst($notification->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <!-- Message Content -->
                            <div class="mb-4">
                                <h6>{{ __('Message') }}</h6>
                                <div class="border rounded p-3 bg-light">
                                    {{ nl2br(e($notification->message)) }}
                                </div>
                            </div>

                            <!-- Metadata -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>{{ __('Channel') }}</h6>
                                    <p>
                                        <span class="badge bg-{{ $notification->channel === 'email' ? 'secondary' : ($notification->channel === 'sms' ? 'success' : ($notification->channel === 'whatsapp' ? 'primary' : 'info')) }}">
                                            <i class="bi bi-{{ $notification->channel === 'email' ? 'envelope' : ($notification->channel === 'sms' ? 'phone' : ($notification->channel === 'whatsapp' ? 'whatsapp' : 'bell')) }}"></i>
                                            {{ ucfirst($notification->channel) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>{{ __('Recipients') }}</h6>
                                    <p>
                                        @if($notification->recipient_type === 'all')
                                            <span class="badge bg-info">{{ __('All Users') }}</span>
                                        @elseif($notification->recipient_type === 'role')
                                            <span class="badge bg-warning">{{ __('Roles: ') . $notification->recipient_roles }}</span>
                                        @else
                                            <span class="badge bg-primary">{{ __('Specific Users') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Scheduling Info -->
                            @if($notification->scheduled_at)
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('Scheduled For') }}</h6>
                                        <p>{{ $notification->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('Time Until Send') }}</h6>
                                        <p>
                                            @if($notification->scheduled_at->isPast())
                                                <span class="text-danger">{{ __('Already sent or past due') }}</span>
                                            @else
                                                {{ $notification->scheduled_at->diffForHumans() }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Timestamps -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>{{ __('Created') }}</h6>
                                    <p>{{ $notification->created_at->format('l, F j, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>{{ __('Last Updated') }}</h6>
                                    <p>{{ $notification->updated_at->format('l, F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Logs -->
                    @if($notification->logs->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Delivery Logs') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Recipient') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Sent At') }}</th>
                                                <th>{{ __('Response') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($notification->logs as $log)
                                                <tr>
                                                    <td>{{ $log->recipient_email ?? $log->recipient_phone ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $log->status === 'sent' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($log->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $log->sent_at ? $log->sent_at->format('M d, H:i') : '-' }}</td>
                                                    <td>
                                                        @if($log->response)
                                                            <small class="text-muted">{{ Str::limit($log->response, 50) }}</small>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($notification->status === 'draft')
                                <form action="{{ route('admin.notifications.send', $notification) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Are you sure you want to send this notification now?') }}')">
                                        <i class="bi bi-send"></i> {{ __('Send Now') }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.notifications.edit', $notification) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-pencil"></i> {{ __('Edit Notification') }}
                                </a>
                            @endif

                            <a href="{{ route('admin.notifications.create') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-plus-circle"></i> {{ __('Create New') }}
                            </a>

                            <hr>

                            <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this notification? This action cannot be undone.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> {{ __('Delete Notification') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Delivery Statistics') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-success mb-1">{{ $stats['sent'] ?? 0 }}</h4>
                                        <small class="text-muted">{{ __('Sent') }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-danger mb-1">{{ $stats['failed'] ?? 0 }}</h4>
                                    <small class="text-muted">{{ __('Failed') }}</small>
                                </div>
                            </div>

                            @if(($stats['sent'] ?? 0) + ($stats['failed'] ?? 0) > 0)
                                <div class="mt-3">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ (($stats['sent'] ?? 0) / (($stats['sent'] ?? 0) + ($stats['failed'] ?? 0))) * 100 }}%">
                                            {{ number_format((($stats['sent'] ?? 0) / (($stats['sent'] ?? 0) + ($stats['failed'] ?? 0))) * 100, 1) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('Success Rate') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recipients Preview -->
                    @if($notification->recipient_type !== 'all')
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Target Recipients') }}</h5>
                            </div>
                            <div class="card-body">
                                @if($notification->recipient_type === 'role')
                                    <p class="mb-2">{{ __('Roles:') }}</p>
                                    @foreach(explode(',', $notification->recipient_roles) as $role)
                                        <span class="badge bg-primary me-1">{{ trim($role) }}</span>
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('Specific users selected') }}</p>
                                @endif

                                <hr>
                                <small class="text-muted">
                                    {{ __('Estimated recipients: ') . $estimatedRecipients }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection