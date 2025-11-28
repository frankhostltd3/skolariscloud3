@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('tenant.modules.human-resource.employees.index') }}">{{ __('Employees') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Add New Employee') }}</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i>{{ __('Add New Employee') }}
                </h1>
            </div>
            <a href="{{ route('tenant.modules.human-resource.employees.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Employees') }}
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Validation Errors') }}
                </h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.modules.human-resource.employees.store') }}"
            enctype="multipart/form-data" id="employeeForm">
            @csrf
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">

                    <!-- Personal Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><i
                                    class="bi bi-person-vcard me-2"></i>{{ __('Personal Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">{{ __('First Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">{{ __('Last Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                        name="gender">
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>
                                            {{ __('Male') }}</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>
                                            {{ __('Female') }}</option>
                                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>
                                            {{ __('Other') }}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="birth_date" class="form-label">{{ __('Date of Birth') }}</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                                        max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="national_id" class="form-label">{{ __('National ID') }}</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                        id="national_id" name="national_id" value="{{ old('national_id') }}">
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="passport_photo" class="form-label">{{ __('Photo') }}</label>
                                    <input type="file" class="form-control @error('passport_photo') is-invalid @enderror"
                                        id="passport_photo" name="passport_photo" accept="image/*">
                                    <small class="text-muted">{{ __('Max 2MB. JPG, PNG, GIF') }}</small>
                                    @error('passport_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0"><i class="bi bi-envelope me-2"></i>{{ __('Contact Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password"
                                        placeholder="{{ __('Leave blank for default') }}">
                                    <small class="text-muted">{{ __('Default: Welcome123!') }}</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                        id="city" name="city" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="state" class="form-label">{{ __('State/Province') }}</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                        id="state" name="state" value="{{ old('state') }}">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0"><i
                                    class="bi bi-briefcase me-2"></i>{{ __('Professional Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info small">
                                <i
                                    class="bi bi-info-circle me-2"></i>{{ __('Employee number will be auto-generated based on department') }}
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label">{{ __('Department') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror"
                                        id="department_id" name="department_id" required>
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="position_id" class="form-label">{{ __('Position') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('position_id') is-invalid @enderror"
                                        id="position_id" name="position_id" required>
                                        <option value="">{{ __('Select Position') }}</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                                {{ $position->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="employee_type" class="form-label">{{ __('Employment Type') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_type') is-invalid @enderror"
                                        id="employee_type" name="employee_type" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="full_time"
                                            {{ old('employee_type') === 'full_time' ? 'selected' : '' }}>
                                            {{ __('Full Time') }}</option>
                                        <option value="part_time"
                                            {{ old('employee_type') === 'part_time' ? 'selected' : '' }}>
                                            {{ __('Part Time') }}</option>
                                        <option value="intern" {{ old('employee_type') === 'intern' ? 'selected' : '' }}>
                                            {{ __('Intern') }}</option>
                                        <option value="contract"
                                            {{ old('employee_type') === 'contract' ? 'selected' : '' }}>
                                            {{ __('Contract') }}
                                        </option>
                                    </select>
                                    @error('employee_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="salary_scale_id" class="form-label">{{ __('Salary Scale') }}</label>
                                    <select class="form-select @error('salary_scale_id') is-invalid @enderror"
                                        id="salary_scale_id" name="salary_scale_id">
                                        <option value="">{{ __('Select Salary Scale') }}</option>
                                        @foreach ($salary_scales as $scale)
                                            <option value="{{ $scale->id }}"
                                                {{ old('salary_scale_id') == $scale->id ? 'selected' : '' }}>
                                                {{ $scale->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('salary_scale_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="hire_date" class="form-label">{{ __('Hire Date') }}</label>
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                        id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}">
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="card-title mb-0"><i
                                    class="bi bi-telephone-fill me-2"></i>{{ __('Emergency Contact') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="emergency_contact_name"
                                        class="form-label">{{ __('Contact Name') }}</label>
                                    <input type="text"
                                        class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                        id="emergency_contact_name" name="emergency_contact_name"
                                        value="{{ old('emergency_contact_name') }}">
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_contact_phone"
                                        class="form-label">{{ __('Contact Phone') }}</label>
                                    <input type="text"
                                        class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                        id="emergency_contact_phone" name="emergency_contact_phone"
                                        value="{{ old('emergency_contact_phone') }}">
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_contact_relation"
                                        class="form-label">{{ __('Relationship') }}</label>
                                    <input type="text"
                                        class="form-control @error('emergency_contact_relation') is-invalid @enderror"
                                        id="emergency_contact_relation" name="emergency_contact_relation"
                                        value="{{ old('emergency_contact_relation') }}">
                                    @error('emergency_contact_relation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i
                                    class="bi bi-file-text me-2"></i>{{ __('Additional Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="medical_conditions"
                                        class="form-label">{{ __('Medical Conditions') }}</label>
                                    <textarea class="form-control @error('medical_conditions') is-invalid @enderror" id="medical_conditions"
                                        name="medical_conditions" rows="2">{{ old('medical_conditions') }}</textarea>
                                    @error('medical_conditions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">

                    <!-- Status Card -->
                    <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i class="bi bi-gear me-2"></i>{{ __('Employment Status') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="employment_status" class="form-label">{{ __('Status') }}</label>
                                <select class="form-select @error('employment_status') is-invalid @enderror"
                                    id="employment_status" name="employment_status">
                                    <option value="active"
                                        {{ old('employment_status', 'active') === 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="probation"
                                        {{ old('employment_status') === 'probation' ? 'selected' : '' }}>
                                        {{ __('Probation') }}</option>
                                    <option value="on_leave"
                                        {{ old('employment_status') === 'on_leave' ? 'selected' : '' }}>
                                        {{ __('On Leave') }}</option>
                                    <option value="inactive"
                                        {{ old('employment_status') === 'inactive' ? 'selected' : '' }}>
                                        {{ __('Inactive') }}</option>
                                    <option value="suspended"
                                        {{ old('employment_status') === 'suspended' ? 'selected' : '' }}>
                                        {{ __('Suspended') }}</option>
                                    <option value="terminated"
                                        {{ old('employment_status') === 'terminated' ? 'selected' : '' }}>
                                        {{ __('Terminated') }}</option>
                                </select>
                                @error('employment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>{{ __('Save Employee') }}
                                </button>
                                <button type="button" class="btn btn-primary btn-lg d-none" id="loadingBtn" disabled>
                                    <span class="spinner-border spinner-border-sm me-2"></span>{{ __('Saving...') }}
                                </button>
                                <a href="{{ route('tenant.modules.human-resource.employees.index') }}"
                                    class="btn btn-outline-secondary">
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
                                <li class="mb-2"><i
                                        class="bi bi-check-circle text-success me-2"></i>{{ __('Required fields are marked with *') }}
                                </li>
                                <li class="mb-2"><i
                                        class="bi bi-check-circle text-success me-2"></i>{{ __('Employee number is auto-generated') }}
                                </li>
                                <li class="mb-2"><i
                                        class="bi bi-check-circle text-success me-2"></i>{{ __('Photo max size: 2MB') }}
                                </li>
                                <li class="mb-2"><i
                                        class="bi bi-check-circle text-success me-2"></i>{{ __('Department determines employee number prefix') }}
                                </li>
                                <li class="mb-0"><i
                                        class="bi bi-check-circle text-success me-2"></i>{{ __('All data is securely stored') }}
                                </li>
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
            const form = document.getElementById('employeeForm');
            const submitBtn = document.getElementById('submitBtn');
            const loadingBtn = document.getElementById('loadingBtn');

            form.addEventListener('submit', function() {
                submitBtn.classList.add('d-none');
                loadingBtn.classList.remove('d-none');
            });

            // Photo validation
            const photoInput = document.getElementById('passport_photo');
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.size / 1024 / 1024 > 2) {
                        alert('{{ __('Photo must be less than 2MB') }}');
                        e.target.value = '';
                    }
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

        .sticky-top {
            z-index: 1020;
        }
    </style>
@endpush
