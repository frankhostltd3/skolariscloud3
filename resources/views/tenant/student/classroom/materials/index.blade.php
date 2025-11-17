@extends('layouts.tenant.student')

@section('title', 'Learning Materials')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>Learning Materials
            </h2>
            <p class="text-muted mb-0">Browse and access your learning resources</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('tenant.student.classroom.materials.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search materials..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="all">All Types</option>
                        <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Document</option>
                        <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
                        <option value="youtube" {{ request('type') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                        <option value="link" {{ request('type') === 'link' ? 'selected' : '' }}>Link</option>
                        <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Image</option>
                        <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Materials Grid -->
    @if($materials->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($materials as $material)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 hover-shadow">
                        <div class="card-body">
                            <!-- Type Icon -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-{{ $material->type === 'document' ? 'file-earmark-pdf' : ($material->type === 'video' ? 'play-circle' : ($material->type === 'youtube' ? 'youtube' : ($material->type === 'link' ? 'link-45deg' : ($material->type === 'image' ? 'image' : 'file-earmark')))) }} text-primary fs-3"></i>
                                </div>
                                <span class="badge bg-primary">{{ ucfirst($material->type) }}</span>
                            </div>

                            <!-- Title -->
                            <h5 class="card-title mb-2">{{ Str::limit($material->title, 50) }}</h5>

                            <!-- Meta Info -->
                            <p class="text-muted small mb-3">
                                <i class="bi bi-book me-1"></i>{{ $material->subject->name }}<br>
                                <i class="bi bi-person me-1"></i>{{ $material->teacher->name }}<br>
                                <i class="bi bi-clock me-1"></i>{{ $material->created_at->diffForHumans() }}
                            </p>

                            <!-- Description -->
                            @if($material->description)
                                <p class="text-muted small mb-3">
                                    {{ Str::limit($material->description, 100) }}
                                </p>
                            @endif

                            <!-- Stats -->
                            <div class="d-flex gap-3 text-muted small mb-3">
                                <span><i class="bi bi-eye me-1"></i>{{ $material->views_count }} views</span>
                                @if($material->is_downloadable)
                                    <span><i class="bi bi-download me-1"></i>{{ $material->downloads_count }} downloads</span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('tenant.student.classroom.materials.show', $material) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                                @if($material->is_downloadable && in_array($material->type, ['document', 'video', 'image', 'audio']))
                                    <a href="{{ route('tenant.student.classroom.materials.download', $material) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $materials->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Materials Found</h4>
                <p class="text-muted">There are no learning materials available at the moment.</p>
            </div>
        </div>
    @endif
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
