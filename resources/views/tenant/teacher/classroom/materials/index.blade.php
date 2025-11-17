@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Learning Materials')

@section('content')
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
            <a href="{{ route('tenant.teacher.classroom.materials.create') }}" class="btn btn-primary">
                <i class="bi bi-cloud-upload me-2"></i>Upload Material
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
        <!-- Empty State -->
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-folder-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Learning Materials Yet</h5>
                    <p class="text-muted mb-4">Upload your first document, video, or resource</p>
                    <a href="{{ route('tenant.teacher.classroom.materials.create') }}" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-2"></i>Upload First Material
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mt-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-file-earmark text-primary" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Documents</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-camera-video text-info" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Videos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-eye text-success" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Total Views</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-download text-warning" style="font-size: 2rem;"></i>
                    <h4 class="mt-2 mb-0">0</h4>
                    <small class="text-muted">Downloads</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

