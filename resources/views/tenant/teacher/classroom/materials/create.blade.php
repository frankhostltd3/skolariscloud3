@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Upload Learning Material')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-cloud-upload me-2 text-primary"></i>Upload Learning Material</h1>
            <p class="text-muted mb-0">Share resources with your students</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classroom.materials.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tenant.teacher.classroom.materials.store') }}" method="POST" enctype="multipart/form-data" id="materialForm">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="e.g., Chapter 5 Study Guide" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of this material...">{{ old('description') }}</textarea>
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
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
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
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>
                                    📄 Document (PDF, Word, Excel, PowerPoint)
                                </option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>
                                    🎥 Video File (MP4, AVI, MOV)
                                </option>
                                <option value="youtube" {{ old('type') == 'youtube' ? 'selected' : '' }}>
                                    📺 YouTube Video
                                </option>
                                <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>
                                    🔗 External Link/Website
                                </option>
                                <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>
                                    🖼️ Image (JPG, PNG, GIF)
                                </option>
                                <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>
                                    🎵 Audio (MP3, WAV)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-3" id="file-upload-section">
                            <label for="file" class="form-label">
                                Upload File <span class="text-danger">*</span>
                                <small class="text-muted">(Max: 50MB)</small>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="file-help">
                                Accepted formats will be shown based on material type selected above.
                            </div>
                            
                            <!-- File Preview -->
                            <div id="file-preview" class="mt-2 d-none">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="bi bi-file-earmark fs-4 me-3"></i>
                                    <div>
                                        <strong id="file-name"></strong><br>
                                        <small id="file-size" class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- URL Section (Hidden by default) -->
                        <div class="mb-3 d-none" id="url-section">
                            <label for="external_url" class="form-label">
                                URL <span class="text-danger">*</span>
                            </label>
                            <input type="url" class="form-control @error('external_url') is-invalid @enderror" 
                                   id="external_url" name="external_url" value="{{ old('external_url') }}"
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
                                   name="is_downloadable" value="1" {{ old('is_downloadable', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_downloadable">
                                <i class="bi bi-download me-1"></i>Allow students to download this material
                            </label>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tenant.teacher.classroom.materials.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Supported Formats -->
            <div class="card border-0 shadow-sm bg-light mb-4">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Supported Formats</h6>
                    
                    <div class="mb-3">
                        <strong class="small">📄 Documents:</strong>
                        <p class="small mb-0 text-muted">PDF, Word (.doc, .docx), Excel (.xls, .xlsx), PowerPoint (.ppt, .pptx)</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong class="small">🎥 Videos:</strong>
                        <p class="small mb-0 text-muted">MP4, AVI, MOV, WMV (or paste YouTube link)</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong class="small">🖼️ Images:</strong>
                        <p class="small mb-0 text-muted">JPG, JPEG, PNG, GIF, SVG</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong class="small">🎵 Audio:</strong>
                        <p class="small mb-0 text-muted">MP3, WAV, OGG</p>
                    </div>
                    
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Max file size: 50 MB</strong>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Use clear, descriptive titles</li>
                        <li class="mb-2">Add descriptions to help students understand the content</li>
                        <li class="mb-2">For large videos, use YouTube links instead of uploading</li>
                        <li class="mb-2">Organize materials by topic or chapter</li>
                        <li class="mb-2">Allow downloads for reference materials</li>
                        <li class="mb-0">Disable downloads for copyrighted content</li>
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
    const fileHelp = document.getElementById('file-help');
    const urlHelp = document.getElementById('url-help');
    const filePreview = document.getElementById('file-preview');
    const downloadCheckbox = document.getElementById('is_downloadable');
    
    // Type change handler
    typeSelect.addEventListener('change', function() {
        const type = this.value;
        
        // Reset
        fileSection.classList.remove('d-none');
        urlSection.classList.add('d-none');
        fileInput.removeAttribute('required');
        document.getElementById('external_url').removeAttribute('required');
        
        if (type === 'youtube' || type === 'link') {
            // Show URL field, hide file upload
            fileSection.classList.add('d-none');
            urlSection.classList.remove('d-none');
            document.getElementById('external_url').setAttribute('required', 'required');
            
            if (type === 'youtube') {
                urlHelp.textContent = 'Paste the YouTube video URL (e.g., https://www.youtube.com/watch?v=...)';
                downloadCheckbox.checked = false;
                downloadCheckbox.disabled = true;
            } else {
                urlHelp.textContent = 'Enter the full URL of the external resource';
                downloadCheckbox.disabled = false;
            }
        } else {
            // Show file upload, hide URL
            fileInput.setAttribute('required', 'required');
            downloadCheckbox.disabled = false;
            
            // Update accepted file types
            switch(type) {
                case 'document':
                    fileInput.setAttribute('accept', '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx');
                    fileHelp.textContent = 'Accepted: PDF, Word, Excel, PowerPoint';
                    break;
                case 'video':
                    fileInput.setAttribute('accept', '.mp4,.avi,.mov,.wmv');
                    fileHelp.textContent = 'Accepted: MP4, AVI, MOV, WMV (Max 50MB)';
                    break;
                case 'image':
                    fileInput.setAttribute('accept', '.jpg,.jpeg,.png,.gif,.svg');
                    fileHelp.textContent = 'Accepted: JPG, PNG, GIF, SVG';
                    break;
                case 'audio':
                    fileInput.setAttribute('accept', '.mp3,.wav,.ogg');
                    fileHelp.textContent = 'Accepted: MP3, WAV, OGG';
                    break;
                default:
                    fileInput.removeAttribute('accept');
                    fileHelp.textContent = 'Select material type first';
            }
        }
    });
    
    // File input change handler
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = `${fileSizeMB} MB`;
            filePreview.classList.remove('d-none');
            
            // Check file size
            if (fileSizeMB > 50) {
                alert('File size exceeds 50MB limit. Please choose a smaller file or use a link instead.');
                this.value = '';
                filePreview.classList.add('d-none');
            }
        } else {
            filePreview.classList.add('d-none');
        }
    });
    
    // Form submission validation
    document.getElementById('materialForm').addEventListener('submit', function(e) {
        const type = typeSelect.value;
        const submitBtn = document.getElementById('submitBtn');
        
        if (!type) {
            e.preventDefault();
            alert('Please select a material type');
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    });
});
</script>
@endpush
@endsection

