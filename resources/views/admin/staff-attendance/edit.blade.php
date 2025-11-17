@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Staff Attendance</h4>
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
                        <h5 class="card-title mb-0">Update Attendance Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.staff-attendance.update', $attendance->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Staff Member</label>
                                    <input type="text" class="form-control" value="{{ $attendance->staff->name }}"
                                        disabled>
                                    <small class="text-muted">Cannot change staff member after creation</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Attendance Date</label>
                                    <input type="text" class="form-control"
                                        value="{{ $attendance->attendance_date->format('F d, Y') }}" disabled>
                                    <small class="text-muted">Cannot change date after creation</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required id="statusSelect">
                                        <option value="">-- Select Status --</option>
                                        <option value="present"
                                            {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present
                                        </option>
                                        <option value="absent"
                                            {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent
                                        </option>
                                        <option value="late"
                                            {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Late
                                        </option>
                                        <option value="half_day"
                                            {{ old('status', $attendance->status) == 'half_day' ? 'selected' : '' }}>Half
                                            Day</option>
                                        <option value="on_leave"
                                            {{ old('status', $attendance->status) == 'on_leave' ? 'selected' : '' }}>On
                                            Leave</option>
                                        <option value="sick_leave"
                                            {{ old('status', $attendance->status) == 'sick_leave' ? 'selected' : '' }}>Sick
                                            Leave</option>
                                        <option value="official_duty"
                                            {{ old('status', $attendance->status) == 'official_duty' ? 'selected' : '' }}>
                                            Official Duty</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3" id="checkInGroup">
                                    <label class="form-label">Check In Time</label>
                                    <input type="time" name="check_in"
                                        class="form-control @error('check_in') is-invalid @enderror"
                                        value="{{ old('check_in', $attendance->check_in ? $attendance->check_in->format('H:i') : '') }}">
                                    @error('check_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3" id="checkOutGroup">
                                    <label class="form-label">Check Out Time</label>
                                    <input type="time" name="check_out"
                                        class="form-control @error('check_out') is-invalid @enderror"
                                        value="{{ old('check_out', $attendance->check_out ? $attendance->check_out->format('H:i') : '') }}">
                                    @error('check_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3" id="leaveReasonGroup" style="display: none;">
                                <label class="form-label">Leave Reason</label>
                                <textarea name="leave_reason" rows="3" class="form-control @error('leave_reason') is-invalid @enderror"
                                    placeholder="Enter reason for leave">{{ old('leave_reason', $attendance->leave_reason) }}</textarea>
                                @error('leave_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Any additional notes">{{ old('notes', $attendance->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    @if ($attendance->approval_status === 'pending')
                                        <button type="button" class="btn btn-danger"
                                            onclick="if(confirm('Delete this attendance record?')) document.getElementById('deleteForm').submit();">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.staff-attendance.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Attendance
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($attendance->approval_status === 'pending')
                            <form id="deleteForm" action="{{ route('admin.staff-attendance.destroy', $attendance->id) }}"
                                method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Record Information</h5>
                    </div>
                    <div class="card-body">
                        <dl class="mb-0">
                            <dt class="mb-1">Created</dt>
                            <dd class="mb-3">{{ $attendance->created_at->format('M d, Y h:i A') }}</dd>

                            <dt class="mb-1">Last Updated</dt>
                            <dd class="mb-3">{{ $attendance->updated_at->format('M d, Y h:i A') }}</dd>

                            <dt class="mb-1">Approval Status</dt>
                            <dd class="mb-3">
                                @if ($attendance->approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($attendance->approval_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </dd>

                            @if ($attendance->approved_by)
                                <dt class="mb-1">Approved By</dt>
                                <dd class="mb-3">{{ $attendance->approver->name }}</dd>

                                <dt class="mb-1">Approved At</dt>
                                <dd class="mb-0">{{ $attendance->approved_at->format('M d, Y h:i A') }}</dd>
                            @endif

                            @if ($attendance->hours_worked)
                                <dt class="mb-1">Hours Worked</dt>
                                <dd class="mb-0">{{ number_format($attendance->hours_worked, 2) }} hours</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="card mt-3">
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

            // Trigger on page load
            document.getElementById('statusSelect').dispatchEvent(new Event('change'));
        </script>
    @endpush
@endsection
