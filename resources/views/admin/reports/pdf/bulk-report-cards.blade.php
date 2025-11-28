<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Report Cards - Bulk Export</title>
    @php
        $fontFamily = setting('report_card_font_family', 'Arial');
        $isGoogleFont = in_array($fontFamily, [
            'Montserrat',
            'Quicksand',
            'Poppins',
            'Raleway',
            'Open Sans',
            'Lato',
            'Roboto',
            'Merriweather',
            'Playfair Display',
        ]);
    @endphp
    @if ($isGoogleFont)
        <link
            href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontFamily) }}:wght@400;700&display=swap"
            rel="stylesheet">
    @endif
    <style>
        body {
            font-family: '{{ setting('report_card_font_family', 'Arial') }}', sans-serif;
            font-size: {{ setting('report_card_font_size', 11) }}px;
            line-height: 1.2;
            margin: 10px;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid {{ setting('report_card_color_theme', '#0066cc') }};
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .school-name {
            font-size: {{ setting('report_card_font_size', 11) + 8 }}px;
            font-weight: {{ setting('report_card_heading_font_weight', 'bold') }};
            color: {{ setting('report_card_color_theme', '#0066cc') }};
            margin-bottom: 2px;
        }

        .report-title {
            font-size: {{ setting('report_card_font_size', 11) + 4 }}px;
            font-weight: {{ setting('report_card_heading_font_weight', 'bold') }};
            margin-top: 2px;
        }

        .student-info {
            margin: 10px 0;
            background-color: #f5f5f5;
            padding: 8px;
            border-radius: 5px;
        }

        .info-row {
            margin-bottom: 3px;
        }

        .info-label {
            font-weight: {{ setting('report_card_heading_font_weight', 'bold') }};
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: {{ setting('report_card_color_theme', '#0066cc') }};
            color: white;
            font-weight: {{ setting('report_card_heading_font_weight', 'bold') }};
        }

        .grade-A,
        .grade-A-plus {
            background-color: #d4edda;
        }

        .grade-B,
        .grade-B-plus {
            background-color: #d1ecf1;
        }

        .grade-C,
        .grade-C-plus {
            background-color: #fff3cd;
        }

        .grade-D,
        .grade-D-plus {
            background-color: #f8d7da;
        }

        .grade-F {
            background-color: #f5c6cb;
        }

        .summary {
            margin: 10px 0;
            padding: 8px;
            background-color: #e7f3ff;
            border-left: 4px solid {{ setting('report_card_color_theme', '#0066cc') }};
        }

        .summary-item {
            display: inline-block;
            width: 48%;
            margin-bottom: 3px;
            font-size: {{ setting('report_card_font_size', 11) + 1 }}px;
        }

        .summary-item strong {
            font-weight: {{ setting('report_card_heading_font_weight', 'bold') }};
        }

        .comments {
            margin: 10px 0;
        }

        .comment-box {
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 5px;
            background-color: #fafafa;
        }

        .comment-title {
            font-weight: {{ setting('report_card_heading_font_weight', 'bold') }};
            color: {{ setting('report_card_color_theme', '#0066cc') }};
            margin-bottom: 3px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: {{ setting('report_card_font_size', 11) - 2 }}px;
            color: #666;
        }

        .signature-line {
            margin-top: 20px;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line-text {
            border-top: 1px solid #333;
            padding-top: 3px;
            margin-top: 30px;
            width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    @foreach ($reports as $index => $student_data)
        <div class="header">
            @php
                $logoPath = setting('school_logo');
                $logoFile = $logoPath ? storage_path('app/public/' . $logoPath) : null;
            @endphp
            @if (setting('report_card_show_logo', true) && $logoFile && file_exists($logoFile))
                <div style="margin-bottom: 5px;">
                    <img src="{{ $logoFile }}" alt="School Logo"
                        style="max-height: {{ setting('report_card_logo_height', 100) }}px; max-width: {{ setting('report_card_logo_width', 200) }}px;">
                </div>
            @endif
            <div class="school-name">{{ setting('report_card_school_name', $school->name) }}</div>
            <div>{!! nl2br(
                e(
                    setting(
                        'report_card_address',
                        setting('school_address') . "\nTel: " . setting('school_phone') . ' | Email: ' . setting('school_email'),
                    ),
                ),
            ) !!}</div>
            <div class="report-title">STUDENT REPORT CARD</div>
            <div>Academic Year: {{ $academic_year }} | {{ $term }}</div>
        </div>

        <div class="student-info">
            <table style="width: 100%; border: none; margin: 0;">
                <tr>
                    <td style="border: none; vertical-align: top; padding: 0;">
                        <div class="info-row">
                            <span class="info-label">Student Name:</span>
                            <span>{{ $student_data['student']->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Student ID:</span>
                            <span>{{ $student_data['student']->id }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span>{{ $student_data['student']->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Class Rank:</span>
                            <span>{{ $student_data['class_rank'] }} of {{ $student_data['total_students'] }}</span>
                        </div>
                    </td>
                    <td
                        style="border: none; width: {{ setting('report_card_photo_width', 80) + 10 }}px; text-align: right; vertical-align: top; padding: 0;">
                        @php
                            $student = $student_data['student'];
                            $photoPath = $student->profile_photo
                                ? storage_path('app/public/' . $student->profile_photo)
                                : null;
                        @endphp
                        @if ($photoPath && file_exists($photoPath))
                            <img src="{{ $photoPath }}" alt="Student Photo"
                                style="width: {{ setting('report_card_photo_width', 80) }}px; height: {{ setting('report_card_photo_height', 80) }}px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                        @else
                            <div
                                style="width: {{ setting('report_card_photo_width', 80) }}px; height: {{ setting('report_card_photo_height', 80) }}px; background-color: #eee; border: 1px solid #ddd; border-radius: 5px; display: inline-block; text-align: center; line-height: {{ setting('report_card_photo_height', 80) }}px; color: #999; font-size: 10px;">
                                No Photo</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        @php
            $assessmentColumns = $student_data['assessment_columns'] ?? [];
            $assessmentLabels = $student_data['assessment_labels'] ?? [];
        @endphp
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    @forelse($assessmentColumns as $code)
                        <th style="text-align: center;">{{ $assessmentLabels[$code] ?? $code }}</th>
                    @empty
                        <th style="text-align: center;">Mark</th>
                        <th style="text-align: center;">Out Of</th>
                    @endforelse
                    <th style="text-align: center;">Average</th>
                    <th style="text-align: center;">Grade</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($student_data['grades'] as $grade)
                    <tr class="grade-{{ str_replace('+', '-plus', $grade['grade']) }}">
                        <td>{{ $grade['subject'] }}</td>
                        @forelse($assessmentColumns as $code)
                            @php $value = $grade['assessments'][$code] ?? null; @endphp
                            <td style="text-align: center;">{{ $value !== null ? $value . '%' : '—' }}</td>
                        @empty
                            <td style="text-align: center;">{{ $grade['mark'] }}</td>
                            <td style="text-align: center;">{{ $grade['out_of'] }}</td>
                        @endforelse
                        <td style="text-align: center;">{{ $grade['mark'] }}%</td>
                        <td style="text-align: center;"><strong>{{ $grade['grade'] }}</strong></td>
                        <td>{{ $grade['comment'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-item">
                <span><strong>Total Marks:</strong></span>
                <span>{{ $student_data['total_marks'] }} / {{ $student_data['total_possible'] }}</span>
            </div>
            <div class="summary-item">
                <span><strong>Overall Percentage:</strong></span>
                <span>{{ $student_data['percentage'] }}%</span>
            </div>
            <div class="summary-item">
                <span><strong>GPA:</strong></span>
                <span>{{ number_format($student_data['gpa'], 2) }} / 4.00</span>
            </div>
            <div class="summary-item">
                <span><strong>Attendance:</strong></span>
                <span>Present: {{ $student_data['attendance']['present'] }} | Absent:
                    {{ $student_data['attendance']['absent'] }} | Late:
                    {{ $student_data['attendance']['late'] }}</span>
            </div>
        </div>

        <div class="comments">
            <div class="comment-box">
                <div class="comment-title">Class Teacher's Comment:</div>
                <div>{{ $student_data['teacher_comment'] }}</div>
            </div>

            <div class="comment-box">
                <div class="comment-title">Principal's Comment:</div>
                <div>{{ $student_data['principal_comment'] }}</div>
            </div>
        </div>

        <div class="signature-line">
            <div class="signature-box">
                <div class="signature-line-text">{{ setting('report_card_signature_1', 'Class Teacher') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line-text">{{ setting('report_card_signature_2', 'Principal') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line-text">{{ setting('report_card_signature_3', 'Parent/Guardian') }}</div>
            </div>
        </div>

        <div class="footer">
            <p>Generated on {{ $generated_at }}</p>
            <p>&copy; {{ now()->year }} {{ $school->name }}. All rights reserved.</p>
            <p><em>This is an official document. Any alteration will render it invalid.</em></p>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
