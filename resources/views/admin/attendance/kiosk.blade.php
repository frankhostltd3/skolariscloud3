@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', 'Attendance Kiosk Mode')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-fingerprint me-2 text-info"></i>
                    Attendance Kiosk Mode
                </h2>
                <p class="text-muted mb-0">Self-service attendance check-in system</p>
            </div>
            <a href="{{ route('tenant.modules.attendance.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Attendance
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        <i class="fas fa-fingerprint fa-5x text-info mb-4"></i>
                        <h3 class="mb-3">Attendance Kiosk</h3>
                        <p class="text-muted mb-4">Scan your fingerprint or enter your student ID to check in</p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Coming Soon:</strong> Fingerprint scanner integration and kiosk mode will be available
                            in a future update.
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <i class="fas fa-fingerprint fa-3x text-primary mb-3"></i>
                                        <h5>Fingerprint Scanner</h5>
                                        <p class="small text-muted">Biometric authentication for quick check-in</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <i class="fas fa-id-card fa-3x text-success mb-3"></i>
                                        <h5>Student ID Card</h5>
                                        <p class="small text-muted">RFID or barcode scanning</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
