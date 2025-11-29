<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .school-address {
            font-size: 11px;
            color: #666;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .student-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .student-info table {
            width: 100%;
        }

        .student-info td {
            padding: 5px 10px;
        }

        .student-info .label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .grades-table th {
            background: #0d6efd;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        .grades-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .grades-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .grades-table .text-end {
            text-align: right;
        }

        .grades-table .text-center {
            text-align: center;
        }

        .grade-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 11px;
        }

        .grade-excellent {
            background: #198754;
            color: white;
        }

        .grade-good {
            background: #0d6efd;
            color: white;
        }

        .grade-average {
            background: #ffc107;
            color: #333;
        }

        .grade-poor {
            background: #dc3545;
            color: white;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #e7f1ff;
            border-radius: 5px;
            border-left: 4px solid #0d6efd;
        }

        .summary h4 {
            margin-bottom: 10px;
            color: #0d6efd;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
        }

        .generated-at {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="school-name">{{ $school->name ?? 'School Name' }}</div>
        <div class="school-address">{{ setting('school_address') ?? '' }}</div>
        <div class="report-title">Academic Report Card</div>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td><strong>{{ $student->name }}</strong></td>
                <td class="label">Student ID:</td>
                <td>{{ $student->student_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Class:</td>
                <td>{{ optional($student->class)->name ?? 'N/A' }}</td>
                <td class="label">Stream:</td>
                <td>{{ optional($student->stream)->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Academic Year:</td>
                <td>{{ date('Y') }}</td>
                <td class="label">Term:</td>
                <td>{{ setting('current_term', 'Term 1') }}</td>
            </tr>
        </table>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Subject</th>
                <th style="width: 20%;">Assessment</th>
                <th style="width: 15%;" class="text-center">Score</th>
                <th style="width: 10%;" class="text-center">Grade</th>
                <th style="width: 15%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($grades as $index => $grade)
                @php
                    $percentage =
                        $grade->total_marks > 0
                            ? round(($grade->marks_obtained / $grade->total_marks) * 100)
                            : $grade->marks_obtained;
                    $gradeClass =
                        $percentage >= 80
                            ? 'grade-excellent'
                            : ($percentage >= 60
                                ? 'grade-good'
                                : ($percentage >= 40
                                    ? 'grade-average'
                                    : 'grade-poor'));
                    $remarks =
                        $percentage >= 80
                            ? 'Excellent'
                            : ($percentage >= 60
                                ? 'Good'
                                : ($percentage >= 40
                                    ? 'Fair'
                                    : 'Needs Improvement'));
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ optional($grade->subject)->name ?? 'Unknown' }}</strong></td>
                    <td>{{ $grade->assessment_type ?? 'General' }}</td>
                    <td class="text-center">{{ $grade->marks_obtained }}/{{ $grade->total_marks ?? 100 }}</td>
                    <td class="text-center">
                        <span class="grade-badge {{ $gradeClass }}">{{ $grade->grade_letter ?? '-' }}</span>
                    </td>
                    <td>{{ $remarks }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #999;">
                        No grades recorded yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($grades->count() > 0)
        <div class="summary">
            <h4>Performance Summary</h4>
            <table style="width: 100%;">
                <tr>
                    <td><strong>Total Subjects:</strong> {{ $grades->count() }}</td>
                    <td><strong>Total Marks:</strong>
                        {{ $grades->sum('marks_obtained') }}/{{ $grades->sum('total_marks') ?: $grades->count() * 100 }}
                    </td>
                    <td><strong>Average:</strong> {{ $averagePercentage }}%</td>
                    <td><strong>Overall Grade:</strong>
                        @if ($averagePercentage >= 80)
                            <span class="grade-badge grade-excellent">A</span>
                        @elseif($averagePercentage >= 60)
                            <span class="grade-badge grade-good">B</span>
                        @elseif($averagePercentage >= 40)
                            <span class="grade-badge grade-average">C</span>
                        @else
                            <span class="grade-badge grade-poor">D</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Class Teacher</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Head Teacher</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Parent/Guardian</div>
            </div>
        </div>
    </div>

    <div class="generated-at">
        Generated on {{ $generatedAt->format('F d, Y \a\t h:i A') }}
    </div>
</body>

</html>
