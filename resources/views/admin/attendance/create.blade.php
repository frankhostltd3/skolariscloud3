@extends('tenant.layouts.app')

@section('title', 'Create Attendance Session')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-check me-2"></i>Create Attendance Session
                        </h5>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back to Attendance
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.attendance.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select name="class_id" id="class_id"
                                        class="form-select @error('class_id') is-invalid @enderror" required>
                                        <option value="">-- Select Class --</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="class_stream_id" class="form-label">Class Stream (Optional)</label>
                                    <select name="class_stream_id" id="class_stream_id"
                                        class="form-select @error('class_stream_id') is-invalid @enderror">
                                        <option value="">-- All Streams --</option>
                                        {{-- Streams will be loaded via AJAX based on selected class --}}
                                    </select>
                                    @error('class_stream_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty to mark attendance for all
                                        streams</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="attendance_date" class="form-label">Attendance Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date" id="attendance_date"
                                        class="form-control @error('attendance_date') is-invalid @enderror"
                                        value="{{ old('attendance_date', date('Y-m-d')) }}" required>
                                    @error('attendance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="time_in" class="form-label">Time In (Optional)</label>
                                    <input type="time" name="time_in" id="time_in"
                                        class="form-control @error('time_in') is-invalid @enderror"
                                        value="{{ old('time_in', date('H:i')) }}">
                                    @error('time_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty to use current time</small>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="Any additional notes for this attendance session...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Note:</strong> After creating the session, you'll be able to mark individual student
                                attendance (Present, Absent, Late, Excused, etc.)
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Create Session
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Load class streams when class is selected
            document.getElementById('class_id').addEventListener('change', function() {
                const classId = this.value;
                const streamSelect = document.getElementById('class_stream_id');

                // Clear existing options
                streamSelect.innerHTML = '<option value="">-- All Streams --</option>';

                if (!classId) {
                    return;
                }

                // Fetch streams for selected class
                fetch(`/api/classes/${classId}/streams`)
                    .then(response => response.json())
                    .then(streams => {
                        streams.forEach(stream => {
                            const option = document.createElement('option');
                            option.value = stream.id;
                            option.textContent = stream.name;
                            streamSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading streams:', error);
                    });
            });
        </script>
    @endpush
@endsection
