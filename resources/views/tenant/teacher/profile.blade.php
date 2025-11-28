@extends('layouts.dashboard-teacher')

@section('title', 'Teacher Profile')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="avatar-circle mb-3">
                            <i class="fas fa-user-circle fa-5x text-secondary"></i>
                        </div>
                        <h5>{{ auth()->user()->name ?? 'Teacher Name' }}</h5>
                        <p class="text-muted">{{ auth()->user()->email ?? 'teacher@example.com' }}</p>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" value="{{ auth()->user()->email ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" class="form-control" value="{{ $user->phone ?? 'Not provided' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee ID</label>
                                <input type="text" class="form-control" value="{{ $user->employee_id ?? 'Not assigned' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department</label>
                                <input type="text" class="form-control" value="{{ $user->department ?? 'Not assigned' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Joining Date</label>
                                <input type="text" class="form-control" value="{{ $user->joining_date ? $user->joining_date->format('M d, Y') : 'Not set' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection