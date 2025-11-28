@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('Edit Enrollment') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a
                        href="{{ route('tenant.academics.enrollments.index') }}">{{ __('Enrollments') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('tenant.academics.enrollments.update', $enrollment) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Student (Read-only) --}}
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Student') }}</label>
                        <input type="text" class="form-control" value="{{ $enrollment->student->name }}" readonly>
                        <small class="text-muted">{{ $enrollment->student->email }}</small>
                    </div>

                    {{-- Academic Year --}}
                    <div class="col-md-6">
                        <label for="academic_year_id" class="form-label">{{ __('Academic Year') }} <span
                                class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academic_year_id"
                            class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ old('academic_year_id', $enrollment->academic_year_id) == $year->id ? 'selected' : '' }}>
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
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ old('class_id', $enrollment->class_id) == $class->id ? 'selected' : '' }}>
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
                            <option value="">{{ __('None') }}</option>
                            @foreach ($streams as $stream)
                                <option value="{{ $stream->id }}"
                                    {{ old('class_stream_id', $enrollment->student->class_stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                    {{ $stream->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_stream_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Enrollment Date --}}
                    <div class="col-md-6">
                        <label for="enrollment_date" class="form-label">{{ __('Enrollment Date') }} <span
                                class="text-danger">*</span></label>
                        <input type="date" name="enrollment_date" id="enrollment_date"
                            class="form-control @error('enrollment_date') is-invalid @enderror"
                            value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}" required>
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
                            <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>
                                {{ __('Active') }}</option>
                            <option value="dropped"
                                {{ old('status', $enrollment->status) == 'dropped' ? 'selected' : '' }}>
                                {{ __('Dropped') }}</option>
                            <option value="transferred"
                                {{ old('status', $enrollment->status) == 'transferred' ? 'selected' : '' }}>
                                {{ __('Transferred') }}</option>
                            <option value="completed"
                                {{ old('status', $enrollment->status) == 'completed' ? 'selected' : '' }}>
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
                            value="{{ old('fees_total', $enrollment->fees_total) }}" min="0" step="0.01">
                        @error('fees_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Fees Paid --}}
                    <div class="col-md-6">
                        <label for="fees_paid" class="form-label">{{ __('Fees Paid') }}</label>
                        <input type="number" name="fees_paid" id="fees_paid"
                            class="form-control @error('fees_paid') is-invalid @enderror"
                            value="{{ old('fees_paid', $enrollment->fees_paid) }}" min="0" step="0.01">
                        @error('fees_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="col-12">
                        <label for="notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $enrollment->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>{{ __('Update Enrollment') }}
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

            streamSelect.innerHTML = '<option value="">{{ __('Loading...') }}</option>';

            fetch(`{{ route('tenant.academics.enrollments.streams') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(streams => {
                    streamSelect.innerHTML = '<option value="">{{ __('None') }}</option>';
                    streams.forEach(stream => {
                        const option = document.createElement('option');
                        option.value = stream.id;
                        option.textContent = stream.name;
                        streamSelect.appendChild(option);
                    });
                });
        });
    </script>
@endpush
