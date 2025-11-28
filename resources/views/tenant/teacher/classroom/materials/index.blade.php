@extends('layouts.dashboard-teacher')

@section('title', 'Learning Materials')

@section('content')
    @php($materialsAvailable = $materialsAvailable ?? true)

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-folder me-2 text-primary"></i>Learning Materials
                </h1>
                <p class="text-muted mb-0">Share documents, videos, and resources with students</p>
            </div>
            <div>
                @if ($materialsAvailable)
                    <a href="{{ route('tenant.teacher.classroom.materials.create') }}" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-2"></i>Upload Material
                    </a>
                @else
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="bi bi-lock me-2"></i>Uploads Unavailable
                    </button>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @unless ($materialsAvailable)
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                Learning materials are not enabled for this school yet. Once the tenant database includes the supporting tables
                you will be able to upload and manage resources.
            </div>
        @endunless

        <!-- Filter Pills -->
        <div class="mb-4">
            <button class="btn btn-sm btn-outline-primary active me-2">
                <i class="bi bi-grid-3x3-gap me-1"></i>All
            </button>
            <button class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-file-earmark-pdf me-1"></i>Documents
            </button>
            <button class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-camera-video me-1"></i>Videos
            </button>
            <button class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-youtube me-1"></i>YouTube
            </button>
            <button class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-link-45deg me-1"></i>Links
            </button>
        </div>

        <!-- Materials Grid -->
        <div class="row g-4">
            @if ($materialsAvailable && $materials->count() > 0)
                @foreach ($materials as $material)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary text-uppercase">{{ ucfirst($material->type) }}</span>
                                <span class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ optional($material->created_at)->format('M d, Y') ?? '—' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-2">{{ $material->title }}</h5>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-collection me-1"></i>{{ $material->class->name ?? 'No class' }}
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-book me-1"></i>{{ $material->subject->name ?? 'No subject' }}
                                </p>
                                @if ($material->description)
                                    <p class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($material->description, 140) }}</p>
                                @endif
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-3 text-muted small">
                                    <span><i class="bi bi-eye me-1"></i>{{ $material->views_count ?? 0 }}</span>
                                    <span><i class="bi bi-download me-1"></i>{{ $material->downloads_count ?? 0 }}</span>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('tenant.teacher.classroom.materials.show', $material) }}"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.teacher.classroom.materials.edit', $material) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-12">
                    {{ $materials->links() }}
                </div>
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-folder-x text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No Learning Materials Yet</h5>
                            <p class="text-muted mb-4">Upload your first document, video, or resource</p>
                            @if ($materialsAvailable)
                                <a href="{{ route('tenant.teacher.classroom.materials.create') }}" class="btn btn-primary">
                                    <i class="bi bi-cloud-upload me-2"></i>Upload First Material
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mt-4">
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <i class="bi bi-file-earmark text-primary" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ data_get($stats ?? [], 'total', 0) }}</h4>
                        <small class="text-muted">Documents</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <i class="bi bi-camera-video text-info" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ data_get($stats ?? [], 'videos', 0) }}</h4>
                        <small class="text-muted">Videos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <i class="bi bi-eye text-success" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ number_format(data_get($stats ?? [], 'total_views', 0)) }}</h4>
                        <small class="text-muted">Total Views</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <i class="bi bi-download text-warning" style="font-size: 2rem;"></i>
                        <h4 class="mt-2 mb-0">{{ number_format(data_get($stats ?? [], 'total_downloads', 0)) }}</h4>
                        <small class="text-muted">Downloads</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
