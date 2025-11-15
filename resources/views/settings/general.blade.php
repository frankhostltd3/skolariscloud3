@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h4 fw-semibold mb-1">
                        <span class="bi bi-gear me-2"></span>
                        {{ __('General Settings') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('Configure general application settings and preferences') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- School Information -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white fw-semibold">
                            <span class="bi bi-building me-2"></span>
                            {{ __('School Information') }}
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.general.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="form_type" value="school_info">

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        @php
                                            $currentSchool = request()->attributes->get('currentSchool');
                                            $currentLogo =
                                                $currentSchool?->logo_url ?: $settings['school_logo'] ?? null;
                                        @endphp
                                        <label class="form-label">{{ __('Current Logo') }}</label>
                                        <div class="d-flex align-items-center gap-3">
                                            @if ($currentLogo)
                                                <img src="{{ $currentLogo }}" alt="School Logo" style="height:60px"
                                                    onerror="this.style.display='none'">
                                            @else
                                                <span class="text-muted">No logo uploaded yet</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_name" class="form-label">School Name</label>
                                        <input type="text"
                                            class="form-control @error('school_name') is-invalid @enderror" id="school_name"
                                            name="school_name"
                                            value="{{ old('school_name', $settings['school_name'] ?? 'School Management System') }}">
                                        @error('school_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="website_title" class="form-label">Website Title</label>
                                        <input type="text"
                                            class="form-control @error('website_title') is-invalid @enderror"
                                            id="website_title" name="website_title"
                                            value="{{ old('website_title', $settings['website_title'] ?? '') }}"
                                            placeholder="Shown in browser title bars">
                                        @error('website_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_code" class="form-label">School Code</label>
                                        <input type="text"
                                            class="form-control @error('school_code') is-invalid @enderror" id="school_code"
                                            name="school_code"
                                            value="{{ old('school_code', $settings['school_code'] ?? 'SCH001') }}">
                                        @error('school_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_email" class="form-label">School Email</label>
                                        <input type="email"
                                            class="form-control @error('school_email') is-invalid @enderror"
                                            id="school_email" name="school_email"
                                            value="{{ old('school_email', $settings['school_email'] ?? 'info@school.com') }}">
                                        @error('school_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_phone" class="form-label">School Phone</label>
                                        <input type="tel"
                                            class="form-control @error('school_phone') is-invalid @enderror"
                                            id="school_phone" name="school_phone"
                                            value="{{ old('school_phone', $settings['school_phone'] ?? '+1-234-567-8900') }}">
                                        @error('school_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="school_address" class="form-label">School Address</label>
                                        <textarea class="form-control @error('school_address') is-invalid @enderror" id="school_address" name="school_address"
                                            rows="3">{{ old('school_address', $settings['school_address'] ?? '123 Education Street, Learning City, State 12345') }}</textarea>
                                        @error('school_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_website" class="form-label">School Website</label>
                                        <input type="url"
                                            class="form-control @error('school_website') is-invalid @enderror"
                                            id="school_website" name="school_website"
                                            value="{{ old('school_website', $settings['school_website'] ?? 'https://www.school.com') }}">
                                        @error('school_website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_logo" class="form-label">Upload New Logo
                                            (PNG/JPG/SVG/WebP)</label>
                                        <input type="file"
                                            class="form-control @error('school_logo') is-invalid @enderror" id="school_logo"
                                            name="school_logo" accept="image/*">
                                        @error('school_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-1">Tip: Use a square transparent PNG/SVG around
                                            120x120 or 240x240 for best results.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="principal_name" class="form-label">Principal Name</label>
                                        <input type="text"
                                            class="form-control @error('principal_name') is-invalid @enderror"
                                            id="principal_name" name="principal_name"
                                            value="{{ old('principal_name', $settings['principal_name'] ?? 'Dr. Jane Smith') }}">
                                        @error('principal_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_type" class="form-label">School Type</label>
                                        <select class="form-select @error('school_type') is-invalid @enderror"
                                            id="school_type" name="school_type">
                                            <option value="government"
                                                {{ ($settings['school_type'] ?? 'private') == 'government' ? 'selected' : '' }}>
                                                Government</option>
                                            <option value="private"
                                                {{ ($settings['school_type'] ?? 'private') == 'private' ? 'selected' : '' }}>
                                                Private</option>
                                            <option value="hybrid"
                                                {{ ($settings['school_type'] ?? 'private') == 'hybrid' ? 'selected' : '' }}>
                                                Hybrid</option>
                                        </select>
                                        @error('school_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_category" class="form-label">School Category</label>
                                        <select class="form-select @error('school_category') is-invalid @enderror"
                                            id="school_category" name="school_category">
                                            <option value="day"
                                                {{ ($settings['school_category'] ?? 'day') == 'day' ? 'selected' : '' }}>
                                                Day
                                            </option>
                                            <option value="boarding"
                                                {{ ($settings['school_category'] ?? 'day') == 'boarding' ? 'selected' : '' }}>
                                                Boarding</option>
                                            <option value="hybrid"
                                                {{ ($settings['school_category'] ?? 'day') == 'hybrid' ? 'selected' : '' }}>
                                                Hybrid</option>
                                        </select>
                                        @error('school_category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="gender_type" class="form-label">Gender Type</label>
                                        <select class="form-select @error('gender_type') is-invalid @enderror"
                                            id="gender_type" name="gender_type">
                                            <option value="boys"
                                                {{ ($settings['gender_type'] ?? 'mixed') == 'boys' ? 'selected' : '' }}>
                                                Boys
                                                Only</option>
                                            <option value="girls"
                                                {{ ($settings['gender_type'] ?? 'mixed') == 'girls' ? 'selected' : '' }}>
                                                Girls Only</option>
                                            <option value="mixed"
                                                {{ ($settings['gender_type'] ?? 'mixed') == 'mixed' ? 'selected' : '' }}>
                                                Mixed</option>
                                        </select>
                                        @error('gender_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="bi bi-floppy me-2"></span>{{ __('Save School Information') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Application Settings -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white fw-semibold">
                            <span class="bi bi-display me-2 text-success"></span>
                            Application Settings
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.general.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="form_type" value="application">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="app_name" class="form-label">Application Name</label>
                                        <input type="text"
                                            class="form-control @error('app_name') is-invalid @enderror" id="app_name"
                                            name="app_name"
                                            value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}">
                                        @error('app_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="timezone" class="form-label">Timezone</label>
                                        <select class="form-select @error('timezone') is-invalid @enderror"
                                            id="timezone" name="timezone">
                                            <option value="UTC"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>
                                                UTC</option>
                                            <option value="America/New_York"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'America/New_York' ? 'selected' : '' }}>
                                                Eastern Time</option>
                                            <option value="America/Chicago"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'America/Chicago' ? 'selected' : '' }}>
                                                Central Time</option>
                                            <option value="America/Denver"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'America/Denver' ? 'selected' : '' }}>
                                                Mountain Time</option>
                                            <option value="America/Los_Angeles"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'America/Los_Angeles' ? 'selected' : '' }}>
                                                Pacific Time</option>
                                            <option value="Europe/London"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'Europe/London' ? 'selected' : '' }}>
                                                GMT
                                            </option>
                                            <option value="Asia/Kolkata"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Kolkata' ? 'selected' : '' }}>
                                                IST
                                            </option>
                                            <option value="Africa/Kampala"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'Africa/Kampala' ? 'selected' : '' }}>
                                                East Africa Time (Kampala)</option>
                                            <option value="Africa/Nairobi"
                                                {{ ($settings['timezone'] ?? 'UTC') == 'Africa/Nairobi' ? 'selected' : '' }}>
                                                East Africa Time (Nairobi)</option>
                                        </select>
                                        @error('timezone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="date_format" class="form-label">Date Format</label>
                                        <select class="form-select @error('date_format') is-invalid @enderror"
                                            id="date_format" name="date_format">
                                            <option value="Y-m-d"
                                                {{ ($settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>
                                                YYYY-MM-DD</option>
                                            <option value="m/d/Y"
                                                {{ ($settings['date_format'] ?? 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>
                                                MM/DD/YYYY</option>
                                            <option value="d/m/Y"
                                                {{ ($settings['date_format'] ?? 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>
                                                DD/MM/YYYY</option>
                                            <option value="F j, Y"
                                                {{ ($settings['date_format'] ?? 'Y-m-d') == 'F j, Y' ? 'selected' : '' }}>
                                                Month
                                                DD, YYYY</option>
                                        </select>
                                        @error('date_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="time_format" class="form-label">Time Format</label>
                                        <select class="form-select @error('time_format') is-invalid @enderror"
                                            id="time_format" name="time_format">
                                            <option value="H:i"
                                                {{ ($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24
                                                Hour
                                                (HH:MM)</option>
                                            <option value="g:i A"
                                                {{ ($settings['time_format'] ?? 'H:i') == 'g:i A' ? 'selected' : '' }}>12
                                                Hour
                                                (H:MM AM/PM)</option>
                                        </select>
                                        @error('time_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="default_language" class="form-label">Default Language</label>
                                        <select class="form-select @error('default_language') is-invalid @enderror"
                                            id="default_language" name="default_language">
                                            <option value="en"
                                                {{ ($settings['default_language'] ?? 'en') == 'en' ? 'selected' : '' }}>
                                                English
                                            </option>
                                            <option value="es"
                                                {{ ($settings['default_language'] ?? 'en') == 'es' ? 'selected' : '' }}>
                                                Spanish
                                            </option>
                                            <option value="fr"
                                                {{ ($settings['default_language'] ?? 'en') == 'fr' ? 'selected' : '' }}>
                                                French
                                            </option>
                                            <option value="de"
                                                {{ ($settings['default_language'] ?? 'en') == 'de' ? 'selected' : '' }}>
                                                German
                                            </option>
                                        </select>
                                        @error('default_language')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="records_per_page" class="form-label">Records Per Page</label>
                                        <select class="form-select @error('records_per_page') is-invalid @enderror"
                                            id="records_per_page" name="records_per_page">
                                            <option value="10"
                                                {{ ($settings['records_per_page'] ?? '15') == '10' ? 'selected' : '' }}>10
                                            </option>
                                            <option value="15"
                                                {{ ($settings['records_per_page'] ?? '15') == '15' ? 'selected' : '' }}>15
                                            </option>
                                            <option value="25"
                                                {{ ($settings['records_per_page'] ?? '15') == '25' ? 'selected' : '' }}>25
                                            </option>
                                            <option value="50"
                                                {{ ($settings['records_per_page'] ?? '15') == '50' ? 'selected' : '' }}>50
                                            </option>
                                            <option value="100"
                                                {{ ($settings['records_per_page'] ?? '15') == '100' ? 'selected' : '' }}>
                                                100
                                            </option>
                                        </select>
                                        @error('records_per_page')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <span class="bi bi-floppy me-2"></span>{{ __('Save Application Settings') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Settings Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-semibold">
                            <span class="bi bi-info-circle me-2 text-info"></span>
                            Settings Help
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <h6 class="fw-semibold">School Information</h6>
                                <p class="mb-0 small">Basic information about your school that appears in reports, emails,
                                    and other communications.</p>
                            </div>

                            <div class="alert alert-success mb-0">
                                <h6 class="fw-semibold">Application Settings</h6>
                                <p class="mb-0 small">Configure how the application behaves, including timezone, language,
                                    and display preferences.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-header bg-white fw-semibold">
                            <span class="bi bi-lightning me-2 text-primary"></span>
                            Quick Actions
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('settings.mail.edit') }}" class="btn btn-outline-primary btn-sm">
                                    <span class="bi bi-envelope me-2"></span>{{ __('Mail Settings') }}
                                </a>
                                <a href="{{ route('settings.payments.edit') }}" class="btn btn-outline-success btn-sm">
                                    <span class="bi bi-credit-card me-2"></span>{{ __('Payment Settings') }}
                                </a>
                                <a href="{{ route('settings.messaging.edit') }}" class="btn btn-outline-info btn-sm">
                                    <span class="bi bi-chat-dots me-2"></span>{{ __('Messaging Channels') }}
                                </a>
                                <button class="btn btn-outline-danger btn-sm" onclick="clearCache()">
                                    <span class="bi bi-trash me-2"></span>{{ __('Clear Cache') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function clearCache() {
            if (!confirm('Are you sure you want to clear the application cache?')) return;
            try {
                const resp = await fetch("{{ route('settings.general.clear-cache') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await resp.json().catch(() => ({}));
                if (resp.ok && data.success) {
                    alert(data.message || 'Application cache cleared successfully!');
                    window.location.reload();
                } else {
                    alert((data && data.message) || 'Failed to clear cache.');
                }
            } catch (e) {
                alert('Error clearing cache: ' + e.message);
            }
        }
    </script>
@endsection
