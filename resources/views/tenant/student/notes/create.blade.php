@extends('layouts.tenant.student')

@section('title', 'Create New Note')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('tenant.student.notes.index', ['view' => 'personal']) }}">{{ __('Notes') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('Create Note') }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-journal-plus me-2"></i>{{ __('Create New Note') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tenant.student.notes.personal.store') }}" method="POST">
                            @csrf

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    {{ __('Title') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" required
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
                                            {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                    required placeholder="{{ __('Write your notes here...') }}">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ __('Use formatting like **bold**, *italic*, - lists') }}
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mb-3">
                                <label for="tags" class="form-label">
                                    {{ __('Tags') }}
                                </label>
                                <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                    id="tags" name="tags" value="{{ old('tags') }}"
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
                                        {{ old('color', '#0d6efd') == '#0d6efd' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="color1"
                                        style="width: 50px; height: 40px; background-color: #0d6efd;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color2" value="#198754">
                                    <label class="btn btn-outline-success" for="color2"
                                        style="width: 50px; height: 40px; background-color: #198754;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color3" value="#ffc107">
                                    <label class="btn btn-outline-warning" for="color3"
                                        style="width: 50px; height: 40px; background-color: #ffc107;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color4" value="#dc3545">
                                    <label class="btn btn-outline-danger" for="color4"
                                        style="width: 50px; height: 40px; background-color: #dc3545;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color5" value="#6f42c1">
                                    <label class="btn btn-outline-secondary" for="color5"
                                        style="width: 50px; height: 40px; background-color: #6f42c1;"></label>

                                    <input type="radio" class="btn-check" name="color" id="color6"
                                        value="#0dcaf0">
                                    <label class="btn btn-outline-info" for="color6"
                                        style="width: 50px; height: 40px; background-color: #0dcaf0;"></label>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>{{ __('Save Note') }}
                                </button>
                                <a href="{{ route('tenant.student.notes.index', ['view' => 'personal']) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-info bg-opacity-10 border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-lightbulb me-2"></i>{{ __('Note Taking Tips') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>{{ __('Use clear and descriptive titles') }}</li>
                            <li>{{ __('Organize notes by subject for easy reference') }}</li>
                            <li>{{ __('Add tags to categorize related topics') }}</li>
                            <li>{{ __('Use colors to prioritize important notes') }}</li>
                            <li>{{ __('Review and update your notes regularly') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
