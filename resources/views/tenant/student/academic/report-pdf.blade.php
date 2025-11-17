<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Academic Report - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24pt;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18pt;
            font-weight: bold;
            margin-top: 10px;
            color: #333;
        }
        .student-info {
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .statistics {
            margin: 20px 0;
            display: flex;
            justify-content: space-around;
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
        }
        .stat-box {
            text-align: center;
        }
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            color: #0d6efd;
        }
        .stat-label {
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #0d6efd;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .grade-excellent {
            color: #198754;
            font-weight: bold;
        }
        .grade-good {
            color: #0d6efd;
            font-weight: bold;
        }
        .grade-average {
            color: #ffc107;
            font-weight: bold;
        }
        .grade-poor {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            font-size: 9pt;
            color: #666;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        h2 {
            color: #0d6efd;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
        }
        .assessment-details {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .assessment-item {
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 3px solid #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="school-name">{{ tenant('name') ?? 'School Name' }}</div>
        <div style="font-size: 10pt; color: #666;">{{ tenant('email') ?? '' }}</div>
        <div class="report-title">ACADEMIC PROGRESS REPORT</div>
        <div style="font-size: 12pt; margin-top: 10px;">{{ $term->name }}</div>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="info-row">
            <div class="info-label">Student Name:</div>
            <div class="info-value">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Admission Number:</div>
            <div class="info-value">{{ $student->admission_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Class:</div>
            <div class="info-value">{{ $student->schoolClass->name ?? 'N/A' }}{{ $student->classStream ? ' - ' . $student->classStream->name : '' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Term:</div>
            <div class="info-value">{{ $term->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Report Date:</div>
            <div class="info-value">{{ now()->format('F d, Y') }}</div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="statistics">
        <div class="stat-box">
            <div class="stat-value">{{ $statistics['total_subjects'] }}</div>
            <div class="stat-label">Total Subjects</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $statistics['total_assessments'] }}</div>
            <div class="stat-label">Assessments Taken</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($statistics['average_score'], 1) }}</div>
            <div class="stat-label">Average Score</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($statistics['average_percentage'], 1) }}%</div>
            <div class="stat-label">Overall Percentage</div>
        </div>
    </div>

    <!-- Subject Performance Summary -->
    <h2>Subject Performance Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th style="text-align: center;">Assessments</th>
                <th style="text-align: center;">Average Score</th>
                <th style="text-align: center;">Percentage</th>
                <th style="text-align: center;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjectGrades as $subjectId => $subjectGradeList)
                @php
                    $subject = $subjectGradeList->first()->subject;
                    $avgScore = $subjectGradeList->avg('marks_obtained');
                    $avgTotal = $subjectGradeList->avg('total_marks');
                    $avgPercentage = $avgTotal > 0 ? ($avgScore / $avgTotal) * 100 : 0;
                    $avgGrade = $subjectGradeList->first()->grade_letter ?? 'N/A';
                    
                    $gradeClass = 'grade-poor';
                    if ($avgPercentage >= 80) $gradeClass = 'grade-excellent';
                    elseif ($avgPercentage >= 70) $gradeClass = 'grade-good';
                    elseif ($avgPercentage >= 50) $gradeClass = 'grade-average';
                @endphp
                <tr>
                    <td><strong>{{ $subject->name }}</strong></td>
                    <td style="text-align: center;">{{ $subjectGradeList->count() }}</td>
                    <td style="text-align: center;">{{ number_format($avgScore, 1) }}/{{ number_format($avgTotal, 0) }}</td>
                    <td style="text-align: center;" class="{{ $gradeClass }}">{{ number_format($avgPercentage, 1) }}%</td>
                    <td style="text-align: center;" class="{{ $gradeClass }}"><strong>{{ $avgGrade }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Detailed Assessment Breakdown -->
    <h2>Detailed Assessment Breakdown</h2>
    @foreach($subjectGrades as $subjectId => $subjectGradeList)
        @php
            $subject = $subjectGradeList->first()->subject;
        @endphp
        <h3 style="color: #333; margin-top: 25px; margin-bottom: 15px;">{{ $subject->name }}</h3>
        <div class="assessment-details">
            @foreach($subjectGradeList as $grade)
                <div class="assessment-item">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <strong>{{ $grade->assessment_name }}</strong>
                        <span>{{ $grade->assessment_date ? $grade->assessment_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10pt; color: #666;">
                        <span>Type: {{ $grade->assessment_type }}</span>
                        <span>Teacher: {{ $grade->teacher->name ?? 'N/A' }}</span>
                    </div>
                    <div style="margin-top: 10px; display: flex; justify-content: space-between;">
                        <div>
                            <strong>Score:</strong> 
                            <span class="{{ 
                                ($grade->marks_obtained / $grade->total_marks * 100) >= 70 ? 'grade-excellent' : 
                                (($grade->marks_obtained / $grade->total_marks * 100) >= 50 ? 'grade-good' : 'grade-poor')
                            }}">
                                {{ number_format($grade->marks_obtained, 1) }}/{{ number_format($grade->total_marks, 0) }}
                                ({{ number_format(($grade->marks_obtained / $grade->total_marks * 100), 1) }}%)
                            </span>
                        </div>
                        <div><strong>Grade:</strong> <span class="grade-good">{{ $grade->grade_letter ?? 'N/A' }}</span></div>
                    </div>
                    @if($grade->remarks)
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #dee2e6;">
                            <strong>Remarks:</strong> {{ $grade->remarks }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Class Teacher's Signature
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Principal's Signature
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Important Note:</strong> This is an official academic report generated by the school management system.</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        <p style="margin-top: 10px; font-style: italic;">
            "Education is the most powerful weapon which you can use to change the world." - Nelson Mandela
        </p>
    </div>
</body>
</html>
