@extends('tenant.layouts.app')

@section('title', __('Student ID Generation'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Student ID Generation') }}</h4>
                </div>
                <div class="card-body">
                    <form id="idGenerationForm" method="POST" action="{{ route('tenant.modules.academic.student-ids.generate') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">{{ __('Select Student') }}</label>
                                    <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                                        <option value="">{{ __('Choose a student...') }}</option>
                                        @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} - {{ $student->admission_no }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template_id" class="form-label">{{ __('Select Template') }}</label>
                                    <select class="form-select @error('template_id') is-invalid @enderror" id="template_id" name="template_id" required>
                                        <option value="">{{ __('Choose a template...') }}</option>
                                        @foreach($templates as $template)
                                        <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->template_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('template_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2" id="generateBtn" disabled>
                                    <i class="fas fa-id-card"></i> {{ __('Generate ID') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="previewBtn" onclick="previewId()" disabled>
                                    <i class="fas fa-eye"></i> {{ __('Preview') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('ID Preview') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="previewContent">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-success" onclick="downloadSvg()">
                        <i class="fas fa-download"></i> {{ __('Download SVG') }}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="downloadPng()">
                        <i class="fas fa-download"></i> {{ __('Download PNG') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enable/disable buttons based on selection
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const templateSelect = document.getElementById('template_id');
    const generateBtn = document.getElementById('generateBtn');
    const previewBtn = document.getElementById('previewBtn');

    function updateButtonStates() {
        const hasStudent = studentSelect.value !== '';
        const hasTemplate = templateSelect.value !== '';
        const bothSelected = hasStudent && hasTemplate;

        generateBtn.disabled = !bothSelected;
        previewBtn.disabled = !bothSelected;
    }

    studentSelect.addEventListener('change', updateButtonStates);
    templateSelect.addEventListener('change', updateButtonStates);

    // Initial check
    updateButtonStates();
});

function previewId() {
    const form = document.getElementById('idGenerationForm');
    const formData = new FormData(form);

    if (!formData.get('student_id') || !formData.get('template_id')) {
        alert('{{ __("Please select both a student and a template.") }}');
        return;
    }

    // Show loading state
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">{{ __("Loading...") }}</span></div>';
    
    // Show modal immediately
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();

    fetch('{{ route("tenant.modules.academic.student-ids.preview") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        previewContent.innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        previewContent.innerHTML = '<div class="alert alert-danger">{{ __("Error loading preview. Please try again.") }}</div>';
    });
}

function downloadSvg() {
    const form = document.getElementById('idGenerationForm');
    const formData = new FormData(form);

    const url = '{{ route("tenant.modules.academic.student-ids.download.svg") }}' +
                '?student_id=' + formData.get('student_id') +
                '&template_id=' + formData.get('template_id');

    window.open(url, '_blank');
}

function downloadPng() {
    const form = document.getElementById('idGenerationForm');
    const formData = new FormData(form);

    const url = '{{ route("tenant.modules.academic.student-ids.download.png") }}' +
                '?student_id=' + formData.get('student_id') +
                '&template_id=' + formData.get('template_id');

    window.open(url, '_blank');
}
</script>
@endsection
