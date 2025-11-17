@extends('tenant.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant.modules.students.index') }}">{{ __('Students') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Add New Student') }}</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-0">
                <i class="bi bi-person-plus-fill me-2"></i>{{ __('Add New Student') }}
            </h1>
        </div>
        <a href="{{ route('tenant.modules.students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Students') }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Validation Errors') }}</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.modules.students.store') }}" method="POST" enctype="multipart/form-data" id="studentForm">
        @csrf
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                
                <!-- Personal Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-person-vcard me-2"></i>{{ __('Personal Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" 
                                    class="form-control @error('first_name') is-invalid @enderror" 
                                    placeholder="John" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" 
                                    class="form-control @error('last_name') is-invalid @enderror" 
                                    placeholder="Doe" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Gender') }} <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                                    class="form-control @error('date_of_birth') is-invalid @enderror" 
                                    max="{{ date('Y-m-d') }}" required>
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('National ID / Birth Certificate No.') }}</label>
                                <input type="text" name="national_id" value="{{ old('national_id') }}" 
                                    class="form-control @error('national_id') is-invalid @enderror" 
                                    placeholder="123456789">
                                @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Blood Group') }}</label>
                                <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                    <option value="">{{ __('Select Blood Group') }}</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                                @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Phone Number') }}</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    placeholder="+1234567890">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Profile Photo') }}</label>
                                <input type="file" name="profile_photo" 
                                    class="form-control @error('profile_photo') is-invalid @enderror" 
                                    accept="image/*" id="profilePhoto">
                                <small class="text-muted">{{ __('Max 2MB. JPG, PNG, GIF') }}</small>
                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-book me-2"></i>{{ __('Academic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Admission Number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="admission_no" value="{{ old('admission_no') }}" 
                                    class="form-control @error('admission_no') is-invalid @enderror" 
                                    placeholder="ADM-2024-001" required>
                                @error('admission_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Class') }} <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Select Class') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Roll Number') }}</label>
                                <input type="text" name="roll_number" value="{{ old('roll_number') }}" 
                                    class="form-control @error('roll_number') is-invalid @enderror" 
                                    placeholder="001">
                                @error('roll_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Section') }}</label>
                                <input type="text" name="section" value="{{ old('section') }}" 
                                    class="form-control @error('section') is-invalid @enderror" 
                                    placeholder="A">
                                @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Admission Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" 
                                    class="form-control @error('admission_date') is-invalid @enderror" required>
                                @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    placeholder="student@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-house me-2"></i>{{ __('Contact Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Address') }}</label>
                                <textarea name="address" rows="2" 
                                    class="form-control @error('address') is-invalid @enderror" 
                                    placeholder="Street address">{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('City') }}</label>
                                <input type="text" name="city" value="{{ old('city') }}" 
                                    class="form-control @error('city') is-invalid @enderror" 
                                    placeholder="City name">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('State / Province') }}</label>
                                <input type="text" name="state" value="{{ old('state') }}" 
                                    class="form-control @error('state') is-invalid @enderror" 
                                    placeholder="State or province">
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Postal Code') }}</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" 
                                    class="form-control @error('postal_code') is-invalid @enderror" 
                                    placeholder="12345">
                                @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Country') }}</label>
                                <input type="text" name="country" value="{{ old('country') }}" 
                                    class="form-control @error('country') is-invalid @enderror" 
                                    placeholder="Country name">
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>{{ __('Guardian Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#father-tab" type="button">
                                    <i class="bi bi-person me-1"></i>{{ __('Father') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mother-tab" type="button">
                                    <i class="bi bi-person me-1"></i>{{ __('Mother') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardian-tab" type="button">
                                    <i class="bi bi-person-badge me-1"></i>{{ __('Guardian') }}
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- Father Tab -->
                            <div class="tab-pane fade show active" id="father-tab">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Father's Name") }}</label>
                                        <input type="text" name="father_name" value="{{ old('father_name') }}" 
                                            class="form-control @error('father_name') is-invalid @enderror">
                                        @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Father's Phone") }}</label>
                                        <input type="tel" name="father_phone" value="{{ old('father_phone') }}" 
                                            class="form-control @error('father_phone') is-invalid @enderror">
                                        @error('father_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Father's Occupation") }}</label>
                                        <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" 
                                            class="form-control @error('father_occupation') is-invalid @enderror">
                                        @error('father_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Father's Email") }}</label>
                                        <input type="email" name="father_email" value="{{ old('father_email') }}" 
                                            class="form-control @error('father_email') is-invalid @enderror">
                                        @error('father_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Mother Tab -->
                            <div class="tab-pane fade" id="mother-tab">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Mother's Name") }}</label>
                                        <input type="text" name="mother_name" value="{{ old('mother_name') }}" 
                                            class="form-control @error('mother_name') is-invalid @enderror">
                                        @error('mother_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Mother's Phone") }}</label>
                                        <input type="tel" name="mother_phone" value="{{ old('mother_phone') }}" 
                                            class="form-control @error('mother_phone') is-invalid @enderror">
                                        @error('mother_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Mother's Occupation") }}</label>
                                        <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}" 
                                            class="form-control @error('mother_occupation') is-invalid @enderror">
                                        @error('mother_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Mother's Email") }}</label>
                                        <input type="email" name="mother_email" value="{{ old('mother_email') }}" 
                                            class="form-control @error('mother_email') is-invalid @enderror">
                                        @error('mother_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Guardian Tab -->
                            <div class="tab-pane fade" id="guardian-tab">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Guardian's Name") }}</label>
                                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" 
                                            class="form-control @error('guardian_name') is-invalid @enderror">
                                        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Guardian's Phone") }}</label>
                                        <input type="tel" name="guardian_phone" value="{{ old('guardian_phone') }}" 
                                            class="form-control @error('guardian_phone') is-invalid @enderror">
                                        @error('guardian_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Guardian's Relationship") }}</label>
                                        <input type="text" name="guardian_relation" value="{{ old('guardian_relation') }}" 
                                            class="form-control @error('guardian_relation') is-invalid @enderror" 
                                            placeholder="Uncle, Aunt, Grandparent, etc.">
                                        @error('guardian_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __("Guardian's Email") }}</label>
                                        <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" 
                                            class="form-control @error('guardian_email') is-invalid @enderror">
                                        @error('guardian_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-telephone-fill me-2"></i>{{ __('Emergency Contact') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Contact Name') }}</label>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" 
                                    class="form-control @error('emergency_contact_name') is-invalid @enderror">
                                @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Contact Phone') }}</label>
                                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" 
                                    class="form-control @error('emergency_contact_phone') is-invalid @enderror">
                                @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Relationship') }}</label>
                                <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}" 
                                    class="form-control @error('emergency_contact_relation') is-invalid @enderror">
                                @error('emergency_contact_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-heart-pulse me-2"></i>{{ __('Medical Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Medical Conditions') }}</label>
                                <textarea name="medical_conditions" rows="2" 
                                    class="form-control @error('medical_conditions') is-invalid @enderror" 
                                    placeholder="Any existing medical conditions">{{ old('medical_conditions') }}</textarea>
                                @error('medical_conditions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Allergies') }}</label>
                                <textarea name="allergies" rows="2" 
                                    class="form-control @error('allergies') is-invalid @enderror" 
                                    placeholder="Food, drug, or other allergies">{{ old('allergies') }}</textarea>
                                @error('allergies')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Current Medications') }}</label>
                                <textarea name="medications" rows="2" 
                                    class="form-control @error('medications') is-invalid @enderror" 
                                    placeholder="List any medications currently taken">{{ old('medications') }}</textarea>
                                @error('medications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Previous School -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-building me-2"></i>{{ __('Previous School Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Previous School Name') }}</label>
                                <input type="text" name="previous_school" value="{{ old('previous_school') }}" 
                                    class="form-control @error('previous_school') is-invalid @enderror">
                                @error('previous_school')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Previous Class/Grade') }}</label>
                                <input type="text" name="previous_class" value="{{ old('previous_class') }}" 
                                    class="form-control @error('previous_class') is-invalid @enderror">
                                @error('previous_class')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Transfer Reason') }}</label>
                                <textarea name="transfer_reason" rows="2" 
                                    class="form-control @error('transfer_reason') is-invalid @enderror" 
                                    placeholder="Reason for transferring from previous school">{{ old('transfer_reason') }}</textarea>
                                @error('transfer_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Needs -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header" style="background-color: #6f42c1; color: white;">
                        <h5 class="card-title mb-0"><i class="bi bi-universal-access me-2"></i>{{ __('Special Needs & Support') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="has_special_needs" value="1" 
                                        class="form-check-input" id="hasSpecialNeeds"
                                        {{ old('has_special_needs') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="hasSpecialNeeds">
                                        {{ __('Student has special needs or requires additional support') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-12" id="specialNeedsDescription" style="display: {{ old('has_special_needs') ? 'block' : 'none' }};">
                                <label class="form-label">{{ __('Special Needs Description') }}</label>
                                <textarea name="special_needs_description" rows="3" 
                                    class="form-control @error('special_needs_description') is-invalid @enderror" 
                                    placeholder="Please describe the special needs and any support requirements">{{ old('special_needs_description') }}</textarea>
                                @error('special_needs_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-file-text me-2"></i>{{ __('Additional Notes') }}</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" rows="4" 
                            class="form-control @error('notes') is-invalid @enderror" 
                            placeholder="Any additional information about the student">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                
                <!-- Status Card -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-gear me-2"></i>{{ __('Student Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    {{ __('Active') }}
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}
                                </option>
                                <option value="transferred" {{ old('status') == 'transferred' ? 'selected' : '' }}>
                                    {{ __('Transferred') }}
                                </option>
                                <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>
                                    {{ __('Graduated') }}
                                </option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
                                    {{ __('Suspended') }}
                                </option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>{{ __('Save Student') }}
                            </button>
                            <button type="button" class="btn btn-primary btn-lg d-none" id="loadingBtn" disabled>
                                <span class="spinner-border spinner-border-sm me-2"></span>{{ __('Saving...') }}
                            </button>
                            <a href="{{ route('tenant.modules.students.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-lightbulb me-2"></i>{{ __('Quick Tips') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Required fields are marked with *') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Admission number must be unique') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Profile photo max size: 2MB') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Provide at least one guardian contact') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Emergency contact is recommended') }}</li>
                            <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>{{ __('All data is securely stored') }}</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission loading state
    const form = document.getElementById('studentForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingBtn = document.getElementById('loadingBtn');
    
    form.addEventListener('submit', function() {
        submitBtn.classList.add('d-none');
        loadingBtn.classList.remove('d-none');
    });

    // Profile photo validation
    const profilePhoto = document.getElementById('profilePhoto');
    if (profilePhoto) {
        profilePhoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    alert('{{ __("Profile photo must be less than 2MB") }}');
                    e.target.value = '';
                }
            }
        });
    }

    // Special needs toggle
    const hasSpecialNeeds = document.getElementById('hasSpecialNeeds');
    const specialNeedsDescription = document.getElementById('specialNeedsDescription');
    
    if (hasSpecialNeeds) {
        hasSpecialNeeds.addEventListener('change', function() {
            specialNeedsDescription.style.display = this.checked ? 'block' : 'none';
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.card-header {
    font-weight: 600;
}
.nav-tabs .nav-link {
    color: #495057;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}
.sticky-top {
    z-index: 1020;
}
</style>
@endpush
