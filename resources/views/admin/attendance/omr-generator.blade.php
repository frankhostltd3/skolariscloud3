@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0"><i class="bi bi-file-earmark-medical"></i> OMR Template Generator</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Generate Attendance Sheet</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.omr.generate') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Select Class <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control"
                                    value="{{ today()->format('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sheet Title</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="e.g., Morning Attendance, Assembly Attendance" value="Attendance Sheet">
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_photos" id="includePhotos"
                                        value="1">
                                    <label class="form-check-label" for="includePhotos">
                                        Include student photos (larger file size)
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Instructions:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Select class and date</li>
                                    <li>Download PDF sheet</li>
                                    <li>Print on white paper</li>
                                    <li>Mark bubbles with dark pen/pencil</li>
                                    <li>Scan completed sheet</li>
                                    <li>Upload to optical scanner</li>
                                </ul>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-download"></i> Generate & Download PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Template Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='500'%3E%3Crect width='400' height='500' fill='%23fff' stroke='%23000' stroke-width='2'/%3E%3Ctext x='200' y='30' text-anchor='middle' font-size='16' font-weight='bold'%3ESchool Name%3C/text%3E%3Ctext x='200' y='50' text-anchor='middle' font-size='14'%3EAttendance Sheet%3C/text%3E%3Ctext x='20' y='80' font-size='12'%3EClass: P7 A%3C/text%3E%3Ctext x='300' y='80' font-size='12'%3EDate: 2025-01-17%3C/text%3E%3Cline x1='20' y1='100' x2='380' y2='100' stroke='%23000'/%3E%3Ctext x='20' y='120' font-size='11' font-weight='bold'%3ENo.%3C/text%3E%3Ctext x='60' y='120' font-size='11' font-weight='bold'%3EStudent Name%3C/text%3E%3Ctext x='250' y='120' font-size='11' font-weight='bold'%3EP%3C/text%3E%3Ctext x='280' y='120' font-size='11' font-weight='bold'%3EA%3C/text%3E%3Ctext x='310' y='120' font-size='11' font-weight='bold'%3EL%3C/text%3E%3Ctext x='340' y='120' font-size='11' font-weight='bold'%3EE%3C/text%3E%3Cline x1='20' y1='125' x2='380' y2='125' stroke='%23000'/%3E%3C!-- Sample rows --%3E%3Ctext x='20' y='145' font-size='10'%3E1%3C/text%3E%3Ctext x='60' y='145' font-size='10'%3EJohn Doe%3C/text%3E%3Ccircle cx='255' cy='140' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='285' cy='140' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='315' cy='140' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='345' cy='140' r='8' fill='none' stroke='%23000'/%3E%3Ctext x='20' y='165' font-size='10'%3E2%3C/text%3E%3Ctext x='60' y='165' font-size='10'%3EJane Smith%3C/text%3E%3Ccircle cx='255' cy='160' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='285' cy='160' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='315' cy='160' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='345' cy='160' r='8' fill='none' stroke='%23000'/%3E%3Ctext x='20' y='185' font-size='10'%3E3%3C/text%3E%3Ctext x='60' y='185' font-size='10'%3EMary Johnson%3C/text%3E%3Ccircle cx='255' cy='180' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='285' cy='180' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='315' cy='180' r='8' fill='none' stroke='%23000'/%3E%3Ccircle cx='345' cy='180' r='8' fill='none' stroke='%23000'/%3E%3Ctext x='20' y='480' font-size='10'%3ELegend: P=Present, A=Absent, L=Late, E=Excused%3C/text%3E%3C/svg%3E"
                                class="img-fluid border" alt="Template Preview">
                            <p class="text-muted mt-3">
                                <small>Sample layout showing bubble sheet format</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Scanning Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Resolution:</strong> Scan at 300 DPI minimum</li>
                            <li><strong>Color:</strong> Grayscale or Black & White</li>
                            <li><strong>Format:</strong> PDF or JPG</li>
                            <li><strong>Alignment:</strong> Keep sheet flat and straight</li>
                            <li><strong>Lighting:</strong> Ensure even illumination</li>
                            <li><strong>Marks:</strong> Fill bubbles completely with dark ink</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
