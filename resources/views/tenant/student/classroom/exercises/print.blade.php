<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $exercise->title }} - Print View</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 24px;
        }

        .print-meta span {
            display: inline-block;
            min-width: 140px;
            font-weight: 600;
        }

        .wysiwyg-output {
            border: 1px solid #dee2e6;
            padding: 16px;
            border-radius: 6px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="no-print mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h4 mb-1">{{ $exercise->title }}</h1>
            <div class="text-muted">Prepared for {{ $student->name }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <a href="{{ route('tenant.student.classroom.exercises.download-pdf', $exercise) }}"
                class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="mb-3">
        <div class="print-meta"><span>Class:</span> {{ $exercise->class->name ?? 'N/A' }}</div><br>
        <div class="print-meta"><span>Subject:</span> {{ $exercise->subject->name ?? 'N/A' }}</div><br>
        <div class="print-meta"><span>Teacher:</span> {{ $exercise->teacher->name ?? 'N/A' }}</div><br>
        <div class="print-meta"><span>Due Date:</span> {{ optional($exercise->due_date)->format('M d, Y h:i A') }}</div>
        <br>
        <div class="print-meta"><span>Max Score:</span> {{ $exercise->max_score ?? 'N/A' }}</div><br>
        <div class="print-meta"><span>Late Submission:</span>
            {{ $exercise->allow_late_submission ? 'Allowed' : 'Not Allowed' }}</div>
    </div>

    @if ($exercise->description)
        <h5>Description</h5>
        <div class="wysiwyg-output mb-4">{!! $exercise->description !!}</div>
    @endif

    <h5>Instructions</h5>
    <div class="wysiwyg-output mb-4">
        @if ($exercise->instructions)
            {!! $exercise->instructions !!}
        @else
            <p>No specific instructions provided.</p>
        @endif
    </div>

    <h5>Assignment Content</h5>
    <div class="wysiwyg-output mb-4">
        @if ($exercise->content)
            {!! $exercise->content !!}
        @else
            <p>The teacher has not added detailed content.</p>
        @endif
    </div>

    @if ($exercise->attachment_path)
        <div class="mb-4">
            <h5>Attachment</h5>
            <p>{{ basename($exercise->attachment_path) }} @if ($exercise->attachment_size)
                    ({{ $exercise->attachment_size }})
                @endif
            </p>
        </div>
    @endif

    @if ($submission)
        <h5>Your Submission</h5>
        <p><strong>Submitted:</strong> {{ optional($submission->submitted_at)->format('M d, Y h:i A') }}</p>
        @if ($submission->grade !== null)
            <p><strong>Score:</strong> {{ $submission->score }}/{{ $exercise->max_score }}
                ({{ $submission->grade }}%)</p>
        @endif
        @if ($submission->submission_text)
            <div class="wysiwyg-output mb-3">{!! $submission->submission_text !!}</div>
        @endif
        @if ($submission->feedback)
            <h6>Teacher Feedback</h6>
            <div class="wysiwyg-output">{!! $submission->feedback !!}</div>
        @endif
    @endif

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>

</html>
