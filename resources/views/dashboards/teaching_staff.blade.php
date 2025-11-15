@extends('tenant.layouts.app')

@section('content')
    <div class="mb-4">
        <p class="text-muted mb-1">Hi {{ $user->name }},</p>
        <h1 class="h3 fw-semibold mb-2">{{ $title }}</h1>
        <p class="text-muted mb-0">Plan lessons, track attendance, and connect with your students and guardians.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Lesson Planning</h5>
                    <p class="text-muted mb-3">Manage weekly lesson outlines and upload learning materials.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">Plan lessons</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Attendance Tracker</h5>
                    <p class="text-muted mb-3">Mark attendance and keep an eye on at-risk students.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">Track attendance</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Gradebook</h5>
                    <p class="text-muted mb-3">Record assessments and share feedback with guardians instantly.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">Open gradebook</a>
                </div>
            </div>
        </div>
    </div>
@endsection
