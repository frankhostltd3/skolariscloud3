@extends('layouts.dashboard-teacher')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Take Roll Call</h1>
            <p class="text-muted mb-0">Select a class and choose your preferred attendance method</p>
        </div>
        <a href="{{ route('tenant.teacher.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Class Selection -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-building me-2"></i>Step 1: Select Class</h5>
        </div>
        <div class="card-body">
            @if($classes->isEmpty())
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>No Classes Assigned:</strong> You don't have any active classes assigned to you. 
                    Please contact the administrator to assign classes or subjects.
                </div>
            @else
                <form method="GET" action="{{ route('tenant.teacher.attendance.take') }}">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label for="class_id" class="form-label">Choose a class to mark attendance</label>
                            <select name="class_id" id="class_id" class="form-select form-select-lg" required>
                            <option value="">-- Select a Class --</option>
                            @forelse($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                    @if($class->section) - {{ $class->section }}@endif
                                    @if($class->stream) ({{ $class->stream }})@endif
                                    - {{ $class->students_count ?? 0 }} {{ Str::plural('student', $class->students_count ?? 0) }}
                                </option>
                            @empty
                                <option value="" disabled>No classes assigned to you</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-arrow-right-circle me-2"></i>Continue
                        </button>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>

    @if($selectedClass)
        <!-- Already Taken Warning -->
        @if($alreadyTaken)
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> Attendance has already been marked for this class today. 
                You can update it using any of the methods below or edit the manual entries.
            </div>
        @endif

        <!-- Method Selection -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Step 2: Choose Attendance Method</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Method 1: Manual Entry -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card h-100 border-primary shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-pencil-square text-primary" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="card-title mb-3">Manual Entry</h4>
                                <p class="text-muted mb-4">
                                    Mark attendance manually for each student using a form. 
                                    Best for small classes or when devices are unavailable.
                                </p>
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Simple and straightforward</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Works without any device</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Add notes for students</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Bulk update support</li>
                                </ul>
                                <a href="{{ route('tenant.teacher.attendance.manual', ['class_id' => $selectedClass->id]) }}" 
                                   class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-pencil-square me-2"></i>Start Manual Entry
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Method 2: Fingerprint Biometric -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card h-100 border-info shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-fingerprint text-info" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="card-title mb-3">Fingerprint Biometric</h4>
                                <p class="text-muted mb-4">
                                    Quick and secure attendance using fingerprint scanner. 
                                    Students scan their fingerprint for instant verification.
                                </p>
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Fast and accurate</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Prevents proxy attendance</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Automatic verification</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Real-time processing</li>
                                </ul>
                                <a href="{{ route('tenant.teacher.attendance.biometric', ['class_id' => $selectedClass->id, 'type' => 'fingerprint']) }}" 
                                   class="btn btn-info btn-lg w-100">
                                    <i class="bi bi-fingerprint me-2"></i>Start Fingerprint Scan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Method 3: Iris Biometric -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card h-100 border-warning shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-eye text-warning" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="card-title mb-3">Iris Recognition</h4>
                                <p class="text-muted mb-4">
                                    High-security attendance using iris scanning technology. 
                                    Most accurate biometric method available.
                                </p>
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Highest accuracy</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Contactless scanning</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Works with glasses</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Maximum security</li>
                                </ul>
                                <a href="{{ route('tenant.teacher.attendance.biometric', ['class_id' => $selectedClass->id, 'type' => 'iris']) }}" 
                                   class="btn btn-warning btn-lg w-100">
                                    <i class="bi bi-eye me-2"></i>Start Iris Scan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Method 4: Barcode/QR Scanning -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card h-100 border-success shadow-sm hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-qr-code-scan text-success" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="card-title mb-3">Barcode/QR Scanning</h4>
                                <p class="text-muted mb-4">
                                    Scan student ID cards with barcode or QR code. 
                                    Quick and easy using mobile camera or scanner.
                                </p>
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Works with ID cards</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use phone camera</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Fast scanning</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>No special hardware</li>
                                </ul>
                                <a href="{{ route('tenant.teacher.attendance.barcode', ['class_id' => $selectedClass->id]) }}" 
                                   class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-qr-code-scan me-2"></i>Start Barcode Scan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
