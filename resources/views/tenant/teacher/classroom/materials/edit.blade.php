@extends('layouts.dashboard-teacher')

@section('title', 'Edit Learning Material')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-pencil me-2 text-primary"></i>Edit Learning Material</h1>
            <p class="text-muted mb-0">Update material details</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.materials.show', $material) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tenant.teacher.classroom.materials.update', $material) }}" method="POST" enctype="multipart/form-data" id="materialForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $material->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $material->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach(\App\Models\SchoolClass::orderBy('name')->get() as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $material->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" 
                                        id="subject_id" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach(\App\Models\Subject::orderBy('name')->get() as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $material->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Material Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="document" {{ old('type', $material->type) == 'document' ? 'selected' : '' }}>
                                    📄 Document (PDF, Word, Excel, PowerPoint)
                                </option>
                                <option value="video" {{ old('type', $material->type) == 'video' ? 'selected' : '' }}>
                                    🎥 Video File (MP4, AVI, MOV)
                                </option>
                                <option value="youtube" {{ old('type', $material->type) == 'youtube' ? 'selected' : '' }}>
                                    📺 YouTube Video
                                </option>
                                <option value="link" {{ old('type', $material->type) == 'link' ? 'selected' : '' }}>
                                    🔗 External Link/Website
                                </option>
                                <option value="image" {{ old('type', $material->type) == 'image' ? 'selected' : '' }}>
                                    🖼️ Image (JPG, PNG, GIF)
                                </option>
                                <option value="audio" {{ old('type', $material->type) == 'audio' ? 'selected' : '' }}>
                                    🎵 Audio (MP3, WAV)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current File Display -->
                        @if($material->file_path || $material->external_url)
                        <div class="alert alert-info mb-3">
                            <strong>Current {{ $material->type === 'youtube' || $material->type === 'link' ? 'Link' : 'File' }}:</strong><br>
                            @if($material->file_path)
                                <i class="bi bi-file-earmark me-2"></i>
                                {{ basename($material->file_path) }}
                                <span class="text-muted">({{ $material->file_size_formatted }})</span>
                            @elseif($material->external_url)
                                <i class="bi bi-link-45deg me-2"></i>
                                <a href="{{ $material->external_url }}" target="_blank" class="text-decoration-none">
                                    {{ Str::limit($material->external_url, 60) }}
                                </a>
                            @endif
                        </div>
                        @endif

                        <!-- File Upload Section -->
                        <div class="mb-3" id="file-upload-section">
                            <label for="file" class="form-label">
                                {{ $material->file_path ? 'Replace File (Optional)' : 'Upload File' }}
                                <small class="text-muted">(Max: 50MB)</small>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="file-help">
                                @if($material->file_path)
                                    Leave empty to keep the current file
                                @else
                                    Upload a new file
                                @endif
                            </div>
                        </div>

                        <!-- URL Section -->
                        <div class="mb-3 {{ in_array($material->type, ['youtube', 'link']) ? '' : 'd-none' }}" id="url-section">
                            <label for="external_url" class="form-label">URL</label>
                            <input type="url" class="form-control @error('external_url') is-invalid @enderror" 
                                   id="external_url" name="external_url" 
                                   value="{{ old('external_url', $material->external_url) }}"
                                   placeholder="https://...">
                            @error('external_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="url-help">
                                Enter the full URL including https://
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_downloadable" 
                                   name="is_downloadable" value="1" 
                                   {{ old('is_downloadable', $material->is_downloadable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_downloadable">
                                <i class="bi bi-download me-1"></i>Allow students to download this material
                            </label>
                        </div>

                        <hr>

                        <!-- Warning about file replacement -->
                        @if($material->file_path)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> If you upload a new file, the current file will be permanently replaced.
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tenant.teacher.classroom.materials.show', $material) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Update Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Material Stats -->
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-bar-chart me-2 text-info"></i>Material Stats</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <strong>Views:</strong> {{ $material->views_count ?? 0 }}
                        </li>
                        <li class="mb-2">
                            <strong>Downloads:</strong> {{ $material->downloads_count ?? 0 }}
                        </li>
                        <li class="mb-2">
                            <strong>Created:</strong> {{ $material->created_at->format('M j, Y') }}
                        </li>
                        <li class="mb-0">
                            <strong>Last Updated:</strong> {{ $material->updated_at->format('M j, Y') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tips -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">You can change the title and description without replacing the file</li>
                        <li class="mb-2">Only upload a new file if you want to replace the existing one</li>
                        <li class="mb-2">Changing the material type may require uploading a new file</li>
                        <li class="mb-0">Student access history will be preserved</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const fileSection = document.getElementById('file-upload-section');
    const urlSection = document.getElementById('url-section');
    const fileInput = document.getElementById('file');
    const urlHelp = document.getElementById('url-help');
    const downloadCheckbox = document.getElementById('is_downloadable');
    
    // Type change handler
    typeSelect.addEventListener('change', function() {
        const type = this.value;
        
        if (type === 'youtube' || type === 'link') {
            fileSection.classList.add('d-none');
            urlSection.classList.remove('d-none');
            fileInput.removeAttribute('required');
            
            if (type === 'youtube') {
                urlHelp.textContent = 'Paste the YouTube video URL';
                downloadCheckbox.checked = false;
                downloadCheckbox.disabled = true;
            } else {
                urlHelp.textContent = 'Enter the full URL of the external resource';
                downloadCheckbox.disabled = false;
            }
        } else {
            fileSection.classList.remove('d-none');
            urlSection.classList.add('d-none');
            downloadCheckbox.disabled = false;
        }
    });
    
    // Initialize based on current type
    typeSelect.dispatchEvent(new Event('change'));
    
    // Form submission
    document.getElementById('materialForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
    });
});
</script>
@endpush
@endsection
