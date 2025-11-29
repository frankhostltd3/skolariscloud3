@extends('layouts.tenant.student')

@section('title', $class->name)

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.student.classroom.classes') }}"
                                class="text-decoration-none">My Classes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $class->name }}</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold text-dark">
                    {{ $class->name }}
                </h2>
                <p class="text-muted mb-0">
                    <span
                        class="badge bg-light text-dark border me-2">{{ $class->educationLevel->name ?? 'General' }}</span>
                    @if ($class->streams->count() > 0)
                        <span class="text-muted small"><i class="bi bi-diagram-3 me-1"></i> Streams:
                            {{ $class->streams->pluck('name')->join(', ') }}</span>
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('tenant.student.classroom.classes') }}"
                    class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Classes
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-muted mb-1 fw-medium text-uppercase small">Virtual Classes</p>
                                <h3 class="mb-0 fw-bold">{{ $stats['attended'] }} <span class="text-muted fs-6 fw-normal">/
                                        {{ $stats['total_classes'] }}</span></h3>
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ $stats['total_classes'] > 0 ? ($stats['attended'] / $stats['total_classes']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-camera-video text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-muted mb-1 fw-medium text-uppercase small">Learning Materials</p>
                                <h3 class="mb-0 fw-bold">{{ $stats['materials'] }}</h3>
                                <small class="text-success"><i class="bi bi-arrow-up-short"></i> Available Resources</small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-book text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-muted mb-1 fw-medium text-uppercase small">Assignments</p>
                                <h3 class="mb-0 fw-bold">{{ $stats['assignments'] }}</h3>
                                <small class="text-muted">Total Tasks</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-pencil-square text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Virtual Classes -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-camera-video me-2 text-primary"></i>Recent
                                Virtual Classes</h5>
                            <a href="{{ route('tenant.student.classroom.virtual.index') }}"
                                class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentClasses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentClasses as $virtualClass)
                                    <div class="list-group-item px-4 py-3 border-light hover-bg-light transition-all">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 bg-light rounded p-2 text-center" style="min-width: 50px;">
                                                    <span
                                                        class="d-block fw-bold text-dark">{{ $virtualClass->scheduled_at->format('d') }}</span>
                                                    <span class="d-block small text-muted text-uppercase"
                                                        style="font-size: 0.65rem;">{{ $virtualClass->scheduled_at->format('M') }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold text-dark">{{ $virtualClass->title }}</h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $virtualClass->scheduled_at->format('h:i A') }}
                                                    </small>
                                                </div>
                                            </div>
                                            <span
                                                class="badge rounded-pill bg-{{ $virtualClass->status === 'completed' ? 'success-subtle text-success' : ($virtualClass->status === 'ongoing' ? 'primary-subtle text-primary' : 'secondary-subtle text-secondary') }} border border-{{ $virtualClass->status === 'completed' ? 'success-subtle' : ($virtualClass->status === 'ongoing' ? 'primary-subtle' : 'secondary-subtle') }}">
                                                {{ ucfirst($virtualClass->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-camera-video text-muted opacity-25" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-muted mb-0">No recent virtual classes found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Materials -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-folder2-open me-2 text-success"></i>Recent
                                Materials</h5>
                            <a href="{{ route('tenant.student.classroom.materials.index') }}"
                                class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentMaterials->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentMaterials as $material)
                                    <div class="list-group-item px-4 py-3 border-light hover-bg-light transition-all">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @php
                                                        $icon = match ($material->type) {
                                                            'pdf' => 'bi-file-pdf text-danger',
                                                            'video' => 'bi-file-play text-primary',
                                                            'image' => 'bi-file-image text-info',
                                                            'audio' => 'bi-file-music text-warning',
                                                            default => 'bi-file-text text-secondary',
                                                        };
                                                    @endphp
                                                    <i class="bi {{ $icon }} fs-3"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold text-dark">{{ $material->title }}</h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        {{ $material->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                            <a href="{{ route('tenant.student.classroom.materials.show', $material->id) }}"
                                                class="btn btn-sm btn-light rounded-circle shadow-sm"
                                                title="View Material">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-folder-x text-muted opacity-25" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-muted mb-0">No recent materials found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }

        .transition-all {
            transition: all 0.2s ease;
        }

        .bg-success-subtle {
            background-color: #d1e7dd;
        }

        .bg-primary-subtle {
            background-color: #cfe2ff;
        }

        .bg-secondary-subtle {
            background-color: #e2e3e5;
        }
    </style>
@endsection
