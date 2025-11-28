@extends('tenant.layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark fw-bold mb-0">
                <i class="bi bi-bell-fill text-primary me-2"></i> Notifications
            </h3>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> Create Notification
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <div class="icon-circle bg-light text-primary p-4 rounded-circle d-inline-block">
                        <i class="bi bi-inbox-fill fs-1"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark">Notification Center</h4>
                <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                    Manage and send notifications to students, teachers, and parents.
                    Keep your school community informed with important updates.
                </p>
                <a href="{{ route('admin.notifications.create') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-megaphone me-2"></i> Send Your First Notification
                </a>
            </div>
        </div>

        <!--
        TODO: In a future update, list sent notifications here.
        This would require a database table to track sent notifications (e.g., 'notification_logs').
        Laravel's default 'notifications' table tracks *received* notifications per user,
        not *sent* campaigns.
        -->
    </div>
@endsection
