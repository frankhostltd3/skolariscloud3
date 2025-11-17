@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="bi bi-fingerprint"></i> Enroll Fingerprints - {{ $user->name }}
                    </h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.biometric.index', ['type' => $userType]) }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Enrollment Interface -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Capture Fingerprint</h5>
                    </div>
                    <div class="card-body">
                        <!-- Finger Selection -->
                        <div class="mb-4">
                            <label class="form-label">Select Finger to Enroll</label>
                            <div class="row g-2">
                                @foreach ([1 => 'Right Thumb', 2 => 'Right Index', 3 => 'Right Middle', 4 => 'Right Ring', 5 => 'Right Pinky', 6 => 'Left Thumb', 7 => 'Left Index', 8 => 'Left Middle', 9 => 'Left Ring', 10 => 'Left Pinky'] as $position => $name)
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="fingerPosition"
                                            id="finger{{ $position }}" value="{{ $position }}">
                                        <label class="btn btn-outline-primary w-100" for="finger{{ $position }}">
                                            <i class="bi bi-fingerprint"></i> {{ $name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Capture Area -->
                        <div class="text-center mb-4">
                            <div class="border border-primary rounded p-5 mb-3" style="background: #f8f9fa;">
                                <i class="bi bi-fingerprint text-primary" style="font-size: 5rem;" id="captureIcon"></i>
                                <div id="qualityIndicator" class="mt-3" style="display: none;">
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar" id="qualityBar" style="width: 0%;">0%
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block" id="qualityText">Minimum:
                                        {{ $settings->fingerprint_quality_threshold }}%</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-lg" id="captureBtn">
                                <i class="bi bi-hand-index-thumb"></i> Start Capture
                            </button>
                            <button type="button" class="btn btn-success btn-lg" id="saveBtn" style="display: none;">
                                <i class="bi bi-save"></i> Save Fingerprint
                            </button>
                        </div>

                        <!-- Instructions -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Enrollment Instructions:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Select which finger to enroll</li>
                                <li>Click "Start Capture"</li>
                                <li>Place finger on scanner</li>
                                <li>Wait for quality indicator to reach {{ $settings->fingerprint_quality_threshold }}%</li>
                                <li>Click "Save Fingerprint"</li>
                                <li>Repeat for additional fingers</li>
                            </ol>
                        </div>

                        <!-- Device Status -->
                        <div class="alert alert-secondary">
                            <strong>Device:</strong> {{ ucfirst($settings->fingerprint_device_type) }} at
                            {{ $settings->fingerprint_device_ip }}:{{ $settings->fingerprint_device_port }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Fingers -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Enrolled Fingers ({{ $templates->count() }}/10)</h5>
                    </div>
                    <div class="card-body">
                        @forelse($templates as $template)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                                <div>
                                    <i class="bi bi-fingerprint text-success"></i>
                                    <strong>{{ $template->getFingerName() }}</strong>
                                    <br>
                                    <small class="text-muted">Quality: {{ $template->quality_score }}%</small>
                                    <br>
                                    <small class="text-muted">{{ $template->enrolled_at->format('M d, Y') }}</small>
                                </div>
                                <form action="{{ route('admin.biometric.delete', $template->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this fingerprint template?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-fingerprint" style="font-size: 3rem;"></i>
                                <p class="mt-2">No fingerprints enrolled yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- User Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>ID:</strong> {{ $user->admission_number ?? ($user->employee_number ?? 'N/A') }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($userType) }}</p>
                        @if ($userType === 'student' && $user->class ?? false)
                            <p><strong>Class:</strong> {{ $user->class->name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let capturedTemplate = null;
        let selectedFinger = null;

        // Finger selection
        document.querySelectorAll('input[name="fingerPosition"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedFinger = this.value;
                document.getElementById('captureBtn').disabled = false;
            });
        });

        // Capture button
        document.getElementById('captureBtn').addEventListener('click', function() {
            if (!selectedFinger) {
                alert('Please select a finger first');
                return;
            }

            // Simulate fingerprint capture (in production, this would connect to device)
            simulateCapture();
        });

        // Simulate capture process
        function simulateCapture() {
            const btn = document.getElementById('captureBtn');
            const icon = document.getElementById('captureIcon');
            const qualityIndicator = document.getElementById('qualityIndicator');
            const qualityBar = document.getElementById('qualityBar');
            const qualityText = document.getElementById('qualityText');
            const saveBtn = document.getElementById('saveBtn');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Capturing...';
            qualityIndicator.style.display = 'block';

            // Simulate progressive quality improvement
            let quality = 0;
            const threshold = {{ $settings->fingerprint_quality_threshold }};

            const interval = setInterval(() => {
                quality += Math.random() * 20;
                if (quality > 100) quality = 100;

                qualityBar.style.width = quality + '%';
                qualityBar.textContent = Math.round(quality) + '%';
                qualityText.textContent = `Quality: ${Math.round(quality)}% (Minimum: ${threshold}%)`;

                // Color coding
                if (quality < threshold) {
                    qualityBar.className = 'progress-bar bg-danger';
                } else if (quality < 85) {
                    qualityBar.className = 'progress-bar bg-warning';
                } else {
                    qualityBar.className = 'progress-bar bg-success';
                }

                if (quality >= 95) {
                    clearInterval(interval);

                    // Generate dummy template data
                    capturedTemplate = {
                        quality: Math.round(quality),
                        data: 'SIMULATED_TEMPLATE_DATA_' + Date.now() + '_' + Math.random().toString(36)
                    };

                    icon.className = 'bi bi-check-circle-fill text-success';
                    btn.style.display = 'none';
                    saveBtn.style.display = 'inline-block';

                    alert('✅ Fingerprint captured successfully! Click "Save Fingerprint" to enroll.');
                }
            }, 300);
        }

        // Save button
        document.getElementById('saveBtn').addEventListener('click', function() {
            if (!capturedTemplate || !selectedFinger) {
                alert('Please capture a fingerprint first');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            fetch('{{ route('admin.biometric.capture') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: {{ $user->id }},
                        user_type: '{{ $userType }}',
                        finger_position: selectedFinger,
                        template_data: capturedTemplate.data,
                        quality_score: capturedTemplate.quality
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message);
                        window.location.reload();
                    } else {
                        alert('❌ ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-save"></i> Save Fingerprint';
                    }
                })
                .catch(error => {
                    alert('❌ Error: ' + error.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-save"></i> Save Fingerprint';
                });
        });
    </script>
@endsection
