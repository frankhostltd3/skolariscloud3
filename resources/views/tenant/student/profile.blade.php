@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="mb-0">My Profile</h4>
            <small class="text-muted">Your personal and academic information</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 28px;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 class="mb-1">{{ $user->name ?? 'Your Name' }}</h5>
                    <div class="text-muted mb-2">{{ $user->email ?? 'email@example.com' }}</div>
                    <div>
                        <span class="badge bg-primary">{{ optional($student)->student_number ?? 'ADM-XXXX' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Personal Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input class="form-control" value="{{ $user->name ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <input class="form-control" value="{{ optional($student)->gender ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input class="form-control" value="{{ optional($student)->date_of_birth ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nationality</label>
                            <input class="form-control" value="{{ optional($student)->nationality ?? '—' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Academic Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Current Class</label>
                            <input class="form-control" value="{{ optional($student->classroom ?? null)->name ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stream</label>
                            <input class="form-control" value="{{ optional($student)->stream ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Enrollment Date</label>
                            <input class="form-control" value="{{ optional($student)->created_at ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Homeroom Teacher</label>
                            <input class="form-control" value="{{ optional($student->classroom->teacher ?? null)->name ?? '—' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Contact Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input class="form-control" value="{{ optional($student)->phone ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alternate Phone</label>
                            <input class="form-control" value="{{ optional($student)->alt_phone ?? '—' }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input class="form-control" value="{{ optional($student)->address ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Name</label>
                            <input class="form-control" value="{{ optional($student)->guardian_name ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Contact</label>
                            <input class="form-control" value="{{ optional($student)->guardian_phone ?? '—' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
