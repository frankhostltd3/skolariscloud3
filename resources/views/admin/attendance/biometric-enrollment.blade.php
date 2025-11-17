@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-fingerprint"></i> Biometric Enrollment</h4>
                    <div class="page-title-right">
                        <div class="btn-group" role="group">
                            <a href="?type=student" class="btn btn-{{ $userType === 'student' ? 'info' : 'outline-info' }}">
                                <i class="bi bi-person-badge"></i> Students
                            </a>
                            <a href="?type=staff"
                                class="btn btn-{{ $userType === 'staff' ? 'success' : 'outline-success' }}">
                                <i class="bi bi-person-workspace"></i> Staff
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Status Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-2"><i class="bi bi-hdd-network"></i> Fingerprint Device</h6>
                        <p class="mb-0 text-muted">
                            <strong>Type:</strong> {{ ucfirst($settings->fingerprint_device_type) }} |
                            <strong>IP:</strong> {{ $settings->fingerprint_device_ip ?? 'Not configured' }} |
                            <strong>Port:</strong> {{ $settings->fingerprint_device_port ?? 'N/A' }} |
                            <strong>Quality Threshold:</strong> {{ $settings->fingerprint_quality_threshold }}%
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-primary" id="testDeviceBtn">
                            <i class="bi bi-wifi"></i> Test Connection
                        </button>
                        <a href="{{ route('tenant.attendance.settings.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ ucfirst($userType) }} List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Name</th>
                                <th width="20%">ID Number</th>
                                <th width="20%">Enrolled Fingers</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($user->photo ?? false)
                                                <img src="{{ $user->photo }}" class="rounded-circle me-2" width="32"
                                                    height="32">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->admission_number ?? ($user->employee_number ?? 'N/A') }}</td>
                                    <td>
                                        @if ($user->biometric_templates_count > 0)
                                            <span class="badge bg-success">
                                                <i class="bi bi-fingerprint"></i> {{ $user->biometric_templates_count }}
                                                fingers
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Not enrolled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.biometric.enroll', ['userType' => $userType, 'userId' => $user->id]) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle"></i> Enroll
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No {{ $userType }}s found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('testDeviceBtn').addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testing...';

            fetch('{{ route('admin.biometric.test-device') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Device connected successfully!\n\n' + JSON.stringify(data.device, null, 2));
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
    </script>
@endsection
