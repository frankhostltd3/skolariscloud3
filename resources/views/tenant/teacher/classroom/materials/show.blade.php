@extends('layouts.dashboard-teacher')

@section('title', $material->title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                @if($material->type == 'document')
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                @elseif($material->type == 'video')
                    <i class="bi bi-play-circle me-2 text-danger"></i>
                @elseif($material->type == 'youtube')
                    <i class="bi bi-youtube me-2 text-danger"></i>
                @elseif($material->type == 'link')
                    <i class="bi bi-link-45deg me-2 text-info"></i>
                @elseif($material->type == 'image')
                    <i class="bi bi-image me-2 text-success"></i>
                @elseif($material->type == 'audio')
                    <i class="bi bi-music-note me-2 text-warning"></i>
                @endif
                {{ $material->title }}
            </h1>
            <p class="text-muted mb-0">
                <span class="badge bg-primary">{{ ucfirst($material->type) }}</span>
                <span class="ms-2">{{ $material->class->name }} - {{ $material->subject->name }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.materials.edit', $material) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('tenant.teacher.classroom.materials.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Views</p>
                            <h3 class="mb-0">{{ $material->views_count }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-eye fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Downloads</p>
                            <h3 class="mb-0">{{ $material->downloads_count }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-download fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Students Accessed</p>
                            <h3 class="mb-0">{{ $material->accesses->unique('student_id')->count() }}</h3>
                            <small class="text-muted">of {{ $material->class->students()->count() }}</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">File Size</p>
                            <h3 class="mb-0">
                                @if($material->file_size)
                                    {{ $material->file_size_formatted }}
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hdd fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Material Preview/Content -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-play-circle me-2 text-primary"></i>Material Content</h5>
                </div>
                <div class="card-body">
                    @if($material->type === 'youtube' && $material->youtube_id)
                        <!-- YouTube Embed -->
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/{{ $material->youtube_id }}" 
                                    title="{{ $material->title }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                    @elseif($material->type === 'link')
                        <!-- External Link -->
                        <div class="text-center py-5">
                            <i class="bi bi-link-45deg display-1 text-primary mb-3"></i>
                            <h5>External Resource</h5>
                            <p class="text-muted mb-4">{{ Str::limit($material->external_url, 80) }}</p>
                            <a href="{{ $material->external_url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Open Link
                            </a>
                        </div>
                    @elseif($material->type === 'image' && $material->file_path)
                        <!-- Image Preview -->
                        <div class="text-center">
                            <img src="{{ $material->file_url }}" alt="{{ $material->title }}" 
                                 class="img-fluid rounded" style="max-height: 500px;">
                        </div>
                    @elseif($material->type === 'video' && $material->file_path)
                        <!-- Video Player -->
                        <div class="ratio ratio-16x9">
                            <video controls>
                                <source src="{{ $material->file_url }}" type="{{ $material->file_mime }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @elseif($material->type === 'audio' && $material->file_path)
                        <!-- Audio Player -->
                        <div class="text-center py-4">
                            <i class="bi bi-music-note-beamed display-1 text-warning mb-3"></i>
                            <audio controls class="w-100">
                                <source src="{{ $material->file_url }}" type="{{ $material->file_mime }}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    @elseif($material->type === 'document' && $material->file_path)
                        <!-- Document Info -->
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text display-1 text-primary mb-3"></i>
                            <h5>{{ basename($material->file_path) }}</h5>
                            <p class="text-muted">{{ $material->file_size_formatted }}</p>
                            <a href="{{ route('tenant.teacher.classroom.materials.download', $material) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>Download Document
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark display-1 text-muted mb-3"></i>
                            <p class="text-muted">No preview available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            @if($material->description)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $material->description }}</p>
                </div>
            </div>
            @endif

            <!-- Student Access Log -->
            @if($material->accesses->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Access</h5>
                    <span class="badge bg-secondary">{{ $material->accesses->count() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Access Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($material->accesses()->with('student')->latest()->take(10)->get() as $access)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ substr($access->student->first_name, 0, 1) }}{{ substr($access->student->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $access->student->full_name }}</div>
                                                <small class="text-muted">{{ $access->student->student_id ?? $access->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $access->created_at->format('M j, Y g:i A') }}</small><br>
                                        <small class="text-muted">{{ $access->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="bi bi-eye me-1"></i>Viewed
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($material->accesses->count() > 10)
                <div class="card-footer bg-white border-top text-center">
                    <small class="text-muted">Showing 10 most recent of {{ $material->accesses->count() }} total accesses</small>
                </div>
                @endif
            </div>
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                    <h5>No Access Yet</h5>
                    <p class="text-muted mb-0">No students have accessed this material yet.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($material->file_path)
                        <a href="{{ route('tenant.teacher.classroom.materials.download', $material) }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Download File
                        </a>
                        @elseif($material->external_url)
                        <a href="{{ $material->external_url }}" target="_blank" class="btn btn-primary">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Open Link
                        </a>
                        @endif
                        
                        <a href="{{ route('tenant.teacher.classroom.materials.edit', $material) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Material
                        </a>
                        
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>Delete Material
                        </button>
                    </div>
                </div>
            </div>

            <!-- Material Details -->
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Material Details</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <strong>Type:</strong> {{ ucfirst($material->type) }}
                        </li>
                        <li class="mb-2">
                            <strong>Class:</strong> {{ $material->class->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Subject:</strong> {{ $material->subject->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Teacher:</strong> {{ $material->teacher->full_name }}
                        </li>
                        <li class="mb-2">
                            <strong>Downloadable:</strong> 
                            @if($material->is_downloadable)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <strong>Created:</strong> {{ $material->created_at->format('M j, Y g:i A') }}
                        </li>
                        <li class="mb-0">
                            <strong>Last Updated:</strong> {{ $material->updated_at->format('M j, Y g:i A') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Usage Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2 text-success"></i>Usage Statistics</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalStudents = $material->class->students()->count();
                        $accessedStudents = $material->accesses->unique('student_id')->count();
                        $accessRate = $totalStudents > 0 ? round(($accessedStudents / $totalStudents) * 100) : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Student Access Rate</small>
                            <small><strong>{{ $accessRate }}%</strong></small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $accessRate }}%" 
                                 aria-valuenow="{{ $accessRate }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">{{ $accessedStudents }} of {{ $totalStudents }} students</small>
                    </div>

                    <hr>

                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <i class="bi bi-eye text-primary me-2"></i>
                            <strong>{{ $material->views_count }}</strong> total views
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-download text-success me-2"></i>
                            <strong>{{ $material->downloads_count }}</strong> total downloads
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this material?</p>
                <p><strong>"{{ $material->title }}"</strong></p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. The file and all access history will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('tenant.teacher.classroom.materials.destroy', $material) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Material</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endpush
@endsection
