@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-person-badge"></i> Record Staff Attendance</h4>
                    <div>
                        <a href="{{ route('admin.staff-attendance.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <h5 class="card-title mb-0">Attendance Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.staff-attendance.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                                    <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select Staff --</option>
                                        @foreach ($staff as $member)
                                            <option value="{{ $member->id }}"
                                                {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->name }}
                                                @if ($member->roles->isNotEmpty())
                                                    ({{ $member->roles->pluck('name')->implode(', ') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('staff_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Attendance Date <span class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date"
                                        class="form-control @error('attendance_date') is-invalid @enderror"
                                        value="{{ old('attendance_date', today()->format('Y-m-d')) }}" required>
                                    @error('attendance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required id="statusSelect">
                                        <option value="">-- Select Status --</option>
                                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present
                                        </option>
                                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent
                                        </option>
                                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late
                                        </option>
                                        <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Half
                                            Day</option>
                                        <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>On
                                            Leave</option>
                                        <option value="sick_leave" {{ old('status') == 'sick_leave' ? 'selected' : '' }}>
                                            Sick Leave</option>
                                        <option value="official_duty"
                                            {{ old('status') == 'official_duty' ? 'selected' : '' }}>Official Duty</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3" id="checkInGroup">
                                    <label class="form-label">Check In Time</label>
                                    <input type="time" name="check_in"
                                        class="form-control @error('check_in') is-invalid @enderror"
                                        value="{{ old('check_in') }}">
                                    @error('check_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3" id="checkOutGroup">
                                    <label class="form-label">Check Out Time</label>
                                    <input type="time" name="check_out"
                                        class="form-control @error('check_out') is-invalid @enderror"
                                        value="{{ old('check_out') }}">
                                    @error('check_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3" id="leaveReasonGroup" style="display: none;">
                                <label class="form-label">Leave Reason</label>
                                <textarea name="leave_reason" rows="3" class="form-control @error('leave_reason') is-invalid @enderror"
                                    placeholder="Enter reason for leave">{{ old('leave_reason') }}</textarea>
                                @error('leave_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Any additional notes">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.staff-attendance.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Attendance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status Guide</h5>
                    </div>
                    <div class="card-body">
                        <dl class="mb-0">
                            <dt class="mb-1"><span class="badge bg-success">Present</span></dt>
                            <dd class="mb-3 text-muted small">Staff member present for full day</dd>

                            <dt class="mb-1"><span class="badge bg-danger">Absent</span></dt>
                            <dd class="mb-3 text-muted small">Staff member did not attend</dd>

                            <dt class="mb-1"><span class="badge bg-warning">Late</span></dt>
                            <dd class="mb-3 text-muted small">Arrived after scheduled time</dd>

                            <dt class="mb-1"><span class="badge bg-info">Half Day</span></dt>
                            <dd class="mb-3 text-muted small">Present for half day only</dd>

                            <dt class="mb-1"><span class="badge bg-secondary">On Leave</span></dt>
                            <dd class="mb-3 text-muted small">Approved leave (vacation, personal)</dd>

                            <dt class="mb-1"><span class="badge bg-secondary">Sick Leave</span></dt>
                            <dd class="mb-3 text-muted small">Medical/health related absence</dd>

                            <dt class="mb-1"><span class="badge bg-primary">Official Duty</span></dt>
                            <dd class="mb-0 text-muted small">Representing school elsewhere</dd>
                        </dl>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 small">
                            <li class="mb-2">Check-in/out times are optional for absent staff</li>
                            <li class="mb-2">Leave reason required for leave types</li>
                            <li class="mb-2">Official duty should include details in notes</li>
                            <li class="mb-0">Records can be edited later if needed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Show/hide fields based on status
            document.getElementById('statusSelect').addEventListener('change', function() {
                const status = this.value;
                const checkInGroup = document.getElementById('checkInGroup');
                const checkOutGroup = document.getElementById('checkOutGroup');
                const leaveReasonGroup = document.getElementById('leaveReasonGroup');

                // Show/hide leave reason field
                if (['on_leave', 'sick_leave'].includes(status)) {
                    leaveReasonGroup.style.display = 'block';
                    leaveReasonGroup.querySelector('textarea').required = true;
                } else {
                    leaveReasonGroup.style.display = 'none';
                    leaveReasonGroup.querySelector('textarea').required = false;
                }

                // Show/hide check in/out for absent
                if (status === 'absent') {
                    checkInGroup.style.display = 'none';
                    checkOutGroup.style.display = 'none';
                } else {
                    checkInGroup.style.display = 'block';
                    checkOutGroup.style.display = 'block';
                }
            });

            // Trigger on page load if status already selected
            if (document.getElementById('statusSelect').value) {
                document.getElementById('statusSelect').dispatchEvent(new Event('change'));
            }
        </script>
    @endpush
@endsection
