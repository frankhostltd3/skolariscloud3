@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('New Enrollment') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a
                        href="{{ route('tenant.academics.enrollments.index') }}">{{ __('Enrollments') }}</a></li>
                <li class="breadcrumb-item active">{{ __('New') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('tenant.academics.enrollments.store') }}">
                @csrf

                <div class="row g-3">
                    {{-- Student --}}
                    <div class="col-md-6">
                        <label for="student_id" class="form-label">{{ __('Student') }} <span
                                class="text-danger">*</span></label>
                        <select name="student_id" id="student_id"
                            class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">{{ __('Select Student') }}</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Academic Year --}}
                    <div class="col-md-6">
                        <label for="academic_year_id" class="form-label">{{ __('Academic Year') }} <span
                                class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academic_year_id"
                            class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">{{ __('Select Academic Year') }}</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ old('academic_year_id', $academicYears->first()->id ?? '') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} ({{ $year->start_date->format('Y') }} -
                                    {{ $year->end_date->format('Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Class --}}
                    <div class="col-md-6">
                        <label for="class_id" class="form-label">{{ __('Class') }} <span
                                class="text-danger">*</span></label>
                        <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror"
                            required>
                            <option value="">{{ __('Select Class') }}</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Stream --}}
                    <div class="col-md-6">
                        <label for="class_stream_id" class="form-label">{{ __('Stream') }}</label>
                        <select name="class_stream_id" id="class_stream_id"
                            class="form-select @error('class_stream_id') is-invalid @enderror">
                            <option value="">{{ __('Select Stream (Optional)') }}</option>
                        </select>
                        @error('class_stream_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('Select a class first to see available streams') }}</small>
                    </div>

                    {{-- Enrollment Date --}}
                    <div class="col-md-6">
                        <label for="enrollment_date" class="form-label">{{ __('Enrollment Date') }} <span
                                class="text-danger">*</span></label>
                        <input type="date" name="enrollment_date" id="enrollment_date"
                            class="form-control @error('enrollment_date') is-invalid @enderror"
                            value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" required>
                        @error('enrollment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label for="status" class="form-label">{{ __('Status') }} <span
                                class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                            required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                {{ __('Active') }}</option>
                            <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}
                            </option>
                            <option value="transferred" {{ old('status') == 'transferred' ? 'selected' : '' }}>
                                {{ __('Transferred') }}</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                {{ __('Completed') }}</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Fees Total --}}
                    <div class="col-md-6">
                        <label for="fees_total" class="form-label">{{ __('Total Fees') }}</label>
                        <input type="number" name="fees_total" id="fees_total"
                            class="form-control @error('fees_total') is-invalid @enderror"
                            value="{{ old('fees_total', 0) }}" min="0" step="0.01">
                        @error('fees_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="col-12">
                        <label for="notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                            placeholder="{{ __('Additional notes about this enrollment...') }}">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>{{ __('Create Enrollment') }}
                    </button>
                    <a href="{{ route('tenant.academics.enrollments.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('class_id').addEventListener('change', function() {
            const classId = this.value;
            const streamSelect = document.getElementById('class_stream_id');

            // Clear existing options
            streamSelect.innerHTML = '<option value="">{{ __('Loading...') }}</option>';

            if (!classId) {
                streamSelect.innerHTML = '<option value="">{{ __('Select Stream (Optional)') }}</option>';
                return;
            }

            // Fetch streams for the selected class
            fetch(`{{ route('tenant.academics.enrollments.streams') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(streams => {
                    streamSelect.innerHTML = '<option value="">{{ __('Select Stream (Optional)') }}</option>';
                    streams.forEach(stream => {
                        const option = document.createElement('option');
                        option.value = stream.id;
                        option.textContent =
                            `${stream.name} (${stream.active_students_count}/${stream.capacity || '∞'})`;
                        streamSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching streams:', error);
                    streamSelect.innerHTML = '<option value="">{{ __('Error loading streams') }}</option>';
                });
        });
    </script>
@endpush
