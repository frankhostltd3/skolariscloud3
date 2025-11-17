@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('title', __('General Settings'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('General Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.general.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- School Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('School Information') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="school_name" class="form-label">{{ __('School Name') }}</label>
                                <input type="text" class="form-control @error('school_name') is-invalid @enderror"
                                       id="school_name" name="school_name"
                                       value="{{ old('school_name', $school->name ?? '') }}" required>
                                @error('school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_code" class="form-label">{{ __('School Code') }}</label>
                                <input type="text" class="form-control @error('school_code') is-invalid @enderror"
                                       id="school_code" name="school_code"
                                       value="{{ old('school_code', setting('school_code', '')) }}">
                                @error('school_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_address" class="form-label">{{ __('School Address') }}</label>
                                <textarea class="form-control @error('school_address') is-invalid @enderror"
                                          id="school_address" name="school_address" rows="3" required>{{ old('school_address', setting('school_address', '')) }}</textarea>
                                @error('school_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_phone" class="form-label">{{ __('Phone Number') }}</label>
                                <input type="text" class="form-control @error('school_phone') is-invalid @enderror"
                                       id="school_phone" name="school_phone"
                                       value="{{ old('school_phone', setting('school_phone', '')) }}" required>
                                @error('school_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_email" class="form-label">{{ __('School Email') }}</label>
                                <input type="email" class="form-control @error('school_email') is-invalid @enderror"
                                       id="school_email" name="school_email"
                                       value="{{ old('school_email', setting('school_email', '')) }}" required>
                                @error('school_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_website" class="form-label">{{ __('Website') }}</label>
                                <input type="url" class="form-control @error('school_website') is-invalid @enderror"
                                       id="school_website" name="school_website"
                                       value="{{ old('school_website', setting('school_website', '')) }}">
                                @error('school_website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_motto" class="form-label">{{ __('School Motto') }}</label>
                                <input type="text" class="form-control @error('school_motto') is-invalid @enderror"
                                       id="school_motto" name="school_motto"
                                       value="{{ old('school_motto', setting('school_motto', '')) }}">
                                @error('school_motto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="principal_name" class="form-label">{{ __('Principal Name') }}</label>
                                <input type="text" class="form-control @error('principal_name') is-invalid @enderror"
                                       id="principal_name" name="principal_name"
                                       value="{{ old('principal_name', setting('principal_name', '')) }}">
                                @error('principal_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Branding -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Branding') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="website_title" class="form-label">{{ __('Website Title') }}</label>
                                <input type="text" class="form-control @error('website_title') is-invalid @enderror"
                                       id="website_title" name="website_title"
                                       value="{{ old('website_title', $school->website_title ?? '') }}">
                                @error('website_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="app_name" class="form-label">{{ __('Application Name') }}</label>
                                <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                       id="app_name" name="app_name"
                                       value="{{ old('app_name', setting('app_name', config('app.name', 'SkolarisCloud'))) }}">
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="school_logo" class="form-label">{{ __('School Logo') }}</label>
                                <input type="file" class="form-control @error('school_logo') is-invalid @enderror"
                                       id="school_logo" name="school_logo" accept="image/*">
                                @if($school && $school->logo_path)
                                    <div class="mt-2">
                                        <img src="{{ $school->logo_url }}" alt="Current Logo" class="img-thumbnail" style="max-width: 100px;">
                                        <small class="text-muted d-block">{{ __('Current logo') }}</small>
                                    </div>
                                @endif
                                @error('school_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="favicon" class="form-label">{{ __('Favicon') }}</label>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                       id="favicon" name="favicon" accept="image/*,.ico">
                                @if($school && $school->favicon_path)
                                    <div class="mt-2">
                                        <img src="{{ $school->favicon_url }}" alt="Current Favicon" class="img-thumbnail" style="max-width: 32px;">
                                        <small class="text-muted d-block">{{ __('Current favicon') }}</small>
                                    </div>
                                @endif
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Social Media Links') }}</h5>
                                <div id="social-links-container">
                                    @if($school && $school->formatted_social_links)
                                        @foreach($school->formatted_social_links as $index => $link)
                                            <div class="social-link-row mb-2">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="social_links[{{ $index }}][platform]"
                                                               value="{{ $link['platform'] }}" placeholder="Platform (e.g., Facebook)">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="social_links[{{ $index }}][label]"
                                                               value="{{ $link['label'] }}" placeholder="Label (e.g., Follow us)">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="url" class="form-control" name="social_links[{{ $index }}][url]"
                                                               value="{{ $link['url'] }}" placeholder="https://...">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-social-link">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-social-link">
                                    <i class="bi bi-plus"></i> {{ __('Add Social Link') }}
                                </button>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('System Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="timezone" class="form-label">{{ __('Timezone') }}</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                    @foreach(timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" {{ old('timezone', setting('timezone', config('app.timezone', 'UTC'))) == $tz ? 'selected' : '' }}>
                                            {{ $tz }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="language" class="form-label">{{ __('Default Language') }}</label>
                                <select class="form-select @error('language') is-invalid @enderror" id="language" name="language">
                                    <option value="en" {{ old('language', setting('language', 'en')) == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="sw" {{ old('language', setting('language', 'en')) == 'sw' ? 'selected' : '' }}>Swahili</option>
                                </select>
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_format" class="form-label">{{ __('Date Format') }}</label>
                                <select class="form-select @error('date_format') is-invalid @enderror" id="date_format" name="date_format">
                                    <option value="Y-m-d" {{ old('date_format', setting('date_format', 'Y-m-d')) == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="d/m/Y" {{ old('date_format', setting('date_format', 'Y-m-d')) == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    <option value="m/d/Y" {{ old('date_format', setting('date_format', 'Y-m-d')) == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                </select>
                                @error('date_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="time_format" class="form-label">{{ __('Time Format') }}</label>
                                <select class="form-select @error('time_format') is-invalid @enderror" id="time_format" name="time_format">
                                    <option value="H:i:s" {{ old('time_format', setting('time_format', 'H:i:s')) == 'H:i:s' ? 'selected' : '' }}>24-hour (HH:MM:SS)</option>
                                    <option value="h:i:s A" {{ old('time_format', setting('time_format', 'H:i:s')) == 'h:i:s A' ? 'selected' : '' }}>12-hour (HH:MM:SS AM/PM)</option>
                                </select>
                                @error('time_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="records_per_page" class="form-label">{{ __('Records Per Page') }}</label>
                                <select class="form-select @error('records_per_page') is-invalid @enderror" id="records_per_page" name="records_per_page">
                                    <option value="10" {{ old('records_per_page', setting('records_per_page', 25)) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ old('records_per_page', setting('records_per_page', 25)) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ old('records_per_page', setting('records_per_page', 25)) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ old('records_per_page', setting('records_per_page', 25)) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                @error('records_per_page')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save General Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let socialLinkIndex = {{ $school && $school->formatted_social_links ? count($school->formatted_social_links) : 0 }};

    document.getElementById('add-social-link').addEventListener('click', function() {
        const container = document.getElementById('social-links-container');
        const row = document.createElement('div');
        row.className = 'social-link-row mb-2';
        row.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="social_links[${socialLinkIndex}][platform]" placeholder="Platform (e.g., Facebook)">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="social_links[${socialLinkIndex}][label]" placeholder="Label (e.g., Follow us)">
                </div>
                <div class="col-md-5">
                    <input type="url" class="form-control" name="social_links[${socialLinkIndex}][url]" placeholder="https://...">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-social-link">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(row);
        socialLinkIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-social-link') || e.target.closest('.remove-social-link')) {
            e.target.closest('.social-link-row').remove();
        }
    });
});
</script>
@endsection