@extends('layouts.tenant.student')

@section('title', 'My Profile')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('My Profile') }}</h1>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            <i class="bi bi-pencil-square me-1"></i>{{ __('Edit Profile') }}
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if ($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo"
                                class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                style="width: 120px; height: 120px;">
                                <span class="text-primary fw-bold"
                                    style="font-size: 3rem;">{{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-semibold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">
                        <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                    </p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-success-subtle text-success border border-success">
                            <i class="bi bi-mortarboard me-1"></i>Student
                        </span>
                        @if ($student)
                            <span class="badge bg-primary-subtle text-primary border border-primary">
                                {{ $student->student_number ?? 'N/A' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-graph-up me-2"></i>{{ __('Quick Stats') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted small">{{ __('Current Class') }}</span>
                        <span
                            class="fw-semibold">{{ optional(optional($student)->currentClass)->name ?? (optional(optional($student)->classroom)->name ?? 'N/A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted small">{{ __('Stream') }}</span>
                        <span
                            class="fw-semibold">{{ optional(optional($student)->stream)->name ?? (optional($student)->stream ?? 'N/A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted small">{{ __('Academic Year') }}</span>
                        <span
                            class="fw-semibold">{{ optional(optional(optional($student)->currentEnrollment)->academicYear)->name ?? date('Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">{{ __('Enrolled Since') }}</span>
                        <span
                            class="fw-semibold">{{ $student && $student->created_at ? $student->created_at->format('M Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Info -->
        <div class="col-md-8">
            <!-- Personal Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>{{ __('Personal Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Full Name') }}</label>
                            <div class="border rounded p-2 bg-light">{{ $user->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Email Address') }}</label>
                            <div class="border rounded p-2 bg-light">{{ $user->email ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Phone Number') }}</label>
                            <div class="border rounded p-2 bg-light">{{ $user->phone ?? (optional($student)->phone ?? '—') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Date of Birth') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ optional($student)->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') : ($user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') : '—') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Gender') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ ucfirst(optional($student)->gender ?? ($user->gender ?? '—')) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Nationality') }}</label>
                            <div class="border rounded p-2 bg-light">{{ optional($student)->nationality ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted fw-semibold">{{ __('Address') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ optional($student)->address ?? ($user->address ?? '—') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book me-2"></i>{{ __('Academic Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Student Number') }}</label>
                            <div class="border rounded p-2 bg-light">{{ optional($student)->student_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Current Class') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ optional(optional($student)->currentClass)->name ?? (optional(optional($student)->classroom)->name ?? '—') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Stream') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ optional(optional($student)->stream)->name ?? (optional($student)->stream ?? '—') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Enrollment Date') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ $student && $student->created_at ? $student->created_at->format('M d, Y') : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Class Teacher') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ optional(optional(optional($student)->currentClass)->classTeacher)->name ?? (optional(optional(optional($student)->classroom)->teacher)->name ?? '—') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Status') }}</label>
                            <div class="border rounded p-2 bg-light">
                                @if (optional($student)->is_active ?? true)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>{{ __('Guardian Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Guardian Name') }}</label>
                            <div class="border rounded p-2 bg-light">{{ optional($student)->guardian_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Relationship') }}</label>
                            <div class="border rounded p-2 bg-light">
                                {{ ucfirst(optional($student)->guardian_relationship ?? '—') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Guardian Phone') }}</label>
                            <div class="border rounded p-2 bg-light">{{ optional($student)->guardian_phone ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Guardian Email') }}</label>
                            <div class="border rounded p-2 bg-light">{{ optional($student)->guardian_email ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Emergency Contact') }}</label>
                            <div class="border rounded p-2 bg-light">{{ $user->emergency_contact_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-semibold">{{ __('Emergency Phone') }}</label>
                            <div class="border rounded p-2 bg-light">{{ $user->emergency_contact_phone ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('tenant.student.profile.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editProfileModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>{{ __('Edit My Profile') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Personal Information Section -->
                        <h6 class="fw-semibold text-primary mb-3 border-bottom pb-2">
                            <i class="bi bi-person-lines-fill me-2"></i>{{ __('Personal Information') }}
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('Full Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}"
                                    disabled>
                                <small class="text-muted">{{ __('Contact admin to change email') }}</small>
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                    placeholder="+256...">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }}</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                    name="gender">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male"
                                        {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>
                                        {{ __('Male') }}</option>
                                    <option value="female"
                                        {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>
                                        {{ __('Female') }}</option>
                                    <option value="other"
                                        {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>
                                        {{ __('Other') }}</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nationality" class="form-label">{{ __('Nationality') }}</label>
                                <input type="text" class="form-control @error('nationality') is-invalid @enderror"
                                    id="nationality" name="nationality"
                                    value="{{ old('nationality', optional($student)->nationality) }}"
                                    placeholder="e.g., Ugandan">
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="religion" class="form-label">{{ __('Religion') }}</label>
                                <input type="text" class="form-control @error('religion') is-invalid @enderror"
                                    id="religion" name="religion"
                                    value="{{ old('religion', optional($student)->religion) }}"
                                    placeholder="e.g., Christian, Muslim">
                                @error('religion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">{{ __('Residential Address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2"
                                    placeholder="Enter your home address">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Guardian Information Section -->
                        <h6 class="fw-semibold text-info mb-3 border-bottom pb-2">
                            <i class="bi bi-people me-2"></i>{{ __('Guardian / Parent Information') }}
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="guardian_name" class="form-label">{{ __('Guardian Name') }}</label>
                                <input type="text" class="form-control @error('guardian_name') is-invalid @enderror"
                                    id="guardian_name" name="guardian_name"
                                    value="{{ old('guardian_name', optional($student)->guardian_name) }}"
                                    placeholder="Full name of guardian">
                                @error('guardian_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_relationship" class="form-label">{{ __('Relationship') }}</label>
                                <select class="form-select @error('guardian_relationship') is-invalid @enderror"
                                    id="guardian_relationship" name="guardian_relationship">
                                    <option value="">{{ __('Select Relationship') }}</option>
                                    <option value="father"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'father' ? 'selected' : '' }}>
                                        {{ __('Father') }}</option>
                                    <option value="mother"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'mother' ? 'selected' : '' }}>
                                        {{ __('Mother') }}</option>
                                    <option value="guardian"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'guardian' ? 'selected' : '' }}>
                                        {{ __('Guardian') }}</option>
                                    <option value="uncle"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'uncle' ? 'selected' : '' }}>
                                        {{ __('Uncle') }}</option>
                                    <option value="aunt"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'aunt' ? 'selected' : '' }}>
                                        {{ __('Aunt') }}</option>
                                    <option value="grandparent"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'grandparent' ? 'selected' : '' }}>
                                        {{ __('Grandparent') }}</option>
                                    <option value="sibling"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'sibling' ? 'selected' : '' }}>
                                        {{ __('Sibling') }}</option>
                                    <option value="other"
                                        {{ old('guardian_relationship', optional($student)->guardian_relationship) === 'other' ? 'selected' : '' }}>
                                        {{ __('Other') }}</option>
                                </select>
                                @error('guardian_relationship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_phone" class="form-label">{{ __('Guardian Phone') }}</label>
                                <input type="text" class="form-control @error('guardian_phone') is-invalid @enderror"
                                    id="guardian_phone" name="guardian_phone"
                                    value="{{ old('guardian_phone', optional($student)->guardian_phone) }}"
                                    placeholder="+256...">
                                @error('guardian_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_email" class="form-label">{{ __('Guardian Email') }}</label>
                                <input type="email" class="form-control @error('guardian_email') is-invalid @enderror"
                                    id="guardian_email" name="guardian_email"
                                    value="{{ old('guardian_email', optional($student)->guardian_email) }}"
                                    placeholder="guardian@email.com">
                                @error('guardian_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <h6 class="fw-semibold text-danger mb-3 border-bottom pb-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Emergency Contact') }}
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="emergency_contact_name"
                                    class="form-label">{{ __('Emergency Contact Name') }}</label>
                                <input type="text"
                                    class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                    id="emergency_contact_name" name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                                    placeholder="Person to contact in emergency">
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact_phone"
                                    class="form-label">{{ __('Emergency Contact Phone') }}</label>
                                <input type="text"
                                    class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                    id="emergency_contact_phone" name="emergency_contact_phone"
                                    value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                                    placeholder="+256...">
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Photo Section -->
                        <h6 class="fw-semibold text-success mb-3 border-bottom pb-2">
                            <i class="bi bi-camera me-2"></i>{{ __('Profile Photo') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="profile_photo" class="form-label">{{ __('Upload New Photo') }}</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror"
                                    id="profile_photo" name="profile_photo" accept="image/*">
                                <small class="text-muted">{{ __('Max 2MB. JPG, PNG, GIF formats accepted') }}</small>
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Current Photo') }}</label>
                                <div>
                                    @if ($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Current Photo"
                                            class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light border d-inline-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-person text-muted" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>{{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>{{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
