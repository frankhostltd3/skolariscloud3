@extends('layouts.tenant.student')

@section('title', $material->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('tenant.student.classroom.index') }}">Classroom</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tenant.student.classroom.materials.index') }}">Materials</a></li>
                <li class="breadcrumb-item active">{{ $material->title }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 class="mb-2">
                    <i class="bi bi-{{ $material->type === 'document' ? 'file-earmark-pdf' : ($material->type === 'video' ? 'play-circle' : ($material->type === 'youtube' ? 'youtube' : ($material->type === 'link' ? 'link-45deg' : ($material->type === 'image' ? 'image' : 'file-earmark')))) }} me-2"></i>
                    {{ $material->title }}
                </h2>
                <p class="text-muted mb-0">
                    <i class="bi bi-book me-1"></i>{{ $material->subject->name }} • 
                    <i class="bi bi-person ms-2 me-1"></i>{{ $material->teacher->name }} • 
                    <i class="bi bi-clock ms-2 me-1"></i>{{ $material->created_at->diffForHumans() }}
                </p>
            </div>
            @if($material->is_downloadable && in_array($material->type, ['document', 'video', 'image', 'audio']))
                <a href="{{ route('tenant.student.classroom.materials.download', $material) }}" 
                   class="btn btn-primary">
                    <i class="bi bi-download me-1"></i>Download
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Material Preview -->
                    @if($material->type === 'youtube' && $material->youtube_id)
                        <div class="ratio ratio-16x9 mb-3">
                            <iframe src="https://www.youtube.com/embed/{{ $material->youtube_id }}" 
                                    allowfullscreen></iframe>
                        </div>
                    @elseif($material->type === 'link')
                        <div class="alert alert-info">
                            <i class="bi bi-link-45deg fs-3"></i>
                            <h5 class="mt-2">External Link</h5>
                            <p class="mb-3">This material is hosted externally. Click the button below to access it.</p>
                            <a href="{{ $material->url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open Link
                            </a>
                        </div>
                    @elseif($material->type === 'image')
                        <div class="text-center mb-3">
                            <img src="{{ $material->file_url }}" alt="{{ $material->title }}" 
                                 class="img-fluid rounded" style="max-height: 600px;">
                        </div>
                    @elseif($material->type === 'video')
                        <div class="mb-3">
                            <video controls class="w-100" style="max-height: 500px;">
                                <source src="{{ $material->file_url }}" type="{{ $material->file_type ?? 'video/mp4' }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @elseif($material->type === 'audio')
                        <div class="text-center p-5 bg-light rounded mb-3">
                            <i class="bi bi-music-note-beamed text-primary" style="font-size: 4rem;"></i>
                            <audio controls class="w-100 mt-3">
                                <source src="{{ $material->file_url }}" type="{{ $material->file_type ?? 'audio/mpeg' }}">
                                Your browser does not support the audio tag.
                            </audio>
                        </div>
                    @elseif($material->type === 'document')
                        <div class="text-center p-5 bg-light rounded mb-3">
                            <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">{{ $material->file_name }}</h5>
                            <p class="text-muted">
                                Size: {{ \App\Services\FileUploadService::formatFileSize($material->file_size) }}
                            </p>
                            @if($material->is_downloadable)
                                <a href="{{ route('tenant.student.classroom.materials.download', $material) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-download me-1"></i>Download to View
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Description -->
                    @if($material->description)
                        <div class="mt-4">
                            <h5 class="mb-3">Description</h5>
                            <p class="text-muted">{{ $material->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Material Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Material Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Type</small>
                        <span class="badge bg-primary">{{ ucfirst($material->type) }}</span>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Class</small>
                        <strong>{{ $material->class->name }}</strong>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Subject</small>
                        <strong>{{ $material->subject->name }}</strong>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Teacher</small>
                        <strong>{{ $material->teacher->name }}</strong>
                    </div>
                    @if(in_array($material->type, ['document', 'video', 'image', 'audio']))
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">File Size</small>
                            <strong>{{ \App\Services\FileUploadService::formatFileSize($material->file_size) }}</strong>
                        </div>
                    @endif
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Downloadable</small>
                        @if($material->is_downloadable)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Uploaded</small>
                        <strong>{{ $material->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($material->type === 'link')
                            <a href="{{ $material->url }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open Link
                            </a>
                        @elseif($material->is_downloadable && in_array($material->type, ['document', 'video', 'image', 'audio']))
                            <a href="{{ route('tenant.student.classroom.materials.download', $material) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-download me-1"></i>Download
                            </a>
                        @endif
                        <a href="{{ route('tenant.student.classroom.materials.by-subject', $material->subject_id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-collection me-1"></i>More from {{ $material->subject->name }}
                        </a>
                        <a href="{{ route('tenant.student.classroom.materials.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
