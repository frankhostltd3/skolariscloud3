@extends('layouts.tenant.student')

@section('title', 'Edit Note')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('tenant.student.notes.index', ['view' => 'personal']) }}">{{ __('Notes') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('Edit Note') }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>{{ __('Edit Note') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tenant.student.notes.personal.update', $personalNote->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    {{ __('Title') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $personalNote->title) }}" required
                                    placeholder="{{ __('Enter note title...') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject_id" class="form-label">
                                    {{ __('Subject') }}
                                </label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                    name="subject_id">
                                    <option value="">{{ __('Select Subject (Optional)') }}</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ old('subject_id', $personalNote->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    {{ __('Content') }} <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15"
                                    required placeholder="{{ __('Write your notes here...') }}">{{ old('content', $personalNote->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span class="me-3">
                                        <i class="bi bi-file-text me-1"></i>
                                        {{ __('Word Count:') }} <strong
                                            id="wordCount">{{ $personalNote->word_count }}</strong>
                                    </span>
                                    <span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ __('Last Updated:') }} {{ $personalNote->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mb-3">
                                <label for="tags" class="form-label">
                                    {{ __('Tags') }}
                                </label>
                                <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                    id="tags" name="tags"
                                    value="{{ old('tags', is_array($personalNote->tags) ? implode(', ', $personalNote->tags) : '') }}"
                                    placeholder="{{ __('exam, important, chapter1') }}">
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ __('Separate tags with commas') }}
                                </div>
                            </div>

                            <!-- Color -->
                            <div class="mb-4">
                                <label class="form-label">{{ __('Note Color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="color" id="color1" value="#0d6efd"
                                        {{ old('color', $personalNote->color ?? '#0d6efd') == '#0d6efd' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="color1"
                                        style="width: 50px; height: 40px; background-color: #0d6efd;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color2" value="#198754"
                                        {{ old('color', $personalNote->color) == '#198754' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="color2"
                                        style="width: 50px; height: 40px; background-color: #198754;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color3" value="#ffc107"
                                        {{ old('color', $personalNote->color) == '#ffc107' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="color3"
                                        style="width: 50px; height: 40px; background-color: #ffc107;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color4" value="#dc3545"
                                        {{ old('color', $personalNote->color) == '#dc3545' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="color4"
                                        style="width: 50px; height: 40px; background-color: #dc3545;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color5" value="#6f42c1"
                                        {{ old('color', $personalNote->color) == '#6f42c1' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="color5"
                                        style="width: 50px; height: 40px; background-color: #6f42c1;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color6"
                                        value="#0dcaf0"
                                        {{ old('color', $personalNote->color) == '#0dcaf0' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="color6"
                                        style="width: 50px; height: 40px; background-color: #0dcaf0;"></label>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>{{ __('Update Note') }}
                                </button>
                                <a href="{{ route('tenant.student.notes.index', ['view' => 'personal']) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                                </a>
                                <form action="{{ route('tenant.student.notes.personal.destroy', $personalNote->id) }}"
                                    method="POST" class="ms-auto"
                                    onsubmit="return confirm('Are you sure you want to delete this note?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-2"></i>{{ __('Delete Note') }}
                                    </button>
                                </form>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Note Info -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <div class="row small text-muted">
                            <div class="col-md-6">
                                <i class="bi bi-calendar-plus me-1"></i>
                                <strong>{{ __('Created:') }}</strong>
                                {{ $personalNote->created_at->format('M d, Y H:i') }}
                            </div>
                            <div class="col-md-6">
                                <i class="bi bi-calendar-check me-1"></i>
                                <strong>{{ __('Updated:') }}</strong>
                                {{ $personalNote->updated_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTextarea = document.getElementById('content');
            const wordCountDisplay = document.getElementById('wordCount');

            function updateWordCount() {
                const text = contentTextarea.value.trim();
                const words = text ? text.split(/\s+/).length : 0;
                wordCountDisplay.textContent = words;
            }

            contentTextarea.addEventListener('input', updateWordCount);
        });
    </script>
@endsection
