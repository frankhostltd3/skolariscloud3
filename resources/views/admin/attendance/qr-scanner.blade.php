@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-qr-code-scan"></i> QR Code Scanner</h4>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-outline-secondary" id="toggleCameraBtn">
                            <i class="bi bi-camera-video"></i> <span id="cameraStatus">Start Camera</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Scanner Column -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Scanner</h5>
                    </div>
                    <div class="card-body">
                        <!-- Attendance Session Selection -->
                        <div class="mb-3">
                            <label class="form-label">Select Attendance Session (Optional)</label>
                            <select class="form-select" id="attendanceSession">
                                <option value="">-- Validate Only (No Recording) --</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">
                                        {{ $session->class->name }} - {{ $session->subject->name ?? 'General' }}
                                        ({{ $session->date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave blank to validate QR codes without recording attendance</small>
                        </div>

                        <!-- User Type Selection -->
                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="userType" id="typeStudent" value="student"
                                    checked>
                                <label class="btn btn-outline-info" for="typeStudent">
                                    <i class="bi bi-person-badge"></i> Students
                                </label>

                                <input type="radio" class="btn-check" name="userType" id="typeStaff" value="staff">
                                <label class="btn btn-outline-success" for="typeStaff">
                                    <i class="bi bi-person-workspace"></i> Staff
                                </label>
                            </div>
                        </div>

                        <!-- Video Stream -->
                        <div id="videoContainer" class="position-relative mb-3" style="display: none;">
                            <video id="qrVideo" class="w-100 rounded"
                                style="max-height: 400px; background: #000;"></video>
                            <div id="scanOverlay" class="position-absolute top-50 start-50 translate-middle">
                                <div class="border border-primary border-3 rounded"
                                    style="width: 250px; height: 250px; opacity: 0.7;"></div>
                            </div>
                        </div>

                        <!-- Manual Code Entry -->
                        <div id="manualEntry">
                            <label class="form-label">Or Enter Code Manually</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="manualCode"
                                    placeholder="Scan or type QR code here">
                                <button class="btn btn-primary" id="processManualBtn">
                                    <i class="bi bi-check-circle"></i> Process
                                </button>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Click "Start Camera" to begin scanning</li>
                                <li>Hold QR code within the frame</li>
                                <li>Wait for automatic detection and beep</li>
                                <li>Or manually enter code and click Process</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Column -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Scan Results</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearResultsBtn">
                            <i class="bi bi-trash"></i> Clear
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="resultsContainer">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-qr-code-scan" style="font-size: 3rem;"></i>
                                <p class="mt-3">Scan a QR code to see results here</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Session Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h3 class="text-success mb-0" id="statScanned">0</h3>
                                <small class="text-muted">Scanned</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-primary mb-0" id="statSuccess">0</h3>
                                <small class="text-muted">Success</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-danger mb-0" id="statFailed">0</h3>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        let html5QrCode;
        let cameraActive = false;
        let stats = {
            scanned: 0,
            success: 0,
            failed: 0
        };

        // Toggle camera
        document.getElementById('toggleCameraBtn').addEventListener('click', function() {
            if (cameraActive) {
                stopCamera();
            } else {
                startCamera();
            }
        });

        // Start camera
        function startCamera() {
            const videoContainer = document.getElementById('videoContainer');
            videoContainer.style.display = 'block';

            html5QrCode = new Html5Qrcode("qrVideo");

            html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                onScanSuccess,
                onScanError
            ).then(() => {
                cameraActive = true;
                document.getElementById('cameraStatus').textContent = 'Stop Camera';
                document.querySelector('#toggleCameraBtn i').className = 'bi bi-camera-video-off';
            }).catch(err => {
                alert('Error starting camera: ' + err);
            });
        }

        // Stop camera
        function stopCamera() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    cameraActive = false;
                    document.getElementById('videoContainer').style.display = 'none';
                    document.getElementById('cameraStatus').textContent = 'Start Camera';
                    document.querySelector('#toggleCameraBtn i').className = 'bi bi-camera-video';
                });
            }
        }

        // Handle successful scan
        function onScanSuccess(decodedText, decodedResult) {
            processCode(decodedText);
            playBeep();
        }

        function onScanError(errorMessage) {
            // Ignore scan errors (normal when no QR in frame)
        }

        // Process QR code
        function processCode(code) {
            const attendanceId = document.getElementById('attendanceSession').value;
            const userType = document.querySelector('input[name="userType"]:checked').value;

            stats.scanned++;
            updateStats();

            fetch('{{ route('admin.qr-scanner.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        attendance_id: attendanceId || null,
                        user_type: userType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        stats.success++;
                        addResult(data, 'success');
                    } else {
                        stats.failed++;
                        addResult(data, 'error');
                    }
                    updateStats();
                })
                .catch(error => {
                    stats.failed++;
                    addResult({
                        message: 'Network error: ' + error.message
                    }, 'error');
                    updateStats();
                });
        }

        // Add result to display
        function addResult(data, type) {
            const container = document.getElementById('resultsContainer');

            // Remove placeholder
            if (container.querySelector('.text-muted')) {
                container.innerHTML = '';
            }

            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'check-circle-fill' : 'x-circle-fill';

            const result = document.createElement('div');
            result.className = `alert ${alertClass} alert-dismissible fade show`;
            result.innerHTML = `
        <i class="bi bi-${icon}"></i>
        <strong>${data.message}</strong>
        ${data.user ? `<br><small>${data.user.user_type} ID: ${data.user.user_id}</small>` : ''}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

            container.insertBefore(result, container.firstChild);

            // Keep only last 10 results
            while (container.children.length > 10) {
                container.removeChild(container.lastChild);
            }
        }

        // Update statistics
        function updateStats() {
            document.getElementById('statScanned').textContent = stats.scanned;
            document.getElementById('statSuccess').textContent = stats.success;
            document.getElementById('statFailed').textContent = stats.failed;
        }

        // Play beep sound
        function playBeep() {
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }

        // Manual code processing
        document.getElementById('processManualBtn').addEventListener('click', function() {
            const code = document.getElementById('manualCode').value.trim();
            if (code) {
                processCode(code);
                document.getElementById('manualCode').value = '';
            }
        });

        // Allow Enter key in manual input
        document.getElementById('manualCode').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('processManualBtn').click();
            }
        });

        // Clear results
        document.getElementById('clearResultsBtn').addEventListener('click', function() {
            document.getElementById('resultsContainer').innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-qr-code-scan" style="font-size: 3rem;"></i>
            <p class="mt-3">Scan a QR code to see results here</p>
        </div>
    `;
            stats = {
                scanned: 0,
                success: 0,
                failed: 0
            };
            updateStats();
        });

        // Stop camera when leaving page
        window.addEventListener('beforeunload', function() {
            if (cameraActive) {
                stopCamera();
            }
        });
    </script>
@endsection
