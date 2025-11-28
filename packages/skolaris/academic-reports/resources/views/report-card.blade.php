<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report Card - {{ $report->student->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ config('skolaris_reports.report_settings.font_url') }}" rel="stylesheet">
    <style>
        :root {
            --font-family: {!! config('skolaris_reports.report_settings.font_family') !!};
            --base-font-size: {{ config('skolaris_reports.report_settings.base_font_size') }};
            --header-weight: {{ config('skolaris_reports.report_settings.header_font_weight') }};
            --body-weight: {{ config('skolaris_reports.report_settings.body_font_weight') }};
            --primary-color: #2c3e50;
            --secondary-color: #7f8c8d;
            --accent-color: #3498db;
            --border-color: #ecf0f1;
        }

        body {
            font-family: var(--font-family);
            font-size: var(--base-font-size);
            font-weight: var(--body-weight);
            color: var(--primary-color);
            line-height: 1.6;
            margin: 0;
            padding: 40px;
            background: #fff;
        }

        .report-container {
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            background: white;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .school-branding {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo {
            height: 80px;
            width: auto;
        }

        .school-info h1 {
            margin: 0;
            font-size: 24px;
            font-weight: var(--header-weight);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .school-info h3 {
            margin: 5px 0 0;
            font-size: 14px;
            color: var(--secondary-color);
            font-weight: 500;
        }

        .report-title {
            text-align: right;
        }

        .report-title h2 {
            margin: 0;
            font-size: 28px;
            color: var(--accent-color);
            font-weight: 300;
        }

        .term-info {
            font-size: 12px;
            color: var(--secondary-color);
        }

        /* Student Details */
        .student-details {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-item label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 2px;
        }

        .info-item span {
            font-size: 14px;
            font-weight: 600;
        }

        .student-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #757575;
        }

        /* Academic Performance */
        .section-title {
            font-size: 16px;
            font-weight: var(--header-weight);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: var(--accent-color);
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            text-align: left;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            font-size: 12px;
            text-transform: uppercase;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            background: #f1f2f6;
        }

        /* Summary Footer */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .summary-item h4 {
            margin: 0 0 5px;
            font-size: 12px;
            color: var(--secondary-color);
        }

        .summary-item .value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Remarks & Signatures */
        .remarks-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .remark-box {
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 8px;
            height: 100%;
        }

        .remark-box h4 {
            margin: 0 0 10px;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--secondary-color);
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid var(--primary-color);
            width: 60%;
            padding-top: 5px;
            font-size: 10px;
            font-style: italic;
        }

        /* Fees Section */
        .fees-section {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 6px;
            color: #856404;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fees-section h4 {
            margin: 0;
            font-size: 14px;
        }

        .fee-status {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            background: rgba(255,255,255,0.5);
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: var(--secondary-color);
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        @media print {
            body { padding: 0; }
            .report-container { width: 100%; max-width: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header -->
        <div class="header">
            <div class="school-branding">
                <img src="{{ asset(config('skolaris_reports.school_logo')) }}" alt="Logo" class="logo">
                <div class="school-info">
                    <h1>{{ config('skolaris_reports.school_name') }}</h1>
                    <h3>Excellence in Education</h3>
                </div>
            </div>
            <div class="report-title">
                <h2>ACADEMIC REPORT</h2>
                <div class="term-info">
                    {{ $report->term->name }}<br>
                    {{ $report->term->start_date }} - {{ $report->term->end_date }}
                </div>
            </div>
        </div>

        <!-- Student Info -->
        <div class="student-details">
            <div class="info-grid">
                <div class="info-item">
                    <label>Student Name</label>
                    <span>{{ $report->student->name }}</span>
                </div>
                <div class="info-item">
                    <label>Admission Number</label>
                    <span>{{ $report->student->id }}</span>
                </div>
                <div class="info-item">
                    <label>Class / Grade</label>
                    <span>{{ $report->class_name }}</span>
                </div>
                <div class="info-item">
                    <label>Date of Issue</label>
                    <span>{{ date('F d, Y') }}</span>
                </div>
            </div>
            <div>
                @if($report->student->photo_path)
                    <img src="{{ asset($report->student->photo_path) }}" alt="Student" class="student-photo">
                @else
                    <div class="no-photo">No Photo</div>
                @endif
            </div>
        </div>

        <!-- Academic Table -->
        <div class="section-title">Academic Performance</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Subject</th>
                    <th style="width: 15%">Score</th>
                    <th style="width: 15%">Grade</th>
                    <th style="width: 30%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->marks as $mark)
                <tr>
                    <td><strong>{{ $mark->subject->name }}</strong></td>
                    <td>{{ $mark->score }}%</td>
                    <td><span class="grade-badge">{{ $mark->grade }}</span></td>
                    <td>{{ $mark->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Total Score</h4>
                <div class="value">{{ $report->total_marks }}</div>
            </div>
            <div class="summary-item">
                <h4>Average Score</h4>
                <div class="value">{{ number_format($report->average_score, 1) }}%</div>
            </div>
            <div class="summary-item">
                <h4>Overall Grade</h4>
                <div class="value">
                    @php
                        $avg = $report->average_score;
                        $grade = 'E';
                        if($avg >= 80) $grade = 'A';
                        elseif($avg >= 70) $grade = 'B';
                        elseif($avg >= 60) $grade = 'C';
                        elseif($avg >= 50) $grade = 'D';
                    @endphp
                    {{ $grade }}
                </div>
            </div>
        </div>

        <!-- Remarks -->
        <div class="remarks-section">
            <div class="remark-box">
                <h4>Class Teacher's Remarks</h4>
                <p><em>"{{ $report->class_teacher_remarks }}"</em></p>
                <div class="signature-line">Class Teacher Signature</div>
            </div>
            <div class="remark-box">
                <h4>Principal's Remarks</h4>
                <p><em>"{{ $report->principal_remarks }}"</em></p>
                <div class="signature-line">Principal Signature</div>
            </div>
        </div>

        <!-- Fees -->
        @if($fee)
        <div class="fees-section">
            <div>
                <h4>Fee Status</h4>
                <span style="font-size: 12px">Balance: {{ number_format($fee->amount_due - $fee->amount_paid, 2) }}</span>
            </div>
            <div class="fee-status" style="color: {{ $fee->status == 'cleared' ? '#155724' : '#721c24' }}; background: {{ $fee->status == 'cleared' ? '#d4edda' : '#f8d7da' }}">
                {{ strtoupper($fee->status) }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            This report is computer generated and requires no seal. | Generated via Skolaris Cloud
        </div>
    </div>
</body>
</html>
