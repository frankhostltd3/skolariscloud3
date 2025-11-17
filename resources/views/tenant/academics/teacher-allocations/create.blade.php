@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Allocate Teacher') }}</h1>
            <p class="text-muted small mt-1">{{ __('Assign a teacher to a subject in a class') }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.teacher-allocations.index') }}"><i
                class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
    </div>
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button"
                class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.teacher-allocations.store') }}" method="POST">@csrf
                <div class="row g-3">
                    <div class="col-md-6"><label for="teacher_id" class="form-label">{{ __('Teacher') }} <span
                                class="text-danger">*</span></label><select
                            class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id"
                            required>
                            @if (!request('teacher_id'))
                                <option value="">{{ __('-- Select Teacher --') }}</option>
                                @endif @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}"
                                        {{ old('teacher_id', request('teacher_id')) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }} ({{ $teacher->email }})</option>
                                @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6"><label for="class_id" class="form-label">{{ __('Class') }} <span
                                class="text-danger">*</span></label><select
                            class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id"
                            required onchange="loadClassSubjects()">
                            @if (!request('class_id'))
                                <option value="">{{ __('-- Select Class --') }}</option>
                                @endif @php $currentLevel = null; @endphp @foreach ($classes as $class)
                                    @if ($currentLevel !== optional($class->educationLevel)->id)
                                        @php $currentLevel = optional($class->educationLevel)->id; @endphp @if (!$loop->first)
                                            <option disabled>──────────</option>
                                        @endif
                                        <option disabled class="fw-bold">
                                            {{ optional($class->educationLevel)->name ?? __('No Level') }}</option>
                                    @endif
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id', request('class_id')) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}</option>
                                @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6"><label for="subject_id" class="form-label">{{ __('Subject') }} <span
                                class="text-danger">*</span></label><select
                            class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id"
                            required>
                            @if (!request('subject_id'))
                                <option value="">{{ __('-- Select Subject --') }}</option>
                                @endif @php $currentLevel = null; @endphp @foreach ($subjects as $subject)
                                    @if ($currentLevel !== optional($subject->educationLevel)->id)
                                        @php $currentLevel = optional($subject->educationLevel)->id; @endphp @if (!$loop->first)
                                            <option disabled>──────────</option>
                                        @endif
                                        <option disabled class="fw-bold">
                                            {{ optional($subject->educationLevel)->name ?? __('All Levels') }}</option>
                                    @endif
                                    <option value="{{ $subject->id }}"
                                        {{ old('subject_id', request('subject_id')) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} @if ($subject->code)
                                            ({{ $subject->code }})
                                        @endif
                                    </option>
                                @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            {{ __('Only subjects assigned to the selected class will be valid') }}</small>
                    </div>
                    <div class="col-md-6"><label for="is_compulsory"
                            class="form-label">{{ __('Subject Status') }}</label><select
                            class="form-select @error('is_compulsory') is-invalid @enderror" id="is_compulsory"
                            name="is_compulsory">
                            <option value="1" {{ old('is_compulsory', 1) ? 'selected' : '' }}>{{ __('Compulsory') }}
                            </option>
                            <option value="0" {{ old('is_compulsory', 1) ? '' : 'selected' }}>{{ __('Optional') }}
                            </option>
                        </select>
                        @error('is_compulsory')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info"><i
                                class="bi bi-info-circle me-2"></i>{{ __('If the subject is already assigned to the class, this will update the teacher. Otherwise, it will create a new class-subject-teacher allocation.') }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i
                            class="bi bi-check-circle me-1"></i>{{ __('Allocate Teacher') }}</button><a
                        href="{{ route('tenant.academics.teacher-allocations.index') }}"
                        class="btn btn-outline-secondary">{{ __('Cancel') }}</a></div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function loadClassSubjects() {
            const classId = document.getElementById('class_id').value;
            if (!classId) return;

            // This could be enhanced with AJAX to filter subjects dynamically
            console.log('Selected class:', classId);
        }
    </script>
@endpush
