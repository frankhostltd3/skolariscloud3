@extends('layouts.tenant.student')

@section('title', 'Taking Exam: ' . $exam->title)

@section('content')
    <div class="container-fluid">
        <!-- Exam Header with Timer -->
        <div class="card shadow-sm border-primary mb-4 sticky-top" style="top: 10px; z-index: 1000;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            <i class="bi bi-clipboard-check text-primary me-2"></i>{{ $exam->title }}
                        </h5>
                        <small class="text-muted">
                            {{ __('Total Points:') }} {{ $exam->total_points }}
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div id="timer-display" class="alert alert-warning mb-0 d-inline-block">
                            <i class="bi bi-clock me-2"></i>
                            <strong>{{ __('Time Remaining:') }}</strong>
                            <span id="timer-text" class="fs-5">--:--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Form -->
        <form id="exam-form" method="POST" action="{{ route('tenant.student.exams.submit', $exam->id) }}">
            @csrf

            @if ($exam->sections->count() > 0)
                @foreach ($exam->sections as $sectionIndex => $section)
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ $section->title }}</h5>
                            @if ($section->description)
                                <small class="text-muted">{{ $section->description }}</small>
                            @endif
                        </div>
                        <div class="card-body">
                            @if ($section->questions->count() > 0)
                                @foreach ($section->questions as $index => $question)
                                    <div class="mb-4 pb-3 border-bottom last-no-border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold">
                                                {{ __('Question') }} {{ $index + 1 }}
                                            </h6>
                                            <span class="badge bg-secondary">
                                                {{ $question->points }} {{ __('points') }}
                                            </span>
                                        </div>

                                        <!-- Question Text -->
                                        <div class="mb-3">
                                            <p class="mb-2">{!! nl2br(e($question->question_text)) !!}</p>
                                            @if ($question->question_image)
                                                <img src="{{ Storage::url($question->question_image) }}"
                                                    alt="Question Image" class="img-fluid rounded mb-3"
                                                    style="max-height: 300px;">
                                            @endif
                                        </div>

                                        <!-- Answer Input -->
                                        @php
                                            $savedAnswer = $attempt->answers[$question->id] ?? null;
                                        @endphp

                                        @if ($question->type === 'multiple_choice')
                                            @if ($question->options)
                                                @foreach ($question->options as $optionKey => $optionValue)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio"
                                                            name="answers[{{ $question->id }}]"
                                                            id="q{{ $question->id }}_option{{ $optionKey }}"
                                                            value="{{ $optionKey }}"
                                                            {{ $savedAnswer == $optionKey ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="q{{ $question->id }}_option{{ $optionKey }}">
                                                            {{ $optionValue }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @elseif($question->type === 'true_false')
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio"
                                                    name="answers[{{ $question->id }}]" id="q{{ $question->id }}_true"
                                                    value="true" {{ $savedAnswer == 'true' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="q{{ $question->id }}_true">
                                                    {{ __('True') }}
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio"
                                                    name="answers[{{ $question->id }}]" id="q{{ $question->id }}_false"
                                                    value="false" {{ $savedAnswer == 'false' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="q{{ $question->id }}_false">
                                                    {{ __('False') }}
                                                </label>
                                            </div>
                                        @elseif($question->type === 'short_answer')
                                            <input type="text" class="form-control" name="answers[{{ $question->id }}]"
                                                id="q{{ $question->id }}" value="{{ $savedAnswer }}"
                                                placeholder="{{ __('Type your answer here...') }}">
                                        @elseif($question->type === 'paragraph')
                                            <textarea class="form-control" name="answers[{{ $question->id }}]" id="q{{ $question->id }}" rows="4"
                                                placeholder="{{ __('Type your answer here...') }}">{{ $savedAnswer }}</textarea>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">{{ __('No questions in this section.') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    {{ __('No sections found for this exam.') }}
                </div>
            @endif

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                <button type="submit" class="btn btn-primary btn-lg"
                    onclick="return confirm('{{ __('Are you sure you want to submit your exam? You cannot change your answers after submission.') }}')">
                    <i class="bi bi-check-circle me-2"></i>{{ __('Submit Exam') }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Timer Logic
                const endTime = new Date("{{ $exam->end_at }}").getTime();
                const timerText = document.getElementById('timer-text');
                const timerDisplay = document.getElementById('timer-display');

                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance < 0) {
                        clearInterval(timerInterval);
                        timerText.innerHTML = "EXPIRED";
                        timerDisplay.classList.remove('alert-warning');
                        timerDisplay.classList.add('alert-danger');
                        // Auto submit
                        document.getElementById('exam-form').submit();
                        return;
                    }

                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    timerText.innerHTML =
                        (hours < 10 ? "0" + hours : hours) + ":" +
                        (minutes < 10 ? "0" + minutes : minutes) + ":" +
                        (seconds < 10 ? "0" + seconds : seconds);

                    // Warning color when less than 5 minutes
                    if (distance < 5 * 60 * 1000) {
                        timerDisplay.classList.remove('alert-warning');
                        timerDisplay.classList.add('alert-danger');
                    }
                }

                const timerInterval = setInterval(updateTimer, 1000);
                updateTimer(); // Initial call
            });
        </script>
    @endpush
@endsection
