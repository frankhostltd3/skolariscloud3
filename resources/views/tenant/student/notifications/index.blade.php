@extends('layouts.dashboard-student')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-bell me-2"></i>{{ __('Notifications') }}
        </h4>
        @if($statistics['unread'] > 0)
            <form action="{{ route('tenant.student.notifications.markAllAsRead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-check-all me-2"></i>{{ __('Mark All as Read') }}
                </button>
            </form>
        @endif
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
                            <h6 class="text-muted mb-1 small">{{ __('Total Notifications') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $statistics['total'] }}</h4>
                        </div>
                        <div class="text-primary" style="font-size: 1.5rem;">
                            <i class="bi bi-bell"></i>
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
                            <h6 class="text-muted mb-1 small">{{ __('Unread') }}</h6>
                            <h4 class="mb-0 text-danger">{{ $statistics['unread'] }}</h4>
                        </div>
                        <div class="text-danger" style="font-size: 1.5rem;">
                            <i class="bi bi-bell-fill"></i>
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
                            <h6 class="text-muted mb-1 small">{{ __('Today') }}</h6>
                            <h4 class="mb-0 text-success">{{ $statistics['today'] }}</h4>
                        </div>
                        <div class="text-success" style="font-size: 1.5rem;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tenant.student.notifications.index') }}" class="row g-2">
                <div class="col-12 col-md-10">
                    <select name="filter" class="form-select form-select-sm">
                        <option value="">{{ __('All Notifications') }}</option>
                        <option value="unread" {{ request('filter') == 'unread' ? 'selected' : '' }}>{{ __('Unread Only') }}</option>
                        <option value="read" {{ request('filter') == 'read' ? 'selected' : '' }}>{{ __('Read Only') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter me-1"></i>{{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}" data-id="{{ $notification->id }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="notification-icon me-2">
                                            <i class="bi bi-info-circle text-primary"></i>
                                        </span>
                                        <h6 class="mb-0 me-2">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                        </h6>
                                        @if(is_null($notification->read_at))
                                            <span class="badge bg-danger">{{ __('New') }}</span>
                                        @endif
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] ?? $notification->data['body'] ?? 'No message content' }}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="ms-3">
                                    @if(is_null($notification->read_at))
                                        <button type="button" class="btn btn-sm btn-link p-0 mark-as-read" data-id="{{ $notification->id }}">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('tenant.student.notifications.destroy', $notification->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">{{ __('No Notifications') }}</h5>
                <p class="text-muted">{{ __('You have no notifications at the moment.') }}</p>
            </div>
        </div>
    @endif
</div>

<style>
.notification-item { transition: all 0.2s ease; border-left: 4px solid transparent !important; }
.notification-item.unread { background-color: #e7f3ff; border-left-color: #0d6efd !important; }
.notification-item:hover { background-color: #f8f9fa; }
.notification-icon { font-size: 1.2rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mark-as-read').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            const item = this.closest('.notification-item');
            
            fetch(`/student/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    item.classList.remove('unread');
                    this.remove();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endsection
