@extends('layouts.dashboard-teacher')

@section('title', $quiz->title)

@include('components.wysiwyg')

@push('styles')
    <style>
        .modal .ck.ck-editor__editable_inline {
            min-height: 140px;
        }

        .modal .ck.ck-editor {
            z-index: 1055;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-puzzle me-2 text-primary"></i>{{ $quiz->title }}
                </h1>
                <p class="text-muted mb-0">
                    {{ $quiz->class->name }}
                    @if ($quiz->subject)
                        • {{ $quiz->subject->name }}
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.quizzes.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
                <div class="btn-group">
                    <a href="{{ route('tenant.teacher.classroom.quizzes.edit', $quiz) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <form action="{{ route('tenant.teacher.classroom.quizzes.destroy', $quiz) }}" method="POST"
                        class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Quiz Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Description</h5>
                                @if ($quiz->description)
                                    <div class="card-text">{!! $quiz->description !!}</div>
                                @else
                                    <p class="card-text text-muted">{{ __('No description provided.') }}</p>
                                @endif

                                <h6 class="mt-3">Instructions</h6>
                                @if ($quiz->instructions)
                                    <div class="card-text text-muted">{!! $quiz->instructions !!}</div>
                                @else
                                    <p class="card-text text-muted">{{ __('No specific instructions.') }}</p>
                                @endif
                            </div>
                            <div class="text-end">
                                <span
                                    class="badge bg-{{ $quiz->status === 'published' ? 'success' : ($quiz->status === 'draft' ? 'warning' : 'secondary') }} mb-2">
                                    {{ ucfirst($quiz->status) }}
                                </span>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' mins' : 'Unlimited' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-trophy me-1"></i> {{ $quiz->total_marks }} Marks
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Questions ({{ $quiz->questions->count() }})</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addQuestionModal">
                        <i class="bi bi-plus-lg me-1"></i>Add Question
                    </button>
                </div>

                @forelse($quiz->questions as $index => $question)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Q{{ $index + 1 }}: {{ $question->type_label }}</span>
                            <div>
                                <span class="badge bg-light text-dark me-2">{{ $question->marks }} Marks</span>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 me-2"
                                    data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}"
                                    title="Edit Question">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form
                                    action="{{ route('tenant.teacher.classroom.quizzes.questions.destroy', ['quiz' => $quiz->id, 'question' => $question->id]) }}"
                                    method="POST" class="d-inline" onsubmit="return confirm('Delete this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold mb-3">{!! $question->question !!}</p>

                            @if ($question->type === 'multiple_choice' && $question->options)
                                <ul class="list-group mb-3">
                                    @foreach ($question->options as $key => $option)
                                        <li
                                            class="list-group-item {{ $question->correct_answer === $key ? 'list-group-item-success' : '' }}">
                                            <span class="fw-bold me-2">{{ $key }}.</span> {{ $option }}
                                            @if ($question->correct_answer === $key)
                                                <i class="bi bi-check-circle-fill float-end text-success"></i>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif($question->type === 'true_false')
                                <div class="alert alert-light border">
                                    <strong>Correct Answer:</strong>
                                    {{ $question->correct_answer === 'true' ? 'True' : 'False' }}
                                </div>
                            @endif

                            @if ($question->explanation)
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Explanation:</strong> {!! $question->explanation !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Edit Question Modal for this question -->
                    <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Question {{ $index + 1 }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form
                                    action="{{ route('tenant.teacher.classroom.quizzes.questions.update', ['quiz' => $quiz->id, 'question' => $question->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Question Type</label>
                                            <select class="form-select edit-question-type" name="type"
                                                data-question-id="{{ $question->id }}" required>
                                                <option value="multiple_choice"
                                                    {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Multiple
                                                    Choice</option>
                                                <option value="true_false"
                                                    {{ $question->type === 'true_false' ? 'selected' : '' }}>True/False
                                                </option>
                                                <option value="short_answer"
                                                    {{ $question->type === 'short_answer' ? 'selected' : '' }}>Short Answer
                                                </option>
                                                <option value="essay" {{ $question->type === 'essay' ? 'selected' : '' }}>
                                                    Essay</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Question Text</label>
                                            <textarea class="form-control wysiwyg-question" name="question" required data-editor-height="200"
                                                data-placeholder="Update the question text">{!! $question->question !!}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Marks</label>
                                            <input type="number" class="form-control" name="marks"
                                                value="{{ $question->marks }}" min="1" required>
                                        </div>

                                        <!-- Multiple Choice Options -->
                                        <div id="editMcOptions{{ $question->id }}"
                                            class="mb-3 {{ $question->type !== 'multiple_choice' ? 'd-none' : '' }}">
                                            <label class="form-label">Options (select the correct answer)</label>
                                            @php
                                                $options = $question->options ?? [
                                                    'A' => '',
                                                    'B' => '',
                                                    'C' => '',
                                                    'D' => '',
                                                ];
                                            @endphp
                                            @foreach (['A', 'B', 'C', 'D'] as $key)
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">{{ $key }}</span>
                                                    <input type="text" class="form-control"
                                                        name="options[{{ $key }}]"
                                                        value="{{ $options[$key] ?? '' }}"
                                                        placeholder="Option {{ $key }}">
                                                    <div class="input-group-text">
                                                        <input class="form-check-input mt-0" type="radio"
                                                            name="correct_answer" value="{{ $key }}"
                                                            {{ $question->correct_answer === $key ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- True/False Options -->
                                        <div id="editTfOptions{{ $question->id }}"
                                            class="mb-3 {{ $question->type !== 'true_false' ? 'd-none' : '' }}">
                                            <label class="form-label">Correct Answer</label>
                                            <select class="form-select" name="correct_answer_tf">
                                                <option value="true"
                                                    {{ $question->correct_answer === 'true' ? 'selected' : '' }}>True
                                                </option>
                                                <option value="false"
                                                    {{ $question->correct_answer === 'false' ? 'selected' : '' }}>False
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Explanation (Optional)</label>
                                            <textarea class="form-control wysiwyg-explanation" name="explanation" data-editor-height="140"
                                                data-placeholder="Explain the correct answer">{!! $question->explanation !!}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Question</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 border rounded bg-light">
                        <i class="bi bi-question-circle display-4 text-muted mb-3 d-block"></i>
                        <p class="text-muted">No questions added yet.</p>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#addQuestionModal">
                            Add Your First Question
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Quiz Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <h3 class="mb-0">{{ $quiz->attempts->count() }}</h3>
                                <small class="text-muted">Attempts</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h3 class="mb-0">{{ $quiz->questions->sum('marks') }}</h3>
                                <small class="text-muted">Total Marks</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Settings Summary</h6>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Shuffle Questions</span>
                            <i
                                class="bi bi-{{ $quiz->shuffle_questions ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Show Results</span>
                            <i
                                class="bi bi-{{ $quiz->show_results_immediately ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Max Attempts</span>
                            <span class="badge bg-secondary">{{ $quiz->max_attempts }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('tenant.teacher.classroom.quizzes.questions.store', $quiz) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Question Type</label>
                            <select class="form-select" name="type" id="questionType" required>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Question Text</label>
                            <textarea class="form-control wysiwyg-question" name="question" required data-editor-height="200"
                                data-placeholder="Enter the question text..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marks</label>
                            <input type="number" class="form-control" name="marks" value="1" min="1"
                                required>
                        </div>

                        <!-- Multiple Choice Options -->
                        <div id="mcOptions" class="mb-3">
                            <label class="form-label">Options</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" class="form-control" name="options[A]" placeholder="Option A">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="A">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" class="form-control" name="options[B]" placeholder="Option B">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="B">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">C</span>
                                <input type="text" class="form-control" name="options[C]" placeholder="Option C">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="C">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">D</span>
                                <input type="text" class="form-control" name="options[D]" placeholder="Option D">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="D">
                                </div>
                            </div>
                        </div>

                        <!-- True/False Options -->
                        <div id="tfOptions" class="mb-3 d-none">
                            <label class="form-label">Correct Answer</label>
                            <select class="form-select" name="correct_answer_tf" id="tfSelect">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Explanation (Optional)</label>
                            <textarea class="form-control wysiwyg-explanation" name="explanation" data-editor-height="140"
                                data-placeholder="Explain why this is the correct answer"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initModalEditors = modal => {
                if (!modal) {
                    return;
                }
                const questionSelector = `#${modal.id} .wysiwyg-question`;
                const explanationSelector = `#${modal.id} .wysiwyg-explanation`;
                window.initWysiwygEditors(questionSelector);
                window.initWysiwygEditors(explanationSelector);
            };

            const addQuestionModal = document.getElementById('addQuestionModal');
            if (addQuestionModal) {
                addQuestionModal.addEventListener('shown.bs.modal', function() {
                    initModalEditors(addQuestionModal);
                });
            }

            document.querySelectorAll('[id^="editQuestionModal"]').forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    initModalEditors(modal);
                });
            });
        });

        const questionTypeSelect = document.getElementById('questionType');
        if (questionTypeSelect) {
            questionTypeSelect.addEventListener('change', function() {
                const type = this.value;
                const mcOptions = document.getElementById('mcOptions');
                const tfOptions = document.getElementById('tfOptions');
                const tfSelect = document.getElementById('tfSelect');

                if (type === 'multiple_choice') {
                    mcOptions.classList.remove('d-none');
                    tfOptions.classList.add('d-none');
                    tfSelect.name = 'correct_answer_tf';
                } else if (type === 'true_false') {
                    mcOptions.classList.add('d-none');
                    tfOptions.classList.remove('d-none');
                    tfSelect.name = 'correct_answer';
                } else {
                    mcOptions.classList.add('d-none');
                    tfOptions.classList.add('d-none');
                    tfSelect.name = 'correct_answer_tf';
                }
            });
        }

        // Handle edit question type changes
        document.querySelectorAll('.edit-question-type').forEach(function(select) {
            select.addEventListener('change', function() {
                const questionId = this.dataset.questionId;
                const type = this.value;
                const mcOptions = document.getElementById('editMcOptions' + questionId);
                const tfOptions = document.getElementById('editTfOptions' + questionId);

                if (type === 'multiple_choice') {
                    mcOptions.classList.remove('d-none');
                    tfOptions.classList.add('d-none');
                } else if (type === 'true_false') {
                    mcOptions.classList.add('d-none');
                    tfOptions.classList.remove('d-none');
                } else {
                    mcOptions.classList.add('d-none');
                    tfOptions.classList.add('d-none');
                }
            });
        });
    </script>
@endpush
