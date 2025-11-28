@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.modules.teachers._sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-10 col-md-9">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-person-plus me-2 text-primary"></i>{{ __('Add New Teacher') }}
                        </h1>
                        <p class="text-muted mb-0">{{ __('Complete the form below to add a new teacher') }}</p>
                    </div>
                    <a href="{{ route('tenant.modules.teachers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('Back') }}
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i
                                class="bi bi-exclamation-triangle me-2"></i>{{ __('Please correct the following errors:') }}
                        </h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('tenant.modules.teachers.store') }}" method="POST" enctype="multipart/form-data"
                    id="teacherForm">
                    @csrf

                    <div class="row">
                        <!-- Main Content -->
                        <div class="col-lg-8">
                            <!-- Personal Information -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-person me-2"></i>{{ __('Personal Information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="first_name" class="form-label">{{ __('First Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                id="first_name" name="first_name" value="{{ old('first_name') }}"
                                                placeholder="Jane" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">{{ __('Last Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                                name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                            <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                                name="gender">
                                                <option value="">{{ __('Select') }}</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                                    {{ __('Male') }}</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                                    {{ __('Female') }}</option>
                                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>
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
                                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                                max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                            @error('date_of_birth')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="national_id" class="form-label">{{ __('National ID') }}</label>
                                            <input type="text"
                                                class="form-control @error('national_id') is-invalid @enderror"
                                                id="national_id" name="national_id" value="{{ old('national_id') }}"
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
                                                        {{ old('blood_group') == $type ? 'selected' : '' }}>
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
                                            <small class="text-muted">{{ __('Max 2MB') }}</small>
                                            @error('profile_photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-envelope me-2"></i>{{ __('Contact Information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">{{ __('Email') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                name="email" value="{{ old('email') }}"
                                                placeholder="jane@example.com" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password"
                                                placeholder="{{ __('Leave blank for default') }}">
                                            <small class="text-muted">{{ __('Default: Teacher@123') }}</small>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">{{ __('Phone') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel"
                                                class="form-control @error('phone') is-invalid @enderror" id="phone"
                                                name="phone" value="{{ old('phone') }}" placeholder="+254 700 000000"
                                                required>
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

                                        <div class="col-md-4">
                                            <label for="city" class="form-label">{{ __('City') }}</label>
                                            <input type="text"
                                                class="form-control @error('city') is-invalid @enderror" id="city"
                                                name="city" value="{{ old('city') }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="state" class="form-label">{{ __('County') }}</label>
                                            <input type="text"
                                                class="form-control @error('state') is-invalid @enderror" id="state"
                                                name="state" value="{{ old('state') }}">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="country" class="form-label">{{ __('Country') }}</label>
                                            <input type="text"
                                                class="form-control @error('country') is-invalid @enderror"
                                                id="country" name="country" value="{{ old('country', 'Kenya') }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-briefcase me-2"></i>{{ __('Professional Information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="qualification"
                                                class="form-label">{{ __('Qualification') }}</label>
                                            <select class="form-select @error('qualification') is-invalid @enderror"
                                                id="qualification" name="qualification">
                                                <option value="">{{ __('Select') }}</option>
                                                @foreach (['PhD', 'Masters', 'Bachelors', 'Diploma', 'Certificate'] as $qual)
                                                    <option value="{{ $qual }}"
                                                        {{ old('qualification') == $qual ? 'selected' : '' }}>
                                                        {{ $qual }}</option>
                                                @endforeach
                                            </select>
                                            @error('qualification')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="specialization"
                                                class="form-label">{{ __('Specialization') }}</label>
                                            <input type="text"
                                                class="form-control @error('specialization') is-invalid @enderror"
                                                id="specialization" name="specialization"
                                                value="{{ old('specialization') }}" placeholder="Mathematics">
                                            @error('specialization')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="experience_years"
                                                class="form-label">{{ __('Experience (Years)') }}</label>
                                            <input type="number"
                                                class="form-control @error('experience_years') is-invalid @enderror"
                                                id="experience_years" name="experience_years"
                                                value="{{ old('experience_years') }}" min="0" max="50">
                                            @error('experience_years')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="joining_date" class="form-label">{{ __('Joining Date') }}</label>
                                            <input type="date"
                                                class="form-control @error('joining_date') is-invalid @enderror"
                                                id="joining_date" name="joining_date"
                                                value="{{ old('joining_date', date('Y-m-d')) }}">
                                            @error('joining_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="employment_type"
                                                class="form-label">{{ __('Employment Type') }}</label>
                                            <select class="form-select @error('employment_type') is-invalid @enderror"
                                                id="employment_type" name="employment_type">
                                                <option value="full_time" selected>{{ __('Full Time') }}</option>
                                                <option value="part_time">{{ __('Part Time') }}</option>
                                                <option value="contract">{{ __('Contract') }}</option>
                                                <option value="visiting">{{ __('Visiting') }}</option>
                                            </select>
                                            @error('employment_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-phone me-2"></i>{{ __('Emergency Contact') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="emergency_contact_name"
                                                class="form-label">{{ __('Name') }}</label>
                                            <input type="text" class="form-control" id="emergency_contact_name"
                                                name="emergency_contact_name"
                                                value="{{ old('emergency_contact_name') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="emergency_contact_phone"
                                                class="form-label">{{ __('Phone') }}</label>
                                            <input type="tel" class="form-control" id="emergency_contact_phone"
                                                name="emergency_contact_phone"
                                                value="{{ old('emergency_contact_phone') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="emergency_contact_relation"
                                                class="form-label">{{ __('Relationship') }}</label>
                                            <input type="text" class="form-control" id="emergency_contact_relation"
                                                name="emergency_contact_relation"
                                                value="{{ old('emergency_contact_relation') }}"
                                                placeholder="Spouse, Parent">
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
                                            <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="2">{{ old('medical_conditions') }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Status Card -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-toggle-on me-2"></i>{{ __('Status') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">{{ __('Employment Status') }}</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" selected>{{ __('Active') }}</option>
                                            <option value="on_leave">{{ __('On Leave') }}</option>
                                            <option value="resigned">{{ __('Resigned') }}</option>
                                            <option value="terminated">{{ __('Terminated') }}</option>
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
                                        <li>{{ __('Email is required for system access') }}</li>
                                        <li>{{ __('Profile photo max size: 2MB') }}</li>
                                        <li>{{ __('Emergency contact is recommended') }}</li>
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
                                        <a href="{{ route('tenant.modules.teachers.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                            <span class="spinner-border spinner-border-sm d-none me-2"
                                                id="spinner"></span>
                                            <i class="bi bi-check-circle me-2" id="icon"></i>
                                            <span id="text">{{ __('Save Teacher') }}</span>
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
                    document.getElementById('teacherForm').addEventListener('submit', function() {
                        const btn = document.getElementById('submitBtn');
                        const spinner = document.getElementById('spinner');
                        const icon = document.getElementById('icon');
                        const text = document.getElementById('text');

                        btn.disabled = true;
                        spinner.classList.remove('d-none');
                        icon.classList.add('d-none');
                        text.textContent = '{{ __('Saving...') }}';
                    });

                    // File size validation
                    document.getElementById('profile_photo')?.addEventListener('change', function(e) {
                        if (e.target.files[0] && e.target.files[0].size > 2 * 1024 * 1024) {
                            alert('{{ __('File size must be less than 2MB') }}');
                            this.value = '';
                        }
                    });
                </script>
            @endpush
        @endsection
