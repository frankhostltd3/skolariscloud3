<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $exercise->title }} - Assignment</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .meta-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        h2 {
            margin-top: 24px;
            font-size: 18px;
        }

        .content {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 4px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 11px;
            border-radius: 4px;
            background: #f1f1f1;
        }

        .section {
            margin-bottom: 18px;
        }

        .small {
            font-size: 11px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $exercise->title }}</h1>
        <p class="small">Generated for {{ $student->name }} on {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Class:</strong> {{ $exercise->class->name ?? 'N/A' }}</td>
            <td><strong>Subject:</strong> {{ $exercise->subject->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Teacher:</strong> {{ $exercise->teacher->name ?? 'N/A' }}</td>
            <td><strong>Due Date:</strong> {{ optional($exercise->due_date)->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td><strong>Maximum Score:</strong> {{ $exercise->max_score ?? 'N/A' }}</td>
            <td><strong>Late Submission:</strong> {{ $exercise->allow_late_submission ? 'Allowed' : 'Not Allowed' }}
            </td>
        </tr>
    </table>

    @if ($exercise->description)
        <div class="section">
            <h2>Description</h2>
            <div class="content">{!! $exercise->description !!}</div>
        </div>
    @endif

    <div class="section">
        <h2>Instructions</h2>
        <div class="content">
            @if ($exercise->instructions)
                {!! $exercise->instructions !!}
            @else
                <p>No specific instructions provided.</p>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Assignment Content</h2>
        <div class="content">
            @if ($exercise->content)
                {!! $exercise->content !!}
            @else
                <p>The teacher has not added detailed content.</p>
            @endif
        </div>
    </div>

    @if ($exercise->attachment_path)
        <div class="section">
            <h2>Attachment</h2>
            <p>
                <span class="badge">{{ basename($exercise->attachment_path) }}</span>
                @if ($exercise->attachment_size)
                    <span class="small">({{ $exercise->attachment_size }})</span>
                @endif
            </p>
        </div>
    @endif

    @if ($submission)
        <div class="section">
            <h2>Your Submission</h2>
            <table class="meta-table">
                <tr>
                    <td><strong>Submitted:</strong> {{ optional($submission->submitted_at)->format('M d, Y h:i A') }}
                    </td>
                    <td><strong>Status:</strong> {{ $submission->grade !== null ? 'Graded' : 'Pending Review' }}</td>
                </tr>
                @if ($submission->grade !== null)
                    <tr>
                        <td><strong>Score:</strong> {{ $submission->score }}/{{ $exercise->max_score }}</td>
                        <td><strong>Grade:</strong> {{ $submission->grade }}%</td>
                    </tr>
                @endif
            </table>
            @if ($submission->submission_text)
                <div class="content">
                    {!! $submission->submission_text !!}
                </div>
            @endif
            @if ($submission->feedback)
                <div class="section">
                    <h3>Teacher Feedback</h3>
                    <div class="content">{!! $submission->feedback !!}</div>
                </div>
            @endif
        </div>
    @endif
</body>

</html>
