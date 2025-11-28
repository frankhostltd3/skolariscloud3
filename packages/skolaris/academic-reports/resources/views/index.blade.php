<!DOCTYPE html>
<html>
<head>
    <title>Academic Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center">
                @if(config('skolaris_reports.school_logo'))
                    <img src="{{ asset(config('skolaris_reports.school_logo')) }}" alt="Logo" style="height: 40px;" class="me-3">
                @endif
                <h4 class="mb-0">{{ config('skolaris_reports.school_name') }} - Academic Reports</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>View Student Report</h5>
                        <form action="" method="GET" id="studentForm">
                            <div class="mb-3">
                                <label class="form-label">Term</label>
                                <select class="form-select" id="studentTerm">
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="studentId" placeholder="Enter Student ID">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="viewStudentReport()">View Report</button>
                        </form>
                    </div>
                    <div class="col-md-6 border-start">
                        <h5>View Class Summary</h5>
                        <form action="" method="GET" id="classForm">
                            <div class="mb-3">
                                <label class="form-label">Term</label>
                                <select class="form-select" id="classTerm">
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Class Name</label>
                                <input type="text" class="form-control" id="className" placeholder="e.g. 10A">
                            </div>
                            <button type="button" class="btn btn-success" onclick="viewClassReport()">View Summary</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewStudentReport() {
            const termId = document.getElementById('studentTerm').value;
            const studentId = document.getElementById('studentId').value;
            if(studentId) {
                window.location.href = `/reports/student/${studentId}/term/${termId}`;
            } else {
                alert('Please enter a Student ID');
            }
        }

        function viewClassReport() {
            const termId = document.getElementById('classTerm').value;
            const className = document.getElementById('className').value;
            if(className) {
                window.location.href = `/reports/class/${className}/term/${termId}`;
            } else {
                alert('Please enter a Class Name');
            }
        }
    </script>
</body>
</html>