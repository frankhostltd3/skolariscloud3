@extends('layouts.tenant.student')

@section('title', 'Recently Accessed Materials')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recently Accessed Materials
                </h2>
                <p class="text-muted mb-0">Materials you've viewed recently</p>
            </div>
            <a href="{{ route('tenant.student.classroom.materials.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i>All Materials
            </a>
        </div>

        <!-- Recent Materials -->
        @if ($recentAccesses->count() > 0)
            <div class="row g-4 mb-4">
                @foreach ($recentAccesses as $material)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 hover-shadow">
                            <div class="card-body">
                                <!-- Type Icon -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i
                                            class="bi bi-{{ $material->type === 'document' ? 'file-earmark-pdf' : ($material->type === 'video' ? 'play-circle' : ($material->type === 'youtube' ? 'youtube' : ($material->type === 'link' ? 'link-45deg' : ($material->type === 'image' ? 'image' : 'file-earmark')))) }} text-primary fs-3"></i>
                                    </div>
                                    <span class="badge bg-primary">{{ ucfirst($material->type) }}</span>
                                </div>

                                <!-- Title -->
                                <h5 class="card-title mb-2">{{ Str::limit($material->title, 50) }}</h5>

                                <!-- Meta Info -->
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-book me-1"></i>{{ $material->subject->name }}<br>
                                    <i class="bi bi-diagram-3 me-1"></i>{{ $material->class->name }}<br>
                                    <i class="bi bi-person me-1"></i>{{ $material->teacher->name }}<br>
                                    <i class="bi bi-clock me-1"></i>{{ $material->created_at->diffForHumans() }}
                                </p>

                                <!-- Description -->
                                @if ($material->description)
                                    <p class="text-muted small mb-3">
                                        {{ Str::limit($material->description, 100) }}
                                    </p>
                                @endif

                                <!-- Stats -->
                                <div class="d-flex gap-3 text-muted small mb-3">
                                    <span><i class="bi bi-eye me-1"></i>{{ $material->views_count ?? 0 }} views</span>
                                    @if ($material->is_downloadable)
                                        <span><i class="bi bi-download me-1"></i>{{ $material->downloads_count ?? 0 }}
                                            downloads</span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('tenant.student.classroom.materials.show', $material) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>View Again
                                    </a>
                                    @if ($material->is_downloadable && in_array($material->type, ['document', 'video', 'image', 'audio']))
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
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Recently Accessed Materials</h4>
                    <p class="text-muted">You haven't accessed any learning materials yet.</p>
                    <a href="{{ route('tenant.student.classroom.materials.index') }}" class="btn btn-primary mt-3">
                        Browse Materials
                    </a>
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
