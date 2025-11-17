@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Attendance Settings</h4>
                    <div class="page-title-right">
                        <form action="{{ route('tenant.attendance.settings.clear-cache') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Clear Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- General Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear text-primary"></i> General Settings
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.attendance.settings.update-general') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Grace Period (minutes)</label>
                                <input type="number" name="grace_period_minutes" class="form-control"
                                    value="{{ $settings->grace_period_minutes }}" min="0" max="60" required>
                                <small class="text-muted">Time window to prevent duplicate scans</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Allow Manual Override</label>
                                <select name="allow_manual_override" class="form-select" required>
                                    <option value="1" {{ $settings->allow_manual_override ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0" {{ !$settings->allow_manual_override ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                <small class="text-muted">Allow admins to manually edit attendance</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Require Approval for Changes</label>
                                <select name="require_approval_for_changes" class="form-select" required>
                                    <option value="1" {{ $settings->require_approval_for_changes ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="0" {{ !$settings->require_approval_for_changes ? 'selected' : '' }}>
                                        No</option>
                                </select>
                                <small class="text-muted">Manual changes need supervisor approval</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Update General Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student Attendance Methods -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-badge text-info"></i> Student Attendance Methods
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.attendance.settings.update-student-methods') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="student_manual_enabled"
                                    id="studentManual" value="1"
                                    {{ $settings->student_manual_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="studentManual">
                                    <i class="bi bi-pencil-square"></i> Manual Entry
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="student_qr_enabled" id="studentQr"
                                    value="1" {{ $settings->student_qr_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="studentQr">
                                    <i class="bi bi-qr-code"></i> QR Code Scanning
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="student_barcode_enabled"
                                    id="studentBarcode" value="1"
                                    {{ $settings->student_barcode_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="studentBarcode">
                                    <i class="bi bi-upc-scan"></i> Barcode Scanning
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="student_fingerprint_enabled"
                                    id="studentFingerprint" value="1"
                                    {{ $settings->student_fingerprint_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="studentFingerprint">
                                    <i class="bi bi-fingerprint"></i> Fingerprint Scanning
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="student_optical_enabled"
                                    id="studentOptical" value="1"
                                    {{ $settings->student_optical_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="studentOptical">
                                    <i class="bi bi-file-earmark-medical"></i> Optical Scanning (OMR)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-check-lg"></i> Update Student Methods
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Staff Attendance Methods -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-workspace text-success"></i> Staff Attendance Methods
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.attendance.settings.update-staff-methods') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="staff_manual_enabled"
                                    id="staffManual" value="1"
                                    {{ $settings->staff_manual_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="staffManual">
                                    <i class="bi bi-pencil-square"></i> Manual Entry
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="staff_qr_enabled" id="staffQr"
                                    value="1" {{ $settings->staff_qr_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="staffQr">
                                    <i class="bi bi-qr-code"></i> QR Code Scanning
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="staff_barcode_enabled"
                                    id="staffBarcode" value="1"
                                    {{ $settings->staff_barcode_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="staffBarcode">
                                    <i class="bi bi-upc-scan"></i> Barcode Scanning
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="staff_fingerprint_enabled"
                                    id="staffFingerprint" value="1"
                                    {{ $settings->staff_fingerprint_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="staffFingerprint">
                                    <i class="bi bi-fingerprint"></i> Fingerprint Scanning
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="staff_optical_enabled"
                                    id="staffOptical" value="1"
                                    {{ $settings->staff_optical_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="staffOptical">
                                    <i class="bi bi-file-earmark-medical"></i> Optical Scanning (OMR)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Update Staff Methods
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- QR/Barcode Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-qr-code text-warning"></i> QR/Barcode Settings
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.attendance.settings.update-qr') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Code Format</label>
                                <select name="qr_code_format" class="form-select" required>
                                    <option value="qr" {{ $settings->qr_code_format == 'qr' ? 'selected' : '' }}>QR
                                        Code</option>
                                    <option value="barcode"
                                        {{ $settings->qr_code_format == 'barcode' ? 'selected' : '' }}>Barcode</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Code Size (px)</label>
                                <input type="number" name="qr_code_size" class="form-control"
                                    value="{{ $settings->qr_code_size }}" min="100" max="500" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Code Prefix</label>
                                <input type="text" name="qr_code_prefix" class="form-control"
                                    value="{{ $settings->qr_code_prefix }}" maxlength="10" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Auto Generate</label>
                                <select name="qr_auto_generate" class="form-select" required>
                                    <option value="1" {{ $settings->qr_auto_generate ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0" {{ !$settings->qr_auto_generate ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Update QR Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Fingerprint Device Settings -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-fingerprint text-danger"></i> Fingerprint Device Settings
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="testDeviceBtn">
                    <i class="bi bi-wifi"></i> Test Connection
                </button>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.attendance.settings.update-fingerprint') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Device Type</label>
                                <select name="fingerprint_device_type" class="form-select" required>
                                    <option value="zkteco"
                                        {{ $settings->fingerprint_device_type == 'zkteco' ? 'selected' : '' }}>ZKTeco
                                    </option>
                                    <option value="morpho"
                                        {{ $settings->fingerprint_device_type == 'morpho' ? 'selected' : '' }}>Morpho
                                    </option>
                                    <option value="suprema"
                                        {{ $settings->fingerprint_device_type == 'suprema' ? 'selected' : '' }}>Suprema
                                    </option>
                                    <option value="generic"
                                        {{ $settings->fingerprint_device_type == 'generic' ? 'selected' : '' }}>Generic
                                        HTTP</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Device IP Address</label>
                                <input type="text" name="fingerprint_device_ip" class="form-control"
                                    value="{{ $settings->fingerprint_device_ip }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Device Port</label>
                                <input type="number" name="fingerprint_device_port" class="form-control"
                                    value="{{ $settings->fingerprint_device_port }}" min="1" max="65535"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Quality Threshold (%)</label>
                                <input type="number" name="fingerprint_quality_threshold" class="form-control"
                                    value="{{ $settings->fingerprint_quality_threshold }}" min="0" max="100"
                                    required>
                                <small class="text-muted">Minimum quality score to accept</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Timeout (seconds)</label>
                                <input type="number" name="fingerprint_timeout_seconds" class="form-control"
                                    value="{{ $settings->fingerprint_timeout_seconds }}" min="5" max="60"
                                    required>
                                <small class="text-muted">Connection timeout</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check-lg"></i> Update Fingerprint Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Optical Scanner Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-medical text-secondary"></i> Optical Scanner Settings
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.attendance.settings.update-optical') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Use OMR Technology</label>
                                <select name="optical_use_omr" class="form-select" required>
                                    <option value="1" {{ $settings->optical_use_omr ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !$settings->optical_use_omr ? 'selected' : '' }}>No</option>
                                </select>
                                <small class="text-muted">Optical Mark Recognition for bubble sheets</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Template ID</label>
                                <input type="number" name="optical_template_id" class="form-control"
                                    value="{{ $settings->optical_template_id }}">
                                <small class="text-muted">Optional: Pre-defined sheet template</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Mark Sensitivity (%)</label>
                                <input type="number" name="optical_mark_sensitivity" class="form-control"
                                    value="{{ $settings->optical_mark_sensitivity }}" min="1" max="100"
                                    required>
                                <small class="text-muted">Darkness threshold for marked bubbles</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-check-lg"></i> Update Optical Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testDeviceBtn').addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testing...';

            fetch('{{ route('tenant.attendance.settings.test-fingerprint') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Device connected successfully!\n\nDevice Info:\n' + JSON.stringify(data.device,
                            null, 2));
                    } else {
                        alert('❌ Connection failed: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Error: ' + error.message);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection
