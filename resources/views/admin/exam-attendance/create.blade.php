@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-clipboard-check"></i> Create Exam Attendance Session</h4>
                    <div>
                        <a href="{{ route('admin.exam-attendance.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Exam Session Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.exam-attendance.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Class <span class="text-danger">*</span></label>
                                    <select name="class_id" class="form-select @error('class_id') is-invalid @enderror"
                                        required id="classSelect">
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
                                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select Subject --</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Exam Date <span class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date"
                                        class="form-control @error('attendance_date') is-invalid @enderror"
                                        value="{{ old('attendance_date', date('Y-m-d')) }}" required>
                                    @error('attendance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="time_in"
                                        class="form-control @error('time_in') is-invalid @enderror"
                                        value="{{ old('time_in') }}" required>
                                    @error('time_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">When exam begins</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Time</label>
                                    <input type="time" name="time_out"
                                        class="form-control @error('time_out') is-invalid @enderror"
                                        value="{{ old('time_out') }}">
                                    @error('time_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">When exam ends (optional)</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes / Instructions</label>
                                <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Enter any exam-specific notes, instructions, or special considerations">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max 1000 characters. Example: Seating arrangement, special
                                    accommodations, exam rules</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> <strong>Note:</strong> After creating the exam session,
                                you'll be able to mark individual student attendance on the next page.
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.exam-attendance.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Create Session
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Exam Attendance Guide</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2"><i class="bi bi-1-circle-fill text-primary"></i> Create Session</h6>
                        <p class="text-muted small mb-3">Set up the exam details (class, subject, date, time)</p>

                        <h6 class="mb-2"><i class="bi bi-2-circle-fill text-primary"></i> Mark Attendance</h6>
                        <p class="text-muted small mb-3">Record which students are present for the exam</p>

                        <h6 class="mb-2"><i class="bi bi-3-circle-fill text-primary"></i> Track Seating</h6>
                        <p class="text-muted small mb-3">Optionally record seat numbers for each student</p>

                        <h6 class="mb-2"><i class="bi bi-4-circle-fill text-primary"></i> Monitor Progress</h6>
                        <p class="text-muted small mb-0">Track exam start/end times and special notes</p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 ps-3">
                            <li class="mb-2 small">Create the session before the exam starts</li>
                            <li class="mb-2 small">End time can be updated later if exam runs longer</li>
                            <li class="mb-2 small">Notes field is useful for recording special circumstances</li>
                            <li class="mb-2 small">You can mark attendance as students arrive</li>
                            <li class="mb-0 small">Absent students will be automatically identified</li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Common Use Cases</h5>
                    </div>
                    <div class="card-body">
                        <dl class="mb-0">
                            <dt class="small mb-1">Mid-term Exams</dt>
                            <dd class="text-muted small mb-3">Track attendance for periodic assessments</dd>

                            <dt class="small mb-1">Final Exams</dt>
                            <dd class="text-muted small mb-3">Record presence for end-of-term exams</dd>

                            <dt class="small mb-1">National Exams</dt>
                            <dd class="text-muted small mb-3">Document attendance for official examinations</dd>

                            <dt class="small mb-1">Make-up Exams</dt>
                            <dd class="text-muted small mb-0">Track students taking deferred exams</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
