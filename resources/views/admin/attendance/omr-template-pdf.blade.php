<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sheet-info {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f0f0f0;
            padding: 5px;
            border: 1px solid #000;
            font-weight: bold;
            text-align: left;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #000;
        }

        .bubble {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 2px solid #000;
            border-radius: 50%;
            margin: 0 3px;
        }

        .row-num {
            width: 30px;
            text-align: center;
        }

        .student-name {
            width: 200px;
        }

        .photo-cell {
            width: 40px;
            text-align: center;
        }

        .bubbles-cell {
            width: 120px;
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            font-size: 9pt;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .legend {
            margin-top: 10px;
        }

        .instructions {
            background: #f9f9f9;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="school-name">{{ $school->name }}</div>
        <div>{{ $title }}</div>
    </div>

    <!-- Sheet Info -->
    <div class="sheet-info">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 50%;"><strong>Class:</strong> {{ $class->name }}</td>
                <td style="border: none; width: 50%;"><strong>Date:</strong> {{ $date->format('l, F d, Y') }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Total Students:</strong> {{ $students->count() }}</td>
                <td style="border: none;"><strong>Teacher Signature:</strong> _________________</td>
            </tr>
        </table>
    </div>

    <!-- Attendance Table -->
    <table>
        <thead>
            <tr>
                <th class="row-num">#</th>
                @if ($include_photos)
                    <th class="photo-cell">Photo</th>
                @endif
                <th class="student-name">Student Name</th>
                <th style="text-align: center; width: 80px;">Present</th>
                <th style="text-align: center; width: 80px;">Absent</th>
                <th style="text-align: center; width: 80px;">Late</th>
                <th style="text-align: center; width: 80px;">Excused</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $student)
                <tr>
                    <td class="row-num">{{ $index + 1 }}</td>
                    @if ($include_photos)
                        <td class="photo-cell">
                            @if ($student->photo)
                                <img src="{{ public_path($student->photo) }}"
                                    style="width: 30px; height: 30px; border-radius: 50%;">
                            @endif
                        </td>
                    @endif
                    <td>{{ $student->name }}</td>
                    <td class="bubbles-cell"><span class="bubble"></span></td>
                    <td class="bubbles-cell"><span class="bubble"></span></td>
                    <td class="bubbles-cell"><span class="bubble"></span></td>
                    <td class="bubbles-cell"><span class="bubble"></span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div class="legend">
            <strong>Legend:</strong> Fill the appropriate bubble completely with dark ink or pencil.
            <strong>P</strong> = Present | <strong>A</strong> = Absent | <strong>L</strong> = Late | <strong>E</strong>
            = Excused
        </div>

        <div class="instructions">
            <strong>Scanning Instructions:</strong>
            <ol style="margin: 5px 0; padding-left: 20px;">
                <li>Fill bubbles completely with dark pen or pencil</li>
                <li>Keep sheet clean and flat</li>
                <li>Scan at 300 DPI or higher</li>
                <li>Upload scanned image to optical scanner in the attendance system</li>
            </ol>
        </div>

        <div style="margin-top: 15px; font-size: 8pt; color: #666;">
            Generated by {{ $school->name }} Attendance System on {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>
</body>

</html>
