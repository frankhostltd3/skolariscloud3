@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>{{ __('Edit Parent/Guardian') }}
                </h1>
                <p class="text-muted mb-0">{{ __('Update parent/guardian information') }}</p>
            </div>
            <a href="{{ route('tenant.users.parents.show', $user) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>{{ __('Back') }}
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading"><i
                        class="bi bi-exclamation-triangle me-2"></i>{{ __('Please correct the following errors:') }}</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('tenant.users.parents.update', $user) }}" method="POST" enctype="multipart/form-data"
            id="parentForm">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Content -->
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
                                <div class="col-md-12">
                                    <label for="email" class="form-label">{{ __('Email') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}"
                                        placeholder="parent@example.com" required>
                                    <small class="text-muted">{{ __('Used for system login') }}</small>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        {{ __('Leave password fields empty to keep current password') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('New Password') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    <small class="text-muted">{{ __('Minimum 8 characters') }}</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label">{{ __('Confirm New Password') }}</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>
                        </div>
                    </div>

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
                                    <label for="first_name" class="form-label">{{ __('First Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="first_name" name="first_name"
                                        value="{{ old('first_name', $profile->first_name ?? '') }}" placeholder="John"
                                        required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="middle_name" class="form-label">{{ __('Middle Name') }}</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                        id="middle_name" name="middle_name"
                                        value="{{ old('middle_name', $profile->middle_name ?? '') }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="last_name" class="form-label">{{ __('Last Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="last_name" name="last_name"
                                        value="{{ old('last_name', $profile->last_name ?? '') }}" placeholder="Doe"
                                        required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                        name="gender">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="male"
                                            {{ old('gender', $profile->gender ?? '') == 'male' ? 'selected' : '' }}>
                                            {{ __('Male') }}</option>
                                        <option value="female"
                                            {{ old('gender', $profile->gender ?? '') == 'female' ? 'selected' : '' }}>
                                            {{ __('Female') }}</option>
                                        <option value="other"
                                            {{ old('gender', $profile->gender ?? '') == 'other' ? 'selected' : '' }}>
                                            {{ __('Other') }}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }}</label>
                                    <input type="date"
                                        class="form-control @error('date_of_birth') is-invalid @enderror"
                                        id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d') ?? '') }}"
                                        max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="national_id" class="form-label">{{ __('National ID') }}</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                        id="national_id" name="national_id"
                                        value="{{ old('national_id', $profile->national_id ?? '') }}"
                                        placeholder="12345678">
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="blood_group" class="form-label">{{ __('Blood Group') }}</label>
                                    <select class="form-select @error('blood_group') is-invalid @enderror"
                                        id="blood_group" name="blood_group">
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $type)
                                            <option value="{{ $type }}"
                                                {{ old('blood_group', $profile->blood_group ?? '') == $type ? 'selected' : '' }}>
                                                {{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('blood_group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="profile_photo" class="form-label">{{ __('Profile Photo') }}</label>
                                    <input type="file"
                                        class="form-control @error('profile_photo') is-invalid @enderror"
                                        id="profile_photo" name="profile_photo" accept="image/*">
                                    <small
                                        class="text-muted">{{ __('Max 2MB. Leave empty to keep current photo.') }}</small>
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($profile?->profile_photo)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>{{ __('Current photo exists') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
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
                                    <label for="phone" class="form-label">{{ __('Phone') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $profile->phone ?? '') }}"
                                        placeholder="+254 700 000000" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="alternate_phone" class="form-label">{{ __('Alternate Phone') }}</label>
                                    <input type="tel"
                                        class="form-control @error('alternate_phone') is-invalid @enderror"
                                        id="alternate_phone" name="alternate_phone"
                                        value="{{ old('alternate_phone', $profile->alternate_phone ?? '') }}"
                                        placeholder="+254 722 000000">
                                    @error('alternate_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">{{ __('Home Address') }}</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2"
                                        placeholder="123 Main Street">{{ old('address', $profile->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                        id="city" name="city" value="{{ old('city', $profile->city ?? '') }}"
                                        placeholder="Nairobi">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="state" class="form-label">{{ __('County') }}</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                        id="state" name="state" value="{{ old('state', $profile->state ?? '') }}"
                                        placeholder="Nairobi">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label">{{ __('Postal Code') }}</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                        id="postal_code" name="postal_code"
                                        value="{{ old('postal_code', $profile->postal_code ?? '') }}"
                                        placeholder="00100">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="country" class="form-label">{{ __('Country') }}</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                                        id="country" name="country"
                                        value="{{ old('country', $profile->country ?? 'Kenya') }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Occupation Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-briefcase me-2"></i>{{ __('Occupation Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="occupation" class="form-label">{{ __('Occupation/Profession') }}</label>
                                    <input type="text" class="form-control @error('occupation') is-invalid @enderror"
                                        id="occupation" name="occupation"
                                        value="{{ old('occupation', $profile->occupation ?? '') }}"
                                        placeholder="Software Engineer">
                                    @error('occupation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="employer" class="form-label">{{ __('Employer/Company') }}</label>
                                    <input type="text" class="form-control @error('employer') is-invalid @enderror"
                                        id="employer" name="employer"
                                        value="{{ old('employer', $profile->employer ?? '') }}"
                                        placeholder="Tech Corp Ltd">
                                    @error('employer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="work_phone" class="form-label">{{ __('Work Phone') }}</label>
                                    <input type="tel" class="form-control @error('work_phone') is-invalid @enderror"
                                        id="work_phone" name="work_phone"
                                        value="{{ old('work_phone', $profile->work_phone ?? '') }}"
                                        placeholder="+254 20 000000">
                                    @error('work_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="annual_income"
                                        class="form-label">{{ __('Annual Income (Optional)') }}</label>
                                    <input type="number"
                                        class="form-control @error('annual_income') is-invalid @enderror"
                                        id="annual_income" name="annual_income"
                                        value="{{ old('annual_income', $profile->annual_income ?? '') }}"
                                        placeholder="1000000" min="0" step="0.01">
                                    @error('annual_income')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="work_address" class="form-label">{{ __('Work Address') }}</label>
                                    <textarea class="form-control @error('work_address') is-invalid @enderror" id="work_address" name="work_address"
                                        rows="2">{{ old('work_address', $profile->work_address ?? '') }}</textarea>
                                    @error('work_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Children/Students -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>{{ __('Children/Students') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="children-container">
                                @php
                                    $existingStudents = old(
                                        'students',
                                        $profile?->students->pluck('id')->toArray() ?? [],
                                    );
                                    $existingRelationships = old(
                                        'relationships',
                                        $profile?->students->pluck('pivot.relationship')->toArray() ?? [],
                                    );
                                @endphp

                                @if (count($existingStudents) > 0)
                                    @foreach ($existingStudents as $index => $studentId)
                                        <div class="child-entry mb-3 p-3 border rounded">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label">{{ __('Student') }}</label>
                                                    <select class="form-select" name="students[]">
                                                        <option value="">{{ __('Select Student') }}</option>
                                                        @foreach ($students as $student)
                                                            <option value="{{ $student->id }}"
                                                                {{ $studentId == $student->id ? 'selected' : '' }}>
                                                                {{ $student->full_name }} ({{ $student->admission_no }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">{{ __('Relationship') }}</label>
                                                    <select class="form-select" name="relationships[]">
                                                        <option value="father"
                                                            {{ ($existingRelationships[$index] ?? '') == 'father' ? 'selected' : '' }}>
                                                            {{ __('Father') }}</option>
                                                        <option value="mother"
                                                            {{ ($existingRelationships[$index] ?? '') == 'mother' ? 'selected' : '' }}>
                                                            {{ __('Mother') }}</option>
                                                        <option value="guardian"
                                                            {{ ($existingRelationships[$index] ?? 'guardian') == 'guardian' ? 'selected' : '' }}>
                                                            {{ __('Guardian') }}</option>
                                                        <option value="relative"
                                                            {{ ($existingRelationships[$index] ?? '') == 'relative' ? 'selected' : '' }}>
                                                            {{ __('Relative') }}</option>
                                                        <option value="other"
                                                            {{ ($existingRelationships[$index] ?? '') == 'other' ? 'selected' : '' }}>
                                                            {{ __('Other') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @if ($index > 0)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger mt-2 remove-child">
                                                    <i class="bi bi-trash me-1"></i>{{ __('Remove') }}
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="child-entry mb-3 p-3 border rounded">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label">{{ __('Student') }}</label>
                                                <select class="form-select" name="students[]">
                                                    <option value="">{{ __('Select Student') }}</option>
                                                    @foreach ($students as $student)
                                                        <option value="{{ $student->id }}">{{ $student->full_name }}
                                                            ({{ $student->admission_no }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">{{ __('Relationship') }}</label>
                                                <select class="form-select" name="relationships[]">
                                                    <option value="father">{{ __('Father') }}</option>
                                                    <option value="mother">{{ __('Mother') }}</option>
                                                    <option value="guardian" selected>{{ __('Guardian') }}</option>
                                                    <option value="relative">{{ __('Relative') }}</option>
                                                    <option value="other">{{ __('Other') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-child">
                                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Another Child') }}
                            </button>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-phone-vibrate me-2"></i>{{ __('Emergency Contact') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="emergency_contact_name" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" id="emergency_contact_name"
                                        name="emergency_contact_name"
                                        value="{{ old('emergency_contact_name', $profile->emergency_contact_name ?? '') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="emergency_contact_phone" class="form-label">{{ __('Phone') }}</label>
                                    <input type="tel" class="form-control" id="emergency_contact_phone"
                                        name="emergency_contact_phone"
                                        value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone ?? '') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="emergency_contact_relation"
                                        class="form-label">{{ __('Relationship') }}</label>
                                    <input type="text" class="form-control" id="emergency_contact_relation"
                                        name="emergency_contact_relation"
                                        value="{{ old('emergency_contact_relation', $profile->emergency_contact_relation ?? '') }}"
                                        placeholder="Sibling, Friend">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-file-text me-2"></i>{{ __('Additional Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="medical_conditions"
                                        class="form-label">{{ __('Medical Conditions') }}</label>
                                    <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="2">{{ old('medical_conditions', $profile->medical_conditions ?? '') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $profile->notes ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Current Photo -->
                    @if ($profile?->profile_photo)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-image me-2"></i>{{ __('Current Photo') }}
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ asset('storage/' . $profile->profile_photo) }}"
                                    alt="{{ $profile->full_name }}" class="img-fluid rounded mb-2"
                                    style="max-height: 200px;">
                                <p class="small text-muted mb-0">{{ __('Upload a new photo to replace this') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Status Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-toggle-on me-2"></i>{{ __('Status') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('Profile Status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active"
                                        {{ old('status', $profile->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="inactive"
                                        {{ old('status', $profile->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                        {{ __('Inactive') }}</option>
                                    <option value="deceased"
                                        {{ old('status', $profile->status ?? '') == 'deceased' ? 'selected' : '' }}>
                                        {{ __('Deceased') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="card shadow-sm bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-lightbulb text-warning me-2"></i>{{ __('Quick Tips') }}
                            </h6>
                            <ul class="small mb-0">
                                <li>{{ __('Fields marked with * are required') }}</li>
                                <li>{{ __('Leave password empty to keep current') }}</li>
                                <li>{{ __('Leave photo empty to keep current image') }}</li>
                                <li>{{ __('Update student relationships as needed') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tenant.users.parents.show', $user) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm d-none me-2" id="spinner"></span>
                                    <i class="bi bi-check-circle me-2" id="icon"></i>
                                    <span id="text">{{ __('Update Parent/Guardian') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.getElementById('parentForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                const spinner = document.getElementById('spinner');
                const icon = document.getElementById('icon');
                const text = document.getElementById('text');

                btn.disabled = true;
                spinner.classList.remove('d-none');
                icon.classList.add('d-none');
                text.textContent = '{{ __('Updating...') }}';
            });

            // File size validation
            document.getElementById('profile_photo')?.addEventListener('change', function(e) {
                if (e.target.files[0] && e.target.files[0].size > 2 * 1024 * 1024) {
                    alert('{{ __('File size must be less than 2MB') }}');
                    this.value = '';
                }
            });

            // Add child functionality
            document.getElementById('add-child').addEventListener('click', function() {
                const container = document.getElementById('children-container');
                const firstEntry = container.querySelector('.child-entry');
                const template = firstEntry.cloneNode(true);

                // Reset selections
                template.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });

                // Remove existing remove button if any
                const existingRemoveBtn = template.querySelector('.remove-child');
                if (existingRemoveBtn) {
                    existingRemoveBtn.remove();
                }

                // Add remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-outline-danger mt-2 remove-child';
                removeBtn.innerHTML = '<i class="bi bi-trash me-1"></i>{{ __('Remove') }}';
                removeBtn.addEventListener('click', function() {
                    template.remove();
                });
                template.querySelector('.row').after(removeBtn);

                container.appendChild(template);
            });

            // Remove child functionality for existing entries
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-child')) {
                    e.target.closest('.child-entry').remove();
                }
            });
        </script>
    @endpush
@endsection
