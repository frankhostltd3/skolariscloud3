@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'My Profile')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">{{ __('My Profile') }}</h1>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
        <i class="bi bi-pencil-square me-1"></i>{{ __('Edit Profile') }}
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
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
                        @php $profileAvatar = $user->profile_photo_url; @endphp
                        @if($profileAvatar)
                            <img src="{{ $profileAvatar }}" alt="Profile Photo" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <span class="text-primary fw-bold" style="font-size: 3rem;">{{ strtoupper(substr($user->name ?? 'T', 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <h4 class="fw-semibold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                </p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary">
                        <i class="bi bi-person-badge me-1"></i>Teacher
                    </span>
                    @if($user->hasRole('Admin'))
                        <span class="badge bg-warning-subtle text-warning border border-warning">
                            <i class="bi bi-shield-check me-1"></i>Admin
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
                    <span class="text-muted small">{{ __('Classes') }}</span>
                    <span class="fw-semibold">{{ $user->classes_count ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="text-muted small">{{ __('Students') }}</span>
                    <span class="fw-semibold">{{ $user->students_count ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">{{ __('Member Since') }}</span>
                    <span class="fw-semibold">{{ $user->created_at ? $user->created_at->format('Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-person-lines-fill me-2"></i>{{ __('Personal Information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Full Name') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->name ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Email Address') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->email ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Phone Number') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Date of Birth') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') : 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Gender') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->gender ? ucfirst($user->gender) : 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Address') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->address ?? 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-briefcase me-2"></i>{{ __('Professional Information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Employee ID') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->employee_id ?? 'Not assigned' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Qualification') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->qualification ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Specialization') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->specialization ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Joining Date') }}</label>
                        <div class="border rounded p-2 bg-light">{{ $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('M d, Y') : 'Not set' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-semibold">{{ __('Status') }}</label>
                        <div class="border rounded p-2 bg-light">
                            @if($user->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($user->status ?? 'Unknown') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tenant.teacher.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>{{ __('Edit Profile') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }}</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">{{ __('Gender') }}</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">{{ __('Select Gender') }}</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="address" class="form-label">{{ __('Address') }}</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="qualification" class="form-label">{{ __('Qualification') }}</label>
                            <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" value="{{ old('qualification', $user->qualification) }}">
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="specialization" class="form-label">{{ __('Specialization') }}</label>
                            <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization', $user->specialization) }}">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="profile_photo" class="form-label">{{ __('Profile Photo') }}</label>
                            <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                            <small class="text-muted">{{ __('Max 2MB. JPG, PNG, GIF') }}</small>
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>{{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

