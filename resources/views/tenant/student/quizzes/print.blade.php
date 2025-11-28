<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $quiz->title }} - Print View</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            padding: 24px;
        }

        .meta-line span {
            display: inline-block;
            min-width: 140px;
            font-weight: 600;
        }

        .question {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 12px;
            margin-bottom: 12px;
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
            <h1 class="h4 mb-1">{{ $quiz->title }}</h1>
            <div class="text-muted">Generated for {{ $student->name }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <a href="{{ route('tenant.student.quizzes.download-pdf', $quiz) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="mb-4">
        <div class="meta-line"><span>Teacher:</span> {{ $quiz->teacher->name ?? 'N/A' }}</div><br>
        <div class="meta-line"><span>Subject:</span> {{ $quiz->subject->name ?? 'N/A' }}</div><br>
        <div class="meta-line"><span>Class:</span> {{ $quiz->class->name ?? 'Multiple' }}</div><br>
        <div class="meta-line"><span>Total Questions:</span> {{ $quiz->questions->count() }}</div><br>
        <div class="meta-line"><span>Total Points:</span>
            {{ $quiz->total_points ?? ($quiz->questions->sum('marks') ?: 'N/A') }}</div><br>
        <div class="meta-line"><span>Duration:</span>
            {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' min' : 'Un-timed' }}</div><br>
        <div class="meta-line"><span>Opens:</span>
            {{ optional($quiz->start_at)->format('M d, Y h:i A') ?? 'Immediate' }}</div><br>
        <div class="meta-line"><span>Closes:</span>
            {{ optional($quiz->end_at)->format('M d, Y h:i A') ?? 'Until closed' }}</div>
    </div>

    @if ($quiz->description)
        <h5>Description</h5>
        <div class="border rounded p-3 mb-4">{!! nl2br(e($quiz->description)) !!}</div>
    @endif

    <h5>Instructions</h5>
    <ul>
        <li>Review all questions carefully.</li>
        @if ($quiz->duration_minutes)
            <li>You must finish within {{ $quiz->duration_minutes }} minutes.</li>
        @endif
        <li>Ensure you have a stable connection before starting.</li>
        <li>Answers are final after submission.</li>
    </ul>

    <h5 class="mt-4">Questions</h5>
    @forelse($quiz->questions as $index => $question)
        <div class="question">
            <strong>Q{{ $index + 1 }}. {{ $question->question }}</strong>
            <div class="text-muted">Type: {{ str_replace('_', ' ', ucfirst($question->type)) }} | Marks:
                {{ $question->marks }}</div>
            @if ($question->type === 'multiple_choice' && $question->options)
                <ol type="A" class="mt-2">
                    @foreach ($question->options as $option)
                        <li>{{ $option }}</li>
                    @endforeach
                </ol>
            @elseif($question->type === 'true_false')
                <p class="mb-0 mt-2">Options: True / False</p>
            @endif
        </div>
    @empty
        <p>No questions available.</p>
    @endforelse

    @if ($attempt)
        <div class="mt-4">
            <h5>Your Attempt</h5>
            <p><strong>Status:</strong> {{ $attempt->submitted_at ? 'Submitted' : 'In Progress' }}</p>
            <p><strong>Started:</strong> {{ optional($attempt->started_at)->format('M d, Y h:i A') ?? 'N/A' }}</p>
            <p><strong>Submitted:</strong> {{ optional($attempt->submitted_at)->format('M d, Y h:i A') ?? 'Pending' }}
            </p>
            @if ($attempt->score !== null)
                <p><strong>Score:</strong>
                    {{ $attempt->score }}/{{ $quiz->total_points ?? ($quiz->questions->sum('marks') ?: 0) }}</p>
            @endif
            @if ($attempt->feedback)
                <div class="border rounded p-3">
                    <h6 class="mb-2">Teacher Feedback</h6>
                    {!! nl2br(e($attempt->feedback)) !!}
                </div>
            @endif
        </div>
    @endif

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>

</html>
