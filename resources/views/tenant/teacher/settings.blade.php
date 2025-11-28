@extends('layouts.dashboard-teacher')

@section('title', 'Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Settings</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email Notifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked disabled>
                            <label class="form-check-label" for="emailNotifications">
                                Receive email notifications
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SMS Notifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="smsNotifications" disabled>
                            <label class="form-check-label" for="smsNotifications">
                                Receive SMS notifications
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Language</label>
                        <select class="form-select" disabled>
                            <option selected>English</option>
                            <option>Spanish</option>
                            <option>French</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Timezone</label>
                        <select class="form-select" disabled>
                            <option selected>UTC</option>
                            <option>EST</option>
                            <option>PST</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection