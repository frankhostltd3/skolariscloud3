@extends('layouts.app')

@section('content')
    <section class="py-5 bg-light" style="min-height: 75vh;">
        <div class="container">
            <div class="row justify-content-between align-items-center mb-4">
                <div class="col-lg-8">
                    <p class="text-muted mb-1">Welcome back, {{ $user->name }}.</p>
                    <h1 class="display-6 fw-semibold mb-2">{{ $title }}</h1>
                    <p class="text-muted mb-0">Monitor key metrics, manage staff, and keep your campus running smoothly.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Operations Center</h5>
                            <p class="text-muted mb-3">Review school-wide performance, manage permissions, and assign roles.
                            </p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Manage roles</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Financial Snapshot</h5>
                            <p class="text-muted mb-3">Keep an eye on budgets, payments, and outstanding balances at a
                                glance.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">View finance tools</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-2">Community Updates</h5>
                            <p class="text-muted mb-3">Communicate with staff, parents, and students through unified
                                channels.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Open communications</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
