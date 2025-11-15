<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        .student-info {
            margin: 20px 0;
            background-color: #f5f5f5;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
        }
        .grade-A, .grade-A-plus { background-color: #d4edda; }
        .grade-B, .grade-B-plus { background-color: #d1ecf1; }
        .grade-C, .grade-C-plus { background-color: #fff3cd; }
        .grade-D, .grade-D-plus { background-color: #f8d7da; }
        .grade-F { background-color: #f5c6cb; }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #0066cc;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .summary-item strong {
            font-weight: bold;
        }
        .comments {
            margin: 20px 0;
        }
        .comment-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .comment-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .signature-line {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line-text {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 50px;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $school->name }}</div>
        <div>{{ setting('school_address', 'School Address') }}</div>
        <div>Tel: {{ setting('school_phone', 'N/A') }} | Email: {{ setting('school_email', 'N/A') }}</div>
        <div class="report-title">STUDENT REPORT CARD</div>
        <div>Academic Year: {{ $academic_year }} | {{ $term }}</div>
    </div>

    <div class="student-info">
        <div class="info-row">
            <div class="info-label">Student Name:</div>
            <div>{{ $student->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Student ID:</div>
            <div>{{ $student->id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div>{{ $student->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Class Rank:</div>
            <div>{{ $class_rank }} of {{ $total_students }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th style="text-align: center;">Mark</th>
                <th style="text-align: center;">Out Of</th>
                <th style="text-align: center;">Grade</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $grade)
            <tr class="grade-{{ str_replace('+', '-plus', $grade['grade']) }}">
                <td>{{ $grade['subject'] }}</td>
                <td style="text-align: center;">{{ $grade['mark'] }}</td>
                <td style="text-align: center;">{{ $grade['out_of'] }}</td>
                <td style="text-align: center;"><strong>{{ $grade['grade'] }}</strong></td>
                <td>{{ $grade['comment'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-item">
            <span><strong>Total Marks:</strong></span>
            <span>{{ $total_marks }} / {{ $total_possible }}</span>
        </div>
        <div class="summary-item">
            <span><strong>Overall Percentage:</strong></span>
            <span>{{ $percentage }}%</span>
        </div>
        <div class="summary-item">
            <span><strong>GPA:</strong></span>
            <span>{{ number_format($gpa, 2) }} / 4.00</span>
        </div>
        <div class="summary-item">
            <span><strong>Attendance:</strong></span>
            <span>Present: {{ $attendance['present'] }} | Absent: {{ $attendance['absent'] }} | Late: {{ $attendance['late'] }}</span>
        </div>
    </div>

    <div class="comments">
        <div class="comment-box">
            <div class="comment-title">Class Teacher's Comment:</div>
            <div>{{ $teacher_comment }}</div>
        </div>

        <div class="comment-box">
            <div class="comment-title">Principal's Comment:</div>
            <div>{{ $principal_comment }}</div>
        </div>
    </div>

    <div class="signature-line">
        <div class="signature-box">
            <div class="signature-line-text">Class Teacher</div>
        </div>
        <div class="signature-box">
            <div class="signature-line-text">Principal</div>
        </div>
        <div class="signature-box">
            <div class="signature-line-text">Parent/Guardian</div>
        </div>
    </div>

    <div class="footer">
        <p>Generated on {{ $generated_at }}</p>
        <p>&copy; {{ now()->year }} {{ $school->name }}. All rights reserved.</p>
        <p><em>This is an official document. Any alteration will render it invalid.</em></p>
    </div>
</body>
</html>
