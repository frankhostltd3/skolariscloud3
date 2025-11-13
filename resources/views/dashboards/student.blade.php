@extends('layouts.app')

@section('content')
    <section class="py-5" style="background-color: #f5f9ff; min-height: 75vh;">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <p class="text-muted mb-1">Hey {{ $user->name }}!</p>
                    <h1 class="h3 fw-semibold mb-2">{{ $title }}</h1>
                    <p class="text-muted mb-0">Stay organised with your classes, assignments, and campus activities.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Class Schedule</h5>
                            <p class="text-muted mb-3">See today's timetable and upcoming lessons at a glance.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">View schedule</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Assignments</h5>
                            <p class="text-muted mb-3">Track due dates, submit work, and review teacher feedback.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Open assignments</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Campus Life</h5>
                            <p class="text-muted mb-3">Get involved with clubs, events, and student communities.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Explore events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
