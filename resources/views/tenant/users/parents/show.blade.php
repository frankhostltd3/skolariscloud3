@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-person-circle me-2 text-primary"></i>{{ $profile?->full_name ?? $user->name }}
                </h1>
                <p class="text-muted mb-0">{{ __('Parent/Guardian Details') }}</p>
            </div>
            <div>
                <a href="{{ route('tenant.users.parents') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back') }}
                </a>
                <a href="{{ route('tenant.users.parents.edit', $user) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>{{ __('Edit') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Main Info -->
            <div class="col-lg-8">
                <!-- Account Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-lock me-2"></i>{{ __('Account Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Email') }}</label>
                                <p class="fw-semibold">
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Account Status') }}</label>
                                <p>
                                    @if ($user->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check-circle me-1"></i>{{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
                                            <i class="bi bi-x-circle me-1"></i>{{ __('Inactive') }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            @if (!$user->is_active && $user->deactivation_reason)
                                <div class="col-12">
                                    <label class="form-label text-muted">{{ __('Deactivation Reason') }}</label>
                                    <p class="text-danger">{{ $user->deactivation_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($profile)
                    <!-- Personal Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person me-2"></i>{{ __('Personal Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted">{{ __('First Name') }}</label>
                                    <p class="fw-semibold">{{ $profile->first_name }}</p>
                                </div>
                                @if ($profile->middle_name)
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('Middle Name') }}</label>
                                        <p class="fw-semibold">{{ $profile->middle_name }}</p>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <label class="form-label text-muted">{{ __('Last Name') }}</label>
                                    <p class="fw-semibold">{{ $profile->last_name }}</p>
                                </div>
                                @if ($profile->gender)
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('Gender') }}</label>
                                        <p>{{ ucfirst($profile->gender) }}</p>
                                    </div>
                                @endif
                                @if ($profile->date_of_birth)
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('Date of Birth') }}</label>
                                        <p>{{ $profile->date_of_birth->format('M d, Y') }}
                                            @if ($profile->age)
                                                <span class="text-muted">({{ $profile->age }} years)</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                @if ($profile->national_id)
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('National ID') }}</label>
                                        <p>{{ $profile->national_id }}</p>
                                    </div>
                                @endif
                                @if ($profile->blood_group)
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('Blood Group') }}</label>
                                        <p><span class="badge bg-danger">{{ $profile->blood_group }}</span></p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-telephone me-2"></i>{{ __('Contact Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">{{ __('Phone') }}</label>
                                    <p class="fw-semibold">
                                        <a href="tel:{{ $profile->phone }}">{{ $profile->phone }}</a>
                                    </p>
                                </div>
                                @if ($profile->alternate_phone)
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">{{ __('Alternate Phone') }}</label>
                                        <p><a
                                                href="tel:{{ $profile->alternate_phone }}">{{ $profile->alternate_phone }}</a>
                                        </p>
                                    </div>
                                @endif
                                @if ($profile->address)
                                    <div class="col-12">
                                        <label class="form-label text-muted">{{ __('Home Address') }}</label>
                                        <p>{{ $profile->address }}</p>
                                    </div>
                                @endif
                                @if ($profile->city || $profile->state || $profile->postal_code)
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('City') }}</label>
                                        <p>{{ $profile->city ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('County') }}</label>
                                        <p>{{ $profile->state ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">{{ __('Postal Code') }}</label>
                                        <p>{{ $profile->postal_code ?? '-' }}</p>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label text-muted">{{ __('Country') }}</label>
                                    <p>{{ $profile->country }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Occupation Information -->
                    @if (
                        $profile->occupation ||
                            $profile->employer ||
                            $profile->work_phone ||
                            $profile->work_address ||
                            $profile->annual_income)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-briefcase me-2"></i>{{ __('Occupation Information') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if ($profile->occupation)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">{{ __('Occupation') }}</label>
                                            <p class="fw-semibold">{{ $profile->occupation }}</p>
                                        </div>
                                    @endif
                                    @if ($profile->employer)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">{{ __('Employer') }}</label>
                                            <p>{{ $profile->employer }}</p>
                                        </div>
                                    @endif
                                    @if ($profile->work_phone)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">{{ __('Work Phone') }}</label>
                                            <p><a href="tel:{{ $profile->work_phone }}">{{ $profile->work_phone }}</a></p>
                                        </div>
                                    @endif
                                    @if ($profile->annual_income)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">{{ __('Annual Income') }}</label>
                                            <p>{{ number_format($profile->annual_income, 2) }} KES</p>
                                        </div>
                                    @endif
                                    @if ($profile->work_address)
                                        <div class="col-12">
                                            <label class="form-label text-muted">{{ __('Work Address') }}</label>
                                            <p>{{ $profile->work_address }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Children/Students -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>{{ __('Children/Students') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($profile->students->count() > 0)
                                <div class="row g-3">
                                    @foreach ($profile->students as $student)
                                        <div class="col-md-6">
                                            <div class="border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">
                                                        <a href="{{ route('tenant.modules.students.show', $student) }}"
                                                            class="text-decoration-none">
                                                            {{ $student->full_name }}
                                                        </a>
                                                    </h6>
                                                    <span
                                                        class="badge bg-primary">{{ ucfirst($student->pivot->relationship) }}</span>
                                                </div>
                                                <p class="text-muted mb-2 small">
                                                    <i class="bi bi-hash me-1"></i>{{ $student->admission_no }}
                                                    @if ($student->class)
                                                        <span class="ms-2"><i
                                                                class="bi bi-book me-1"></i>{{ $student->class->name }}</span>
                                                    @endif
                                                </p>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if ($student->pivot->is_primary)
                                                        <span class="badge bg-success bg-opacity-10 text-success">
                                                            <i class="bi bi-star-fill me-1"></i>Primary
                                                        </span>
                                                    @endif
                                                    @if ($student->pivot->can_pickup)
                                                        <span class="badge bg-info bg-opacity-10 text-info">
                                                            <i class="bi bi-car-front me-1"></i>Can Pickup
                                                        </span>
                                                    @endif
                                                    @if ($student->pivot->financial_responsibility)
                                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                                            <i class="bi bi-cash me-1"></i>Financial
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center py-3">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    {{ __('No children linked to this parent yet.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    @if ($profile->emergency_contact_name || $profile->emergency_contact_phone || $profile->emergency_contact_relation)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-phone-vibrate me-2"></i>{{ __('Emergency Contact') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if ($profile->emergency_contact_name)
                                        <div class="col-md-4">
                                            <label class="form-label text-muted">{{ __('Name') }}</label>
                                            <p class="fw-semibold">{{ $profile->emergency_contact_name }}</p>
                                        </div>
                                    @endif
                                    @if ($profile->emergency_contact_phone)
                                        <div class="col-md-4">
                                            <label class="form-label text-muted">{{ __('Phone') }}</label>
                                            <p><a
                                                    href="tel:{{ $profile->emergency_contact_phone }}">{{ $profile->emergency_contact_phone }}</a>
                                            </p>
                                        </div>
                                    @endif
                                    @if ($profile->emergency_contact_relation)
                                        <div class="col-md-4">
                                            <label class="form-label text-muted">{{ __('Relationship') }}</label>
                                            <p>{{ $profile->emergency_contact_relation }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Additional Information -->
                    @if ($profile->medical_conditions || $profile->notes)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-file-text me-2"></i>{{ __('Additional Information') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($profile->medical_conditions)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ __('Medical Conditions') }}</label>
                                        <p>{{ $profile->medical_conditions }}</p>
                                    </div>
                                @endif
                                @if ($profile->notes)
                                    <div>
                                        <label class="form-label text-muted">{{ __('Notes') }}</label>
                                        <p>{{ $profile->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('Parent profile not yet created. Please edit to add profile information.') }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Profile Photo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        @if ($profile?->profile_photo)
                            <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="{{ $profile->full_name }}"
                                class="img-fluid rounded-circle mb-3"
                                style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 200px; height: 200px;">
                                <i class="bi bi-person-circle display-1 text-muted"></i>
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $profile?->full_name ?? $user->name }}</h5>
                        <p class="text-muted mb-3">{{ __('Parent/Guardian') }}</p>
                        @if ($profile)
                            <span class="badge {{ $profile->getStatusBadgeClass() }} mb-2">
                                {{ ucfirst($profile->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-lightning me-2"></i>{{ __('Quick Actions') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('tenant.users.parents.edit', $user) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>{{ __('Edit Profile') }}
                            </a>

                            @if ($user->is_active)
                                <button type="button" class="btn btn-outline-warning"
                                    onclick="deactivateUser({{ $user->id }})">
                                    <i class="bi bi-pause-circle me-2"></i>{{ __('Deactivate Account') }}
                                </button>
                            @else
                                <form action="{{ route('tenant.users.parents.activate', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success w-100">
                                        <i class="bi bi-play-circle me-2"></i>{{ __('Activate Account') }}
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('tenant.users.parents.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure you want to delete this parent/guardian? This action cannot be undone.') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-2"></i>{{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Record Information -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>{{ __('Record Information') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="mb-2">
                                <span class="text-muted">{{ __('Created') }}:</span>
                                <br>{{ $user->created_at->format('M d, Y h:i A') }}
                            </div>
                            <div>
                                <span class="text-muted">{{ __('Last Updated') }}:</span>
                                <br>{{ $user->updated_at->format('M d, Y h:i A') }}
                            </div>
                            @if ($user->activated_at)
                                <div class="mt-2">
                                    <span class="text-muted">{{ __('Activated') }}:</span>
                                    <br>{{ $user->activated_at->format('M d, Y h:i A') }}
                                </div>
                            @endif
                            @if ($user->deactivated_at)
                                <div class="mt-2">
                                    <span class="text-muted">{{ __('Deactivated') }}:</span>
                                    <br>{{ $user->deactivated_at->format('M d, Y h:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivation Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deactivateForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Deactivate Parent/Guardian') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="deactivation_reason"
                                class="form-label">{{ __('Reason for deactivation (optional)') }}</label>
                            <textarea class="form-control" id="deactivation_reason" name="reason" rows="3"
                                placeholder="{{ __('Enter reason...') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('Deactivate') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function deactivateUser(userId) {
                const modal = new bootstrap.Modal(document.getElementById('deactivateModal'));
                const form = document.getElementById('deactivateForm');
                form.action = `/users/parents/${userId}/deactivate`;
                modal.show();
            }
        </script>
    @endpush
@endsection
