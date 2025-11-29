@extends('layouts.tenant.student')

@section('title', 'My Classes')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1 fw-bold text-dark">My Classes</h2>
                <p class="text-muted mb-0">Access your enrolled subjects and learning materials</p>
            </div>
            <div>
                <span class="badge bg-white text-primary border px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-calendar-check me-1"></i>
                    {{ now()->format('F Y') }}
                </span>
            </div>
        </div>

        @if ($enrolledClasses->count() > 0)
            <div class="row g-4">
                @foreach ($enrolledClasses as $enrollment)
                    @php
                        $class = $enrollment->schoolClass;
                        $academicYear = $enrollment->academicYear;
                        // Generate a random gradient for the card header
                        $gradients = [
                            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            'linear-gradient(135deg, #6B73FF 0%, #000DFF 100%)',
                            'linear-gradient(135deg, #20E2D7 0%, #F9FEA5 100%)',
                            'linear-gradient(135deg, #FF9A9E 0%, #FECFEF 99%, #FECFEF 100%)',
                            'linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%)',
                            'linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%)',
                        ];
                        $bgGradient = $gradients[$loop->index % count($gradients)];
                        $textColor = in_array($loop->index % count($gradients), [2, 3, 4, 5])
                            ? 'text-dark'
                            : 'text-white';
                        $badgeClass = in_array($loop->index % count($gradients), [2, 3, 4, 5])
                            ? 'bg-dark text-white'
                            : 'bg-white text-primary';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-lift transition-all">
                            <div class="card-header border-0 py-4 px-4 position-relative overflow-hidden"
                                style="background: {{ $bgGradient }}; min-height: 140px;">
                                <div class="position-relative z-1">
                                    <span class="badge {{ $badgeClass }} bg-opacity-75 backdrop-blur mb-2">
                                        {{ $class->educationLevel->name ?? 'Class' }}
                                    </span>
                                    <h4 class="card-title mb-1 fw-bold {{ $textColor }}">{{ $class->name }}</h4>
                                    <p class="mb-0 small {{ $textColor }} opacity-75">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $academicYear->name ?? 'Current Year' }}
                                    </p>
                                </div>
                                <!-- Decorative icon -->
                                <div class="position-absolute top-0 end-0 mt-n2 me-n2 opacity-10">
                                    <i class="bi bi-mortarboard-fill" style="font-size: 8rem; color: white;"></i>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-group">
                                            <div class="avatar avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center text-primary border border-white"
                                                title="Class Teacher">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                        </div>
                                        <span class="ms-2 small text-muted">Class Teacher</span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle me-1"></i> Active
                                    </span>
                                </div>

                                <div class="row g-2 mb-4">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded text-center border border-light-subtle">
                                            <h5 class="mb-0 fw-bold text-primary">{{ $class->subjects_count ?? '-' }}</h5>
                                            <small class="text-muted text-uppercase fw-bold"
                                                style="font-size: 0.65rem;">Subjects</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded text-center border border-light-subtle">
                                            <h5 class="mb-0 fw-bold text-info">{{ $class->students_count ?? '-' }}</h5>
                                            <small class="text-muted text-uppercase fw-bold"
                                                style="font-size: 0.65rem;">Classmates</small>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('tenant.student.classroom.classes.show', $class->id) }}"
                                    class="btn btn-primary w-100 rounded-pill py-2 fw-medium shadow-sm">
                                    Enter Classroom <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px;">
                        <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <h3 class="text-muted fw-bold">No classes found</h3>
                <p class="text-muted mb-4">You are not currently enrolled in any active classes.</p>
                <a href="{{ route('tenant.student.dashboard') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i> Return to Dashboard
                </a>
            </div>
        @endif
    </div>

    <style>
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        }

        .backdrop-blur {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .avatar {
            width: 32px;
            height: 32px;
        }

        .bg-success-subtle {
            background-color: #d1e7dd;
        }

        .text-success {
            color: #198754;
        }
    </style>
@endsection
