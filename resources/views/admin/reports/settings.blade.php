@extends('tenant.layouts.app')

@section('title', 'Report Card Settings')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Report Card Settings</h1>
                <p class="text-muted mb-0">Configure the appearance and content of student report cards.</p>
            </div>
            <a href="{{ route('admin.reports.report-cards') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Report Cards
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Configuration</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.reports.settings.update') }}">
                            @csrf
                            @method('PUT')

                            @php
                                $assessmentOptions = $assessmentOptions ?? [
                                    'BOT' => 'Beginning of Term (BOT)',
                                    'MOT' => 'Mid of Term (MOT)',
                                    'EOT' => 'End of Term (EOT)',
                                ];
                                $selectedAssessments = $selectedAssessments ?? array_keys($assessmentOptions);
                            @endphp

                            <h6 class="fw-bold text-primary mb-3">Header & Branding</h6>

                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="report_card_show_logo"
                                    name="report_card_show_logo" value="1"
                                    {{ setting('report_card_show_logo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="report_card_show_logo">Show School Logo</label>
                                <div class="form-text">Display the school logo at the top of the report card.</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Logo Max Width (px)</label>
                                    <input type="number" class="form-control" name="report_card_logo_width"
                                        value="{{ setting('report_card_logo_width', 200) }}" min="50" max="500">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Logo Max Height (px)</label>
                                    <input type="number" class="form-control" name="report_card_logo_height"
                                        value="{{ setting('report_card_logo_height', 100) }}" min="50" max="300">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">School Name Heading</label>
                                <input type="text" class="form-control" name="report_card_school_name"
                                    value="{{ setting('report_card_school_name', auth()->user()->school->name) }}">
                                <div class="form-text">Leave blank to use the default school name.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">School Address & Contact Info</label>
                                <textarea class="form-control" name="report_card_address" rows="3">{{ setting('report_card_address', setting('school_address') . "\nTel: " . setting('school_phone') . ' | Email: ' . setting('school_email')) }}</textarea>
                                <div class="form-text">This text appears below the school name. Use new lines for
                                    formatting.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Color Theme</label>
                                <input type="color" class="form-control form-control-color" name="report_card_color_theme"
                                    value="{{ setting('report_card_color_theme', '#0066cc') }}" title="Choose your color">
                                <div class="form-text">Primary color used for headers and borders.</div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-primary mb-3">Assessment Columns</h6>
                            <p class="small text-muted">Select the assessment periods that should appear in the marks table.
                                The order below matches the one used on the report card. At least one option is required.
                            </p>

                            <div class="row">
                                @foreach ($assessmentOptions as $code => $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="report_card_assessments[]"
                                                value="{{ $code }}" id="assessment_{{ $code }}"
                                                {{ in_array($code, $selectedAssessments) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="assessment_{{ $code }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('report_card_assessments')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-1"></i>These selections control the columns shown per
                                subject (e.g., BOT, MOT, EOT) on PDF report cards.
                            </div>

                            <h6 class="fw-bold text-primary mb-3 mt-4">Student Photo</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Photo Width (px)</label>
                                    <input type="number" class="form-control" name="report_card_photo_width"
                                        value="{{ setting('report_card_photo_width', 80) }}" min="50" max="200">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Photo Height (px)</label>
                                    <input type="number" class="form-control" name="report_card_photo_height"
                                        value="{{ setting('report_card_photo_height', 80) }}" min="50"
                                        max="200">
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-primary mb-3">Typography</h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Font Family</label>
                                    <select class="form-select" name="report_card_font_family">
                                        <optgroup label="Standard Fonts">
                                            <option value="Arial"
                                                {{ setting('report_card_font_family', 'Arial') == 'Arial' ? 'selected' : '' }}>
                                                Arial</option>
                                            <option value="Helvetica"
                                                {{ setting('report_card_font_family') == 'Helvetica' ? 'selected' : '' }}>
                                                Helvetica</option>
                                            <option value="Times New Roman"
                                                {{ setting('report_card_font_family') == 'Times New Roman' ? 'selected' : '' }}>
                                                Times New Roman</option>
                                            <option value="Courier"
                                                {{ setting('report_card_font_family') == 'Courier' ? 'selected' : '' }}>
                                                Courier</option>
                                            <option value="Georgia"
                                                {{ setting('report_card_font_family') == 'Georgia' ? 'selected' : '' }}>
                                                Georgia</option>
                                            <option value="Verdana"
                                                {{ setting('report_card_font_family') == 'Verdana' ? 'selected' : '' }}>
                                                Verdana</option>
                                        </optgroup>
                                        <optgroup label="Modern Sans-Serif">
                                            <option value="DejaVu Sans"
                                                {{ setting('report_card_font_family') == 'DejaVu Sans' ? 'selected' : '' }}>
                                                DejaVu Sans (UTF-8)</option>
                                            <option value="Montserrat"
                                                {{ setting('report_card_font_family') == 'Montserrat' ? 'selected' : '' }}>
                                                Montserrat (Google Font)</option>
                                            <option value="Quicksand"
                                                {{ setting('report_card_font_family') == 'Quicksand' ? 'selected' : '' }}>
                                                Quicksand (Google Font)</option>
                                            <option value="Poppins"
                                                {{ setting('report_card_font_family') == 'Poppins' ? 'selected' : '' }}>
                                                Poppins (Google Font)</option>
                                            <option value="Raleway"
                                                {{ setting('report_card_font_family') == 'Raleway' ? 'selected' : '' }}>
                                                Raleway (Google Font)</option>
                                            <option value="Open Sans"
                                                {{ setting('report_card_font_family') == 'Open Sans' ? 'selected' : '' }}>
                                                Open Sans (Google Font)</option>
                                            <option value="Lato"
                                                {{ setting('report_card_font_family') == 'Lato' ? 'selected' : '' }}>Lato
                                                (Google Font)</option>
                                            <option value="Roboto"
                                                {{ setting('report_card_font_family') == 'Roboto' ? 'selected' : '' }}>
                                                Roboto (Google Font)</option>
                                        </optgroup>
                                        <optgroup label="Serif Fonts">
                                            <option value="Merriweather"
                                                {{ setting('report_card_font_family') == 'Merriweather' ? 'selected' : '' }}>
                                                Merriweather (Google Font)</option>
                                            <option value="Playfair Display"
                                                {{ setting('report_card_font_family') == 'Playfair Display' ? 'selected' : '' }}>
                                                Playfair Display (Google Font)</option>
                                        </optgroup>
                                    </select>
                                    <div class="form-text">Google Fonts require internet connection during PDF generation.
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Base Font Size (px)</label>
                                    <input type="number" class="form-control" name="report_card_font_size"
                                        value="{{ setting('report_card_font_size', 12) }}" min="10"
                                        max="18">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Heading Weight</label>
                                    <select class="form-select" name="report_card_heading_font_weight">
                                        <option value="bold"
                                            {{ setting('report_card_heading_font_weight', 'bold') == 'bold' ? 'selected' : '' }}>
                                            Bold</option>
                                        <option value="normal"
                                            {{ setting('report_card_heading_font_weight') == 'normal' ? 'selected' : '' }}>
                                            Normal</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-primary mb-3">Signatures</h6>
                            <p class="small text-muted">Define the titles for the signature lines at the bottom of the
                                report.</p>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Signature 1 Title</label>
                                    <input type="text" class="form-control" name="report_card_signature_1"
                                        value="{{ setting('report_card_signature_1', 'Class Teacher') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Signature 2 Title</label>
                                    <input type="text" class="form-control" name="report_card_signature_2"
                                        value="{{ setting('report_card_signature_2', 'Principal') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Signature 3 Title</label>
                                    <input type="text" class="form-control" name="report_card_signature_3"
                                        value="{{ setting('report_card_signature_3', 'Parent/Guardian') }}">
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Preview Tips</h5>
                    </div>
                    <div class="card-body">
                        <p>Changes made here will affect all future generated report cards.</p>
                        <ul class="mb-0">
                            <li>Ensure your school logo is uploaded in <strong>General Settings</strong>.</li>
                            <li>The color theme applies to table headers and borders.</li>
                            <li>You can customize the address format to include your motto or website.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
