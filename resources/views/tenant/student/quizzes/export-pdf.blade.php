<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $quiz->title }} - Quiz PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 16px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        td,
        th {
            border: 1px solid #ddd;
            padding: 6px 8px;
        }

        .section {
            margin-bottom: 18px;
        }

        .question {
            margin-bottom: 14px;
        }

        .question strong {
            display: block;
            margin-bottom: 6px;
        }

        .small {
            font-size: 11px;
            color: #555;
        }
    </style>
</head>

<body>
    <h1>{{ $quiz->title }}</h1>
    <p class="small">Generated for {{ $student->name }} on {{ now()->format('M d, Y h:i A') }}</p>

    <table>
        <tr>
            <td><strong>Teacher:</strong> {{ $quiz->teacher->name ?? 'N/A' }}</td>
            <td><strong>Subject:</strong> {{ $quiz->subject->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Class:</strong> {{ $quiz->class->name ?? 'Multiple' }}</td>
            <td><strong>Total Questions:</strong> {{ $quiz->questions->count() }}</td>
        </tr>
        <tr>
            <td><strong>Total Points:</strong> {{ $quiz->total_points ?? ($quiz->questions->sum('marks') ?: 'N/A') }}
            </td>
            <td><strong>Duration:</strong>
                {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' minutes' : 'Un-timed' }}</td>
        </tr>
        <tr>
            <td><strong>Opens:</strong> {{ optional($quiz->start_at)->format('M d, Y h:i A') ?? 'Immediate' }}</td>
            <td><strong>Closes:</strong>
                {{ optional($quiz->end_at)->format('M d, Y h:i A') ?? 'Until further notice' }}</td>
        </tr>
    </table>

    @if ($quiz->description)
        <div class="section">
            <h2>Description</h2>
            <p>{!! nl2br(e($quiz->description)) !!}</p>
        </div>
    @endif

    <div class="section">
        <h2>Instructions</h2>
        <ul>
            <li>Read every question carefully before answering.</li>
            @if ($quiz->duration_minutes)
                <li>You have {{ $quiz->duration_minutes }} minutes to complete this quiz.</li>
            @endif
            <li>Ensure a stable internet connection if taking online.</li>
            <li>Answers cannot be changed after submission.</li>
        </ul>
    </div>

    <div class="section">
        <h2>Questions Overview</h2>
        @forelse($quiz->questions as $index => $question)
            <div class="question">
                <strong>Q{{ $index + 1 }}. {{ $question->question }}</strong>
                <div class="small">Type: {{ str_replace('_', ' ', ucfirst($question->type)) }} | Marks:
                    {{ $question->marks }}</div>
                @if ($question->type === 'multiple_choice' && $question->options)
                    <ol>
                        @foreach ($question->options as $option)
                            <li>{{ $option }}</li>
                        @endforeach
                    </ol>
                @endif
                @if ($question->type === 'true_false')
                    <p>Options: True / False</p>
                @endif
            </div>
        @empty
            <p>No questions have been added to this quiz.</p>
        @endforelse
    </div>

    @if ($attempt)
        <div class="section">
            <h2>Your Attempt</h2>
            <table>
                <tr>
                    <td><strong>Status:</strong> {{ $attempt->submitted_at ? 'Submitted' : 'In Progress' }}</td>
                    <td><strong>Started:</strong>
                        {{ optional($attempt->started_at)->format('M d, Y h:i A') ?? 'Not started' }}</td>
                </tr>
                <tr>
                    <td><strong>Submitted:</strong>
                        {{ optional($attempt->submitted_at)->format('M d, Y h:i A') ?? 'N/A' }}</td>
                    <td><strong>Score:</strong>
                        @if ($attempt->score !== null)
                            {{ $attempt->score }}/{{ $quiz->total_points ?? ($quiz->questions->sum('marks') ?: 0) }}
                        @else
                            Pending
                        @endif
                    </td>
                </tr>
            </table>
            @if ($attempt->feedback)
                <div class="section">
                    <h3>Teacher Feedback</h3>
                    <p>{!! nl2br(e($attempt->feedback)) !!}</p>
                </div>
            @endif
        </div>
    @endif
</body>

</html>
