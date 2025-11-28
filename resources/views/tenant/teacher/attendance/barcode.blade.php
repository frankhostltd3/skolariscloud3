@extends('layouts.dashboard-teacher')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-qr-code-scan me-2"></i>Barcode/QR Attendance
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
            <!-- Scanner Card -->
            <div class="card border-success shadow-lg">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-upc-scan me-2"></i>Scan Student ID
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <!-- Scanner Animation -->
                    <div class="scanner-box mb-4" id="scannerBox">
                        <i class="bi bi-qr-code" style="font-size: 8rem; color: #198754;"></i>
                        <div class="scanner-beam"></div>
                    </div>

                    <h3 class="mb-3" id="scannerStatus">Ready to Scan</h3>
                    <p class="text-muted mb-4" id="scannerMessage">
                        Scan student ID card barcode or QR code
                    </p>

                    <!-- Manual Entry Fallback -->
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="barcodeInput" 
                                       placeholder="Scan or type barcode..." 
                                       autofocus>
                                <button class="btn btn-success" type="button" onclick="processManualBarcode()">
                                    <i class="bi bi-arrow-right-circle"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                Focus on the input box and scan the barcode, or type it manually
                            </small>
                        </div>
                    </div>

                    <!-- Simulate Scan Buttons (for demo) -->
                    <div class="alert alert-warning mt-4" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Demo Mode:</strong> Use buttons below to simulate barcode scans.
                        In production, use actual barcode scanner or camera.
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-success" onclick="simulateScan('STU-000001')">
                            Simulate STU-001
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="simulateScan('STU-000002')">
                            Simulate STU-002
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="simulateScan('STU-000003')">
                            Simulate STU-003
                        </button>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Instructions</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Using Barcode Scanner:</h6>
                    <ol class="mb-4">
                        <li>Connect USB barcode scanner to your computer</li>
                        <li>Click on the input box to focus it</li>
                        <li>Scan each student's ID card</li>
                        <li>The barcode will be entered automatically and processed</li>
                        <li>Student will appear in the "Marked Students" list</li>
                    </ol>

                    <h6 class="fw-bold mb-3">Using Camera (QR Code):</h6>
                    <ol class="mb-4">
                        <li>Click "Enable Camera" button (below)</li>
                        <li>Allow camera access when prompted</li>
                        <li>Hold student ID QR code in front of camera</li>
                        <li>Wait for automatic detection</li>
                    </ol>

                    <button type="button" class="btn btn-primary" onclick="enableCamera()" id="cameraBtn">
                        <i class="bi bi-camera-video me-2"></i>Enable Camera
                    </button>

                    <h6 class="fw-bold mb-3 mt-4">Manual Entry:</h6>
                    <p class="mb-0">Type the barcode number manually in the input box and click the arrow button.</p>
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
let markedCount = {{ $markedStudents->count() }};
let totalStudents = {{ $class->students_count ?? 0 }};
let cameraEnabled = false;

// Listen for barcode scanner input
document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        processManualBarcode();
    }
});

// Auto-submit after scanner input (scanners typically send Enter)
let barcodeBuffer = '';
let barcodeTimeout;

document.getElementById('barcodeInput').addEventListener('input', function(e) {
    clearTimeout(barcodeTimeout);
    barcodeTimeout = setTimeout(() => {
        if (this.value.length > 3) {
            processManualBarcode();
        }
    }, 100);
});

function processManualBarcode() {
    const input = document.getElementById('barcodeInput');
    const barcode = input.value.trim();
    
    if (!barcode) {
        return;
    }
    
    processBarcode(barcode);
    input.value = '';
}

function simulateScan(barcode) {
    document.getElementById('barcodeInput').value = barcode;
    processBarcode(barcode);
    setTimeout(() => {
        document.getElementById('barcodeInput').value = '';
    }, 1000);
}

function processBarcode(barcode) {
    // Update UI to show processing
    document.getElementById('scannerStatus').textContent = 'Processing...';
    document.getElementById('scannerStatus').className = 'mb-3 text-warning';
    document.getElementById('scannerMessage').textContent = 'Verifying barcode...';
    
    // Send AJAX request
    fetch('{{ route("tenant.teacher.attendance.barcode.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            class_id: classId,
            barcode: barcode,
            attendance_date: attendanceDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success
            document.getElementById('scannerStatus').textContent = 'Success!';
            document.getElementById('scannerStatus').className = 'mb-3 text-success';
            document.getElementById('scannerMessage').textContent = data.message;
            
            // Add to marked students list
            addMarkedStudent(data.student, data.timestamp);
            
            // Play success sound
            playBeep();
            
            // Reset after 2 seconds
            setTimeout(resetScanner, 2000);
        } else {
            // Error
            document.getElementById('scannerStatus').textContent = 'Error';
            document.getElementById('scannerStatus').className = 'mb-3 text-danger';
            document.getElementById('scannerMessage').textContent = data.message;
            
            // Play error sound
            playErrorBeep();
            
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
    document.getElementById('scannerMessage').textContent = 'Scan student ID card barcode or QR code';
    document.getElementById('barcodeInput').focus();
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

function enableCamera() {
    if (cameraEnabled) {
        alert('Camera QR scanning requires a library like Html5-QRCode. This is a placeholder for production implementation.');
        return;
    }
    
    alert('Camera feature will be implemented using Html5-QRCode library in production.');
    // In production, use: https://github.com/mebjas/html5-qrcode
}

function playBeep() {
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

function playErrorBeep() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 400;
    oscillator.type = 'sawtooth';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.3);
}

// Auto-focus input on load
document.getElementById('barcodeInput').focus();
</script>

<style>
.scanner-box {
    position: relative;
    display: inline-block;
}

.scanner-beam {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, transparent, #198754, transparent);
    animation: scanBeam 2s infinite;
}

@keyframes scanBeam {
    0%, 100% {
        transform: translateY(-50px);
        opacity: 0;
    }
    50% {
        transform: translateY(50px);
        opacity: 1;
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

#barcodeInput:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}
</style>
@endsection
