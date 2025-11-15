@extends('tenant.layouts.app')

@section('content')
    <div class="mb-4">
        <p class="text-muted mb-1">Welcome {{ $user->name }},</p>
        <h1 class="h3 fw-semibold mb-2">{{ $title }}</h1>
        <p class="text-muted mb-0">Follow your child's progress, upcoming events, and school communications.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Progress Reports</h5>
                    <p class="text-muted mb-3">Review grades, attendance, and teacher feedback in real time.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View progress</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Communications</h5>
                    <p class="text-muted mb-3">Stay connected with teachers and receive important announcements.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">Open messages</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Payments & Billing</h5>
                    <p class="text-muted mb-3">Manage tuition payments and track outstanding balances easily.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">Manage billing</a>
                </div>
            </div>
        </div>
    </div>
@endsection
