@extends('layouts.app')

@section('content')
    <section class="py-5" style="background-color: #f8fafc; min-height: 75vh;">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <p class="text-muted mb-1">Hello {{ $user->name }},</p>
                    <h1 class="h3 fw-semibold mb-2">{{ $title }}</h1>
                    <p class="text-muted mb-0">Stay on top of daily operations, announcements, and campus logistics.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Daily Briefing</h5>
                            <p class="text-muted mb-3">Review upcoming events, maintenance requests, and bulletin updates.
                            </p>
                            <a href="#" class="btn btn-outline-primary btn-sm">View schedule</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Task Manager</h5>
                            <p class="text-muted mb-3">Track administrative workflows and collaborate with other
                                departments.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Open tasks</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
