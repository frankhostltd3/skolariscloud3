@extends('layouts.dashboard-teacher')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-{{ $type == 'fingerprint' ? 'fingerprint' : 'eye' }} me-2"></i>
                {{ ucfirst($type) }} Attendance
            </h1>
            <p class="text-muted mb-0">{{ $class->name }} {{ $class->section }} - {{ $today->format('l, F d, Y') }}</p>
        </div>
        <a href="{{ route('tenant.teacher.attendance.take', ['class_id' => $class->id]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="row">
        <!-- Scanner Interface -->
        <div class="col-lg-7 mb-4">
            <!-- Device Status -->
            <div class="card mb-4 {{ $deviceStatus['connected'] ? 'border-success' : 'border-danger' }}">
                <div class="card-header {{ $deviceStatus['connected'] ? 'bg-success' : 'bg-danger' }} text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-{{ $deviceStatus['connected'] ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                        Device Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1"><strong>{{ $deviceStatus['message'] }}</strong></p>
                            @if($deviceStatus['connected'])
                                <small class="text-muted">{{ $deviceStatus['device_name'] }}</small>
                            @else
                                <small class="text-danger">Please connect the device and refresh the page.</small>
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scanner Card -->
            <div class="card border-primary shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check me-2"></i>Scan {{ ucfirst($type) }}
                    </h5>
                </div>
                <div class="card-body text-center p-5" id="scannerArea">
                    @if($deviceStatus['connected'])
                        <!-- Scanner Animation -->
                        <div class="scanner-animation mb-4" id="scannerAnimation">
                            <i class="bi bi-{{ $type == 'fingerprint' ? 'fingerprint' : 'eye' }}" 
                               style="font-size: 8rem; color: #0d6efd;"></i>
                            <div class="scanning-line"></div>
                        </div>

                        <h3 class="mb-3" id="scannerStatus">Ready to Scan</h3>
                        <p class="text-muted mb-4" id="scannerMessage">
                            Place {{ $type == 'fingerprint' ? 'finger on the scanner' : 'eye in front of the camera' }} to mark attendance
                        </p>

                        <!-- Simulate Scan Button (for demo purposes) -->
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Demo Mode:</strong> Click "Simulate Scan" to test the system. 
                            In production, this would be automatic when biometric is detected.
                        </div>

                        <button type="button" class="btn btn-primary btn-lg" onclick="simulateScan()">
                            <i class="bi bi-play-circle me-2"></i>Simulate Scan (Demo)
                        </button>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 5rem;"></i>
                            <h4 class="mt-4 text-danger">Device Not Connected</h4>
                            <p class="text-muted">Please connect the {{ $type }} scanner and refresh this page.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Instructions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Instructions</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        @if($type == 'fingerprint')
                            <li>Ensure the fingerprint scanner is connected and powered on</li>
                            <li>Ask each student to place their finger on the scanner</li>
                            <li>Wait for the beep sound indicating successful scan</li>
                            <li>The student's name will appear in the "Marked Students" list</li>
                            <li>Repeat for all students</li>
                        @else
                            <li>Ensure the iris scanner is connected and powered on</li>
                            <li>Ask each student to look into the camera</li>
                            <li>Keep eye open and steady for 2-3 seconds</li>
                            <li>Wait for the beep sound indicating successful scan</li>
                            <li>The student's name will appear in the "Marked Students" list</li>
                        @endif
                    </ol>
                </div>
            </div>
        </div>

        <!-- Marked Students List -->
        <div class="col-lg-5">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check-fill me-2"></i>
                        Marked Students
                        <span class="badge bg-white text-success ms-2" id="markedCount">{{ $markedStudents->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div id="markedStudentsList">
                        @forelse($markedStudents as $marked)
                            <div class="d-flex align-items-center p-3 border-bottom">
                                @if($marked->student && $marked->student->photo)
                                    <img src="{{ asset('storage/' . $marked->student->photo) }}" 
                                         alt="{{ $marked->student->name }}" 
                                         class="rounded-circle me-3" 
                                         width="50" height="50">
                                @else
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px; font-size: 20px;">
                                        {{ $marked->student ? strtoupper(substr($marked->student->name, 0, 1)) : '?' }}
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $marked->student->name ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $marked->created_at->format('h:i A') }}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted" id="emptyState">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-3">No students marked yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Students:</span>
                        <strong>{{ $class->students_count ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted">Remaining:</span>
                        <strong id="remainingCount">{{ ($class->students_count ?? 0) - $markedStudents->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const classId = {{ $class->id }};
const attendanceDate = '{{ $today->toDateString() }}';
const biometricType = '{{ $type }}';
let markedCount = {{ $markedStudents->count() }};
let totalStudents = {{ $class->students_count ?? 0 }};

// Simulate scan for demo purposes
// In production, this would listen to the actual biometric device SDK
function simulateScan() {
    // Generate random student ID for demo
    // In production, this data comes from the biometric device
    const randomStudentId = Math.floor(Math.random() * 1000) + 1;
    const biometricData = `biometric_${biometricType}_${randomStudentId}`;
    
    processBiometric(biometricData);
}

function processBiometric(biometricData) {
    // Update UI to show scanning
    document.getElementById('scannerStatus').textContent = 'Processing...';
    document.getElementById('scannerStatus').className = 'mb-3 text-warning';
    document.getElementById('scannerMessage').textContent = 'Please wait...';
    
    // Send AJAX request
    fetch('{{ route("tenant.teacher.attendance.biometric.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            class_id: classId,
            biometric_data: biometricData,
            type: biometricType,
            attendance_date: attendanceDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success - show student info
            document.getElementById('scannerStatus').textContent = 'Success!';
            document.getElementById('scannerStatus').className = 'mb-3 text-success';
            document.getElementById('scannerMessage').textContent = data.message;
            
            // Add to marked students list
            addMarkedStudent(data.student, data.timestamp);
            
            // Play success sound (optional)
            playBeep();
            
            // Reset after 2 seconds
            setTimeout(resetScanner, 2000);
        } else {
            // Error
            document.getElementById('scannerStatus').textContent = 'Error';
            document.getElementById('scannerStatus').className = 'mb-3 text-danger';
            document.getElementById('scannerMessage').textContent = data.message;
            
            // Reset after 3 seconds
            setTimeout(resetScanner, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('scannerStatus').textContent = 'Connection Error';
        document.getElementById('scannerStatus').className = 'mb-3 text-danger';
        document.getElementById('scannerMessage').textContent = 'Failed to communicate with server';
        setTimeout(resetScanner, 3000);
    });
}

function resetScanner() {
    document.getElementById('scannerStatus').textContent = 'Ready to Scan';
    document.getElementById('scannerStatus').className = 'mb-3';
    document.getElementById('scannerMessage').textContent = 'Place ' + (biometricType === 'fingerprint' ? 'finger on the scanner' : 'eye in front of the camera') + ' to mark attendance';
}

function addMarkedStudent(student, timestamp) {
    const list = document.getElementById('markedStudentsList');
    const emptyState = document.getElementById('emptyState');
    
    // Remove empty state if exists
    if (emptyState) {
        emptyState.remove();
    }
    
    // Create student item
    const studentHtml = `
        <div class="d-flex align-items-center p-3 border-bottom bg-success bg-opacity-10">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                 style="width: 50px; height: 50px; font-size: 20px;">
                ${student.name.charAt(0).toUpperCase()}
            </div>
            <div class="flex-grow-1">
                <div class="fw-medium">${student.name}</div>
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>${timestamp}
                </small>
            </div>
            <div>
                <span class="badge bg-success">
                    <i class="bi bi-check-lg"></i>
                </span>
            </div>
        </div>
    `;
    
    list.insertAdjacentHTML('afterbegin', studentHtml);
    
    // Update counts
    markedCount++;
    document.getElementById('markedCount').textContent = markedCount;
    document.getElementById('remainingCount').textContent = totalStudents - markedCount;
}

function playBeep() {
    // Create a simple beep sound
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.2);
}
</script>

<style>
.scanner-animation {
    position: relative;
    display: inline-block;
}

.scanning-line {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #0d6efd, transparent);
    animation: scan 2s infinite;
}

@keyframes scan {
    0% {
        top: 0;
    }
    50% {
        top: 100%;
    }
    100% {
        top: 0;
    }
}

#markedStudentsList > div:first-child {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endsection
