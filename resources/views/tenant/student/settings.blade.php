@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="mb-0">Settings</h4>
            <small class="text-muted">Manage your account and preferences</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Account</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $user->email ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Change Password</label>
                        <div class="d-grid">
                            <button class="btn btn-primary" type="button" disabled>Change Password (Ask Admin)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Preferences</div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="prefDarkMode" disabled>
                        <label class="form-check-label" for="prefDarkMode">Dark mode</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="prefEmailNotif" checked disabled>
                        <label class="form-check-label" for="prefEmailNotif">Email notifications</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="prefPushNotif" checked disabled>
                        <label class="form-check-label" for="prefPushNotif">Push notifications</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">Notifications</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifAssignments" checked disabled>
                                <label class="form-check-label" for="notifAssignments">Assignments</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifExams" checked disabled>
                                <label class="form-check-label" for="notifExams">Exams & Quizzes</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifLibrary" disabled>
                                <label class="form-check-label" for="notifLibrary">Library</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">Note: Settings are read-only in this demo. Contact admin to update your preferences.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
