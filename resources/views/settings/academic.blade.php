@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">
                <span class="bi bi-mortarboard me-2"></span>
                {{ __('Academic Settings') }}
            </h1>
            <p class="text-muted mb-0">{{ __('Configure academic year, grading system, and curriculum settings') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Academic Year Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <span class="bi bi-calendar-event me-2"></span>
                    {{ __('Academic Year Settings') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.academic.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="academic_year">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_academic_year" class="form-label">Current Academic Year</label>
                                <input type="text"
                                    class="form-control @error('current_academic_year') is-invalid @enderror"
                                    id="current_academic_year" name="current_academic_year"
                                    value="{{ old('current_academic_year', $settings['current_academic_year'] ?? '2024-2025') }}"
                                    placeholder="e.g., 2024-2025">
                                @error('current_academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="academic_year_start" class="form-label">Academic Year Start Date</label>
                                <input type="date"
                                    class="form-control @error('academic_year_start') is-invalid @enderror"
                                    id="academic_year_start" name="academic_year_start"
                                    value="{{ old('academic_year_start', $settings['academic_year_start'] ?? date('Y-09-01')) }}">
                                @error('academic_year_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="academic_year_end" class="form-label">Academic Year End Date</label>
                                <input type="date" class="form-control @error('academic_year_end') is-invalid @enderror"
                                    id="academic_year_end" name="academic_year_end"
                                    value="{{ old('academic_year_end', $settings['academic_year_end'] ?? date('Y-06-30', strtotime('+1 year'))) }}">
                                @error('academic_year_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="semester_system" class="form-label">Term System</label>
                                <select class="form-select @error('semester_system') is-invalid @enderror"
                                    id="semester_system" name="semester_system">
                                    <option value="semester"
                                        {{ ($settings['semester_system'] ?? 'semester') == 'semester' ? 'selected' : '' }}>
                                        Semester (2 terms)</option>
                                    <option value="trimester"
                                        {{ ($settings['semester_system'] ?? 'semester') == 'trimester' ? 'selected' : '' }}>
                                        Trimester (3 terms)</option>
                                    <option value="quarter"
                                        {{ ($settings['semester_system'] ?? 'semester') == 'quarter' ? 'selected' : '' }}>
                                        Quarter (4 terms)</option>
                                    <option value="annual"
                                        {{ ($settings['semester_system'] ?? 'semester') == 'annual' ? 'selected' : '' }}>
                                        Annual (1 term)</option>
                                </select>
                                @error('semester_system')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Academic Year Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grading System -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-star me-2 text-success"></span>
                        Grading System
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.academic.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="grading">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="grading_scale" class="form-label">Grading Scale</label>
                                <select class="form-select @error('grading_scale') is-invalid @enderror" id="grading_scale"
                                    name="grading_scale">
                                    <option value="percentage"
                                        {{ ($settings['grading_scale'] ?? 'percentage') == 'percentage' ? 'selected' : '' }}>
                                        Percentage (0-100%)</option>
                                    <option value="gpa_4"
                                        {{ ($settings['grading_scale'] ?? 'percentage') == 'gpa_4' ? 'selected' : '' }}>GPA
                                        4.0 Scale</option>
                                    <option value="gpa_5"
                                        {{ ($settings['grading_scale'] ?? 'percentage') == 'gpa_5' ? 'selected' : '' }}>GPA
                                        5.0 Scale</option>
                                    <option value="letter"
                                        {{ ($settings['grading_scale'] ?? 'percentage') == 'letter' ? 'selected' : '' }}>
                                        Letter Grades (A-F)</option>
                                </select>
                                @error('grading_scale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="passing_grade" class="form-label">Minimum Passing Grade</label>
                                <input type="number" class="form-control @error('passing_grade') is-invalid @enderror"
                                    id="passing_grade" name="passing_grade"
                                    value="{{ old('passing_grade', $settings['passing_grade'] ?? '60') }}" min="0"
                                    max="100">
                                @error('passing_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Grade Levels Configuration -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Grade Level Ranges</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Letter Grade</th>
                                                <th>Min %</th>
                                                <th>Max %</th>
                                                <th>GPA Points</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-success">A</span></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_a_min" value="{{ $settings['grade_a_min'] ?? '90' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_a_max"
                                                        value="{{ $settings['grade_a_max'] ?? '100' }}" min="0"
                                                        max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_a_gpa"
                                                        value="{{ $settings['grade_a_gpa'] ?? '4.0' }}" step="0.1"
                                                        min="0" max="5"></td>
                                                <td>Excellent</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-primary">B</span></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_b_min" value="{{ $settings['grade_b_min'] ?? '80' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_b_max" value="{{ $settings['grade_b_max'] ?? '89' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_b_gpa"
                                                        value="{{ $settings['grade_b_gpa'] ?? '3.0' }}" step="0.1"
                                                        min="0" max="5"></td>
                                                <td>Good</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-warning">C</span></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_c_min" value="{{ $settings['grade_c_min'] ?? '70' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_c_max" value="{{ $settings['grade_c_max'] ?? '79' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_c_gpa"
                                                        value="{{ $settings['grade_c_gpa'] ?? '2.0' }}" step="0.1"
                                                        min="0" max="5"></td>
                                                <td>Satisfactory</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-info">D</span></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_d_min" value="{{ $settings['grade_d_min'] ?? '60' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_d_max" value="{{ $settings['grade_d_max'] ?? '69' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_d_gpa"
                                                        value="{{ $settings['grade_d_gpa'] ?? '1.0' }}" step="0.1"
                                                        min="0" max="5"></td>
                                                <td>Needs Improvement</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-danger">F</span></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_f_min" value="{{ $settings['grade_f_min'] ?? '0' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_f_max" value="{{ $settings['grade_f_max'] ?? '59' }}"
                                                        min="0" max="100"></td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="grade_f_gpa"
                                                        value="{{ $settings['grade_f_gpa'] ?? '0.0' }}" step="0.1"
                                                        min="0" max="5"></td>
                                                <td>Failing</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Grading System') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Settings -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-person-check me-2 text-warning"></span>
                        Attendance Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.academic.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="attendance">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="attendance_marking" class="form-label">Attendance Marking</label>
                                <select class="form-select @error('attendance_marking') is-invalid @enderror"
                                    id="attendance_marking" name="attendance_marking">
                                    <option value="automatic"
                                        {{ ($settings['attendance_marking'] ?? 'automatic') == 'automatic' ? 'selected' : '' }}>
                                        Automatic</option>
                                    <option value="manual"
                                        {{ ($settings['attendance_marking'] ?? 'automatic') == 'manual' ? 'selected' : '' }}>
                                        Manual</option>
                                    <option value="biometric"
                                        {{ ($settings['attendance_marking'] ?? 'automatic') == 'biometric' ? 'selected' : '' }}>
                                        Biometric</option>
                                </select>
                                @error('attendance_marking')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="minimum_attendance" class="form-label">Minimum Attendance Required (%)</label>
                                <input type="number"
                                    class="form-control @error('minimum_attendance') is-invalid @enderror"
                                    id="minimum_attendance" name="minimum_attendance"
                                    value="{{ old('minimum_attendance', $settings['minimum_attendance'] ?? '75') }}"
                                    min="0" max="100">
                                @error('minimum_attendance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="late_arrival_grace" class="form-label">Late Arrival Grace Period
                                    (minutes)</label>
                                <input type="number"
                                    class="form-control @error('late_arrival_grace') is-invalid @enderror"
                                    id="late_arrival_grace" name="late_arrival_grace"
                                    value="{{ old('late_arrival_grace', $settings['late_arrival_grace'] ?? '15') }}"
                                    min="0" max="60">
                                @error('late_arrival_grace')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="attendance_notifications" class="form-label">Attendance Notifications</label>
                                <select class="form-select @error('attendance_notifications') is-invalid @enderror"
                                    id="attendance_notifications" name="attendance_notifications">
                                    <option value="enabled"
                                        {{ ($settings['attendance_notifications'] ?? 'enabled') == 'enabled' ? 'selected' : '' }}>
                                        Enabled</option>
                                    <option value="disabled"
                                        {{ ($settings['attendance_notifications'] ?? 'enabled') == 'disabled' ? 'selected' : '' }}>
                                        Disabled</option>
                                </select>
                                @error('attendance_notifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <span class="bi bi-floppy me-2"></span>{{ __('Save Attendance Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-info-circle me-2 text-info"></span>
                        Academic Settings Help
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Academic Year</h6>
                        <p class="mb-0">Set the current academic year and term structure that will be used throughout the
                            system.</p>
                    </div>

                    <div class="alert alert-success">
                        <h6>Grading System</h6>
                        <p class="mb-0">Configure how grades are calculated and displayed. This affects report cards and
                            transcripts.</p>
                    </div>

                    <div class="alert alert-warning">
                        <h6>Attendance</h6>
                        <p class="mb-0">Set attendance policies and requirements that will be enforced across all
                            classes.</p>
                    </div>
                </div>
            </div>

            <!-- Current Settings Summary -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-bar-chart me-2 text-primary"></span>
                        Current Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Academic Year</small>
                        <div class="fw-bold">{{ $settings['current_academic_year'] ?? '2024-2025' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Grading Scale</small>
                        <div class="fw-bold">
                            {{ ucfirst(str_replace('_', ' ', $settings['grading_scale'] ?? 'percentage')) }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Passing Grade</small>
                        <div class="fw-bold">{{ $settings['passing_grade'] ?? '60' }}%</div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Min. Attendance</small>
                        <div class="fw-bold">{{ $settings['minimum_attendance'] ?? '75' }}%</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <span class="bi bi-lightning me-2 text-primary"></span>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('settings.general.edit') }}" class="btn btn-outline-primary">
                            <span class="bi bi-gear me-2"></span>{{ __('General Settings') }}
                        </a>
                        <a href="{{ route('settings.mail.edit') }}" class="btn btn-outline-info">
                            <span class="bi bi-envelope me-2"></span>{{ __('Mail Delivery') }}
                        </a>
                        <a href="{{ route('settings.payments.edit') }}" class="btn btn-outline-success">
                            <span class="bi bi-credit-card me-2"></span>{{ __('Payment Settings') }}
                        </a>
                        <a href="{{ route('settings.messaging.edit') }}" class="btn btn-outline-warning">
                            <span class="bi bi-chat-dots me-2"></span>{{ __('Messaging') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
