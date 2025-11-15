@extends('tenant.layouts.app')

@section('title', 'Report Cards')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Report Cards</h1>
            <p class="text-muted mb-0">Generate end-of-term student report cards as PDFs, either per student or for an entire class.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Single Student Report Card
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.report-cards.export-student') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Student <span class="text-danger">*</span></label>
                            <select class="form-select" name="student_id" required>
                                <option value="">Select student</option>
                                @foreach($students as $stu)
                                <option value="{{ $stu->id }}">{{ $stu->name }} (ID: {{ $stu->id }})</option>
                                @endforeach
                            </select>
                            @if($students->isEmpty())
                                <small class="text-muted">No students found. Please add students first.</small>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year">
                                <option value="">Current Year</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2022-2023">2022-2023</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term/Semester</label>
                            <select class="form-select" name="term">
                                <option value="">All Terms</option>
                                <option value="1">Term 1</option>
                                <option value="2">Term 2</option>
                                <option value="3">Term 3</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100" type="submit" {{ $students->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-download me-2"></i>Download PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Class Report Cards (Bulk)
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.report-cards.export-class') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select class="form-select" name="class_id" required>
                                <option value="">Select class</option>
                                @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                            @if($classes->isEmpty())
                                <small class="text-muted">No classes found. Please add classes first.</small>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year">
                                <option value="">Current Year</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2022-2023">2022-2023</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term/Semester</label>
                            <select class="form-select" name="term">
                                <option value="">All Terms</option>
                                <option value="1">Term 1</option>
                                <option value="2">Term 2</option>
                                <option value="3">Term 3</option>
                            </select>
                        </div>
                        <button class="btn btn-secondary w-100" type="submit" {{ $classes->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-file-pdf me-2"></i>Download Merged PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Panel -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Report Card Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">
                                <i class="fas fa-check-circle text-success me-2"></i>Included Information
                            </h6>
                            <ul class="small mb-0">
                                <li>Student personal details</li>
                                <li>Academic performance by subject</li>
                                <li>Overall GPA and class ranking</li>
                                <li>Attendance summary</li>
                                <li>Teacher comments and remarks</li>
                                <li>School logo and official seal</li>
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">
                                <i class="fas fa-cog text-primary me-2"></i>Features
                            </h6>
                            <ul class="small mb-0">
                                <li>Professional PDF format</li>
                                <li>Customizable by term/semester</li>
                                <li>Bulk generation for entire classes</li>
                                <li>Historical data access</li>
                                <li>Print-ready formatting</li>
                                <li>Secure and confidential</li>
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Usage Tips
                            </h6>
                            <ul class="small mb-0">
                                <li>Select "Current Year" for latest data</li>
                                <li>Use bulk download for parent meetings</li>
                                <li>Generate at end of each term</li>
                                <li>Review data before distribution</li>
                                <li>Archive previous reports for records</li>
                                <li>Ensure grades are finalized first</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
