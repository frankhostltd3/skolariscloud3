@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Taking Quiz: ' . $quiz->title)

@section('content')
<div class="container-fluid">
    <!-- Quiz Header with Timer -->
    <div class="card shadow-sm border-primary mb-4 sticky-top" style="top: 10px; z-index: 1000;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-1">
                        <i class="bi bi-clipboard-check text-primary me-2"></i>{{ $quiz->title }}
                    </h5>
                    <small class="text-muted">
                        {{ __('Total Points:') }} {{ $quiz->total_points ?? $quiz->questions->count() }}
                    </small>
                </div>
                <div class="col-md-4 text-md-end">
                    @if($remainingSeconds !== null)
                        <div id="timer-display" class="alert alert-warning mb-0 d-inline-block">
                            <i class="bi bi-clock me-2"></i>
                            <strong>{{ __('Time Remaining:') }}</strong>
                            <span id="timer-text" class="fs-5">--:--</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($isLateWindow)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ __('Note: You are submitting after the official deadline. This may be marked as a late submission.') }}
        </div>
    @endif

    <!-- Quiz Form -->
    <form id="quiz-form" method="POST" action="{{ route('tenant.student.quizzes.submit', $quiz->id) }}">
        @csrf

        @if($quiz->questions->count() > 0)
            @foreach($quiz->questions as $index => $question)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                {{ __('Question') }} {{ $index + 1 }} {{ __('of') }} {{ $quiz->questions->count() }}
                            </h6>
                            @if($question->pivot->points ?? false)
                                <span class="badge bg-primary">
                                    {{ $question->pivot->points }} {{ __('points') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Question Text -->
                        <div class="mb-3">
                            <p class="fw-bold mb-2">{!! nl2br(e($question->question_text)) !!}</p>
                            @if($question->question_image)
                                <img src="{{ Storage::url($question->question_image) }}" 
                                     alt="Question Image" 
                                     class="img-fluid rounded mb-3"
                                     style="max-height: 300px;">
                            @endif
                        </div>

                        <!-- Answer Input -->
                        @php
                            $savedAnswer = $attempt->answers[$question->id] ?? null;
                        @endphp

                        @if($question->type === 'multiple_choice')
                            @if($question->options)
                                @foreach($question->options as $optionKey => $optionValue)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="q{{ $question->id }}_option{{ $optionKey }}"
                                               value="{{ $optionKey }}"
                                               {{ $savedAnswer == $optionKey ? 'checked' : '' }}>
                                        <label class="form-check-label" for="q{{ $question->id }}_option{{ $optionKey }}">
                                            {{ $optionValue }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif

                        @elseif($question->type === 'true_false')
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       id="q{{ $question->id }}_true"
                                       value="true"
                                       {{ $savedAnswer == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="q{{ $question->id }}_true">
                                    {{ __('True') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       id="q{{ $question->id }}_false"
                                       value="false"
                                       {{ $savedAnswer == 'false' ? 'checked' : '' }}>
                                <label class="form-check-label" for="q{{ $question->id }}_false">
                                    {{ __('False') }}
                                </label>
                            </div>

                        @elseif($question->type === 'short_answer')
                            <input type="text" 
                                   class="form-control" 
                                   name="answers[{{ $question->id }}]"
                                   placeholder="{{ __('Type your answer here...') }}"
                                   value="{{ $savedAnswer }}">

                        @elseif($question->type === 'essay')
                            <textarea class="form-control" 
                                      name="answers[{{ $question->id }}]" 
                                      rows="5"
                                      placeholder="{{ __('Type your answer here...') }}">{{ $savedAnswer }}</textarea>

                        @else
                            <div class="alert alert-warning">
                                {{ __('Unknown question type:') }} {{ $question->type }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Submit Section -->
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                {{ __('Ready to Submit?') }}
                            </h6>
                            <p class="small text-muted mb-0">
                                {{ __('Please review all your answers before submitting. You cannot change your answers after submission.') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button type="button" 
                                    class="btn btn-success btn-lg" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#submitConfirmModal">
                                <i class="bi bi-send me-2"></i>{{ __('Submit Quiz') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ __('This quiz has no questions yet.') }}
            </div>
        @endif
    </form>

    <!-- Progress Indicator -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h6 class="mb-3">{{ __('Progress') }}</h6>
            <div class="progress" style="height: 25px;">
                <div id="progress-bar" 
                     class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%">
                    <span id="progress-text">0%</span>
                </div>
            </div>
            <p class="small text-muted mt-2 mb-0">
                <span id="answered-count">0</span> {{ __('of') }} {{ $quiz->questions->count() }} {{ __('questions answered') }}
            </p>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Confirm Submission') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">{{ __('Are you sure you want to submit your quiz?') }}</p>
                <div class="alert alert-warning mb-0">
                    <ul class="mb-0">
                        <li>{{ __('You cannot change your answers after submission') }}</li>
                        <li>{{ __('Make sure you have answered all questions') }}</li>
                        <li id="unanswered-warning" class="text-danger d-none">
                            <strong>{{ __('Warning:') }}</strong> 
                            <span id="unanswered-count"></span> {{ __('question(s) are not answered yet!') }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                </button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('quiz-form').submit()">
                    <i class="bi bi-send me-2"></i>{{ __('Yes, Submit Quiz') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalQuestions = {{ $quiz->questions->count() }};
    const form = document.getElementById('quiz-form');
    
    // Timer functionality
    @if($remainingSeconds !== null)
        let remainingSeconds = {{ $remainingSeconds }};
        const timerText = document.getElementById('timer-text');
        const timerDisplay = document.getElementById('timer-display');
        
        function updateTimer() {
            if (remainingSeconds <= 0) {
                // Auto-submit when time runs out
                alert('{{ __("Time's up! The quiz will be submitted automatically.") }}');
                form.submit();
                return;
            }
            
            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;
            
            let display = '';
            if (hours > 0) {
                display = `${hours}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            } else {
                display = `${minutes}:${String(seconds).padStart(2, '0')}`;
            }
            
            timerText.textContent = display;
            
            // Change color when time is running out
            if (remainingSeconds <= 300) { // 5 minutes
                timerDisplay.classList.remove('alert-warning');
                timerDisplay.classList.add('alert-danger');
            }
            
            remainingSeconds--;
        }
        
        // Update immediately
        updateTimer();
        
        // Update every second
        setInterval(updateTimer, 1000);
    @endif
    
    // Progress tracking
    function updateProgress() {
        const inputs = form.querySelectorAll('input[type="radio"]:checked, input[type="text"][value!=""], textarea');
        const uniqueQuestions = new Set();
        
        inputs.forEach(input => {
            const name = input.name;
            if (name.startsWith('answers[')) {
                if (input.type === 'radio' && input.checked) {
                    uniqueQuestions.add(name);
                } else if (input.type === 'text' && input.value.trim() !== '') {
                    uniqueQuestions.add(name);
                } else if (input.tagName === 'TEXTAREA' && input.value.trim() !== '') {
                    uniqueQuestions.add(name);
                }
            }
        });
        
        const answeredCount = uniqueQuestions.size;
        const percentage = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;
        
        document.getElementById('progress-bar').style.width = percentage + '%';
        document.getElementById('progress-text').textContent = percentage + '%';
        document.getElementById('answered-count').textContent = answeredCount;
        
        return answeredCount;
    }
    
    // Update progress on any input change
    form.addEventListener('change', updateProgress);
    form.addEventListener('input', updateProgress);
    
    // Initial progress update
    updateProgress();
    
    // Submit confirmation with unanswered questions warning
    const submitModal = document.getElementById('submitConfirmModal');
    submitModal.addEventListener('show.bs.modal', function() {
        const answeredCount = updateProgress();
        const unansweredCount = totalQuestions - answeredCount;
        
        if (unansweredCount > 0) {
            document.getElementById('unanswered-warning').classList.remove('d-none');
            document.getElementById('unanswered-count').textContent = unansweredCount;
        } else {
            document.getElementById('unanswered-warning').classList.add('d-none');
        }
    });
    
    // Warn before leaving page
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '{{ __("Are you sure you want to leave? Your progress may be lost.") }}';
        return e.returnValue;
    });
    
    // Remove warning when submitting
    form.addEventListener('submit', function() {
        window.removeEventListener('beforeunload', arguments.callee);
    });
    
    // Auto-save to localStorage (optional enhancement)
    function autoSave() {
        const formData = new FormData(form);
        const answers = {};
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('answers[')) {
                answers[key] = value;
            }
        }
        localStorage.setItem('quiz_{{ $quiz->id }}_autosave', JSON.stringify(answers));
    }
    
    // Auto-save every 30 seconds
    setInterval(autoSave, 30000);
    
    // Clear auto-save on submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('quiz_{{ $quiz->id }}_autosave');
    });
});
</script>
@endsection

