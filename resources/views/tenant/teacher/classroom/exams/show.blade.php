@extends('layouts.dashboard-teacher')

@section('title', $exam->title)

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-file-earmark-check me-2 text-primary"></i>{{ $exam->title }}
                </h1>
                <p class="text-muted mb-0">
                    {{ $exam->class->name }}
                    @if ($exam->subject)
                        • {{ $exam->subject->name }}
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('tenant.teacher.classroom.exams.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
                <div class="btn-group">
                    <a href="{{ route('tenant.teacher.classroom.exams.edit', $exam) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    @php
                        $canSubmitForReview = !in_array($exam->approval_status, ['pending_review', 'approved']);
                        $submitLabel =
                            $exam->approval_status === 'changes_requested'
                                ? 'Resubmit for Review'
                                : 'Submit for Review';
                    @endphp
                    @if ($canSubmitForReview)
                        <form action="{{ route('tenant.teacher.classroom.exams.publish', $exam) }}" method="POST"
                            class="d-inline" onsubmit="return confirm('Submit this exam for admin review now?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-send-check me-2"></i>{{ $submitLabel }}
                            </button>
                        </form>
                    @else
                        @if ($exam->approval_status === 'pending_review')
                            <span class="badge bg-info align-self-center ms-2">Pending Admin Review</span>
                        @elseif ($exam->approval_status === 'approved')
                            <span class="badge bg-success align-self-center ms-2">Approved</span>
                        @endif
                    @endif
                    <form action="{{ route('tenant.teacher.classroom.exams.destroy', $exam) }}" method="POST"
                        class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam?');">
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please check the form for errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($exam->approval_status === 'pending_review')
            <div class="alert alert-info d-flex align-items-start" role="alert">
                <i class="bi bi-hourglass-split me-2"></i>
                <div>
                    This exam is waiting for admin approval. You will be notified once it is reviewed.
                </div>
            </div>
        @elseif ($exam->approval_status === 'changes_requested')
            <div class="alert alert-warning d-flex align-items-start" role="alert">
                <i class="bi bi-exclamation-diamond me-2"></i>
                <div>
                    Admins requested updates before approval.
                    @if ($exam->approval_notes)
                        <div class="mt-1 small">Feedback: {{ $exam->approval_notes }}</div>
                    @endif
                </div>
            </div>
        @elseif ($exam->approval_status === 'rejected')
            <div class="alert alert-danger d-flex align-items-start" role="alert">
                <i class="bi bi-x-octagon me-2"></i>
                <div>
                    The previous submission was rejected. Review the notes and resubmit when ready.
                    @if ($exam->approval_notes)
                        <div class="mt-1 small">Reason: {{ $exam->approval_notes }}</div>
                    @endif
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Exam Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Description</h5>
                                <p class="card-text">{{ $exam->description ?: 'No description provided.' }}</p>

                                <h6 class="mt-3">Instructions</h6>
                                <p class="card-text text-muted">{{ $exam->instructions ?: 'No specific instructions.' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span
                                    class="badge bg-{{ $exam->status === 'active' ? 'success' : ($exam->status === 'draft' ? 'warning' : 'secondary') }} mb-2">
                                    {{ ucfirst($exam->status) }}
                                </span>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i> {{ $exam->duration_minutes }} mins
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-trophy me-1"></i> {{ $exam->total_marks }} Marks
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sections & Questions -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Exam Content</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addSectionModal">
                        <i class="bi bi-plus-lg me-1"></i>Add Section
                    </button>
                </div>

                @forelse($exam->sections as $section)
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $section->title }}</h6>
                                @if ($section->description)
                                    <small class="text-muted">{{ $section->description }}</small>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                    onclick="openAddQuestionModal({{ $section->id }})">
                                    <i class="bi bi-plus-lg"></i> Add Question
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                    onclick="editSection({{ $section->id }}, '{{ $section->title }}', '{{ $section->description }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form
                                    action="{{ route('tenant.teacher.classroom.exams.sections.destroy', ['exam' => $exam->id, 'section' => $section->id]) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this section and all its questions?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if ($section->questions->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($section->questions as $index => $question)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="w-100">
                                                    <div class="d-flex justify-content-between">
                                                        <span
                                                            class="badge bg-light text-dark border mb-2">{{ $question->type_label }}</span>
                                                        <span class="badge bg-info text-dark mb-2">{{ $question->marks }}
                                                            Marks</span>
                                                    </div>
                                                    <p class="mb-2 fw-bold">{{ $question->question }}</p>

                                                    @if ($question->type === 'multiple_choice' && $question->options)
                                                        <ul class="list-unstyled ps-3 mb-2">
                                                            @foreach ($question->options as $key => $option)
                                                                <li
                                                                    class="{{ $question->correct_answer === $key ? 'text-success fw-bold' : 'text-muted' }}">
                                                                    {{ $key }}. {{ $option }}
                                                                    @if ($question->correct_answer === $key)
                                                                        <i class="bi bi-check-circle-fill ms-1"></i>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($question->type === 'true_false')
                                                        <div class="ps-3 mb-2 text-muted">
                                                            Correct: <span
                                                                class="fw-bold text-success">{{ $question->correct_answer === 'true' ? 'True' : 'False' }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ms-3 d-flex flex-column">
                                                    <button type="button"
                                                        class="btn btn-sm btn-link text-secondary p-0 mb-1"
                                                        onclick="editQuestion({{ $section->id }}, {{ $question->id }}, {{ json_encode($question) }})">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form
                                                        action="{{ route('tenant.teacher.classroom.exams.questions.destroy', ['exam' => $exam->id, 'section' => $section->id, 'question' => $question->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Delete this question?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-link text-danger p-0">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-question-circle mb-2 d-block h4"></i>
                                    No questions in this section yet.
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 border rounded bg-light">
                        <i class="bi bi-layers display-4 text-muted mb-3 d-block"></i>
                        <p class="text-muted">No sections added yet.</p>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#addSectionModal">
                            Add Your First Section
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Exam Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <h3 class="mb-0">{{ $stats['attempts'] ?? 0 }}</h3>
                                <small class="text-muted">Attempts</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h3 class="mb-0">
                                    {{ $exam->sections->sum(function ($s) {return $s->questions->sum('marks');}) }}</h3>
                                <small class="text-muted">Total Marks</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0">
                                    {{ $exam->sections->sum(function ($s) {return $s->questions->count();}) }}</h3>
                                <small class="text-muted">Questions</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0">{{ $exam->sections->count() }}</h3>
                                <small class="text-muted">Sections</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($exam->creation_method !== 'manual')
                    @php
                        $statusColors = [
                            'idle' => 'secondary',
                            'requested' => 'info',
                            'processing' => 'warning',
                            'completed' => 'success',
                            'failed' => 'danger',
                        ];
                        $generationBlocked = in_array($exam->generation_status, ['requested', 'processing'], true);
                        $lastRequest = data_get($exam->generation_metadata, 'last_request', []);
                        $selectedTypes = data_get($lastRequest, 'question_types', []);
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">Automation &amp; AI</h6>
                            <span
                                class="badge bg-{{ $statusColors[$exam->generation_status] ?? 'secondary' }} text-uppercase">
                                {{ ucfirst($exam->generation_status ?? 'idle') }}
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-2">
                                {{ __('Mode: :method · Provider: :provider', [
                                    'method' => ucfirst($exam->creation_method),
                                    'provider' => $exam->generation_provider ? ucfirst($exam->generation_provider) : __('Not configured'),
                                ]) }}
                            </p>

                            @if (!empty($lastRequest))
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-history text-primary me-2"></i>
                                        <div>
                                            @php
                                                $lastRequestedAt = data_get($lastRequest, 'requested_at');
                                                $lastRequestedLabel = $lastRequestedAt
                                                    ? \Carbon\Carbon::parse($lastRequestedAt)->diffForHumans()
                                                    : __('recently');
                                            @endphp
                                            <div class="fw-semibold">
                                                {{ __('Last request :time', ['time' => $lastRequestedLabel]) }}
                                            </div>
                                            @if (!empty($lastRequest['syllabus_topics']))
                                                <small
                                                    class="text-muted">{{ \Illuminate\Support\Str::limit($lastRequest['syllabus_topics'], 80) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('tenant.teacher.classroom.exams.generate', $exam) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Syllabus Focus</label>
                                    <textarea class="form-control" name="syllabus_topics" rows="2" placeholder="Topic list, one per line"
                                        @disabled($generationBlocked)>{{ old('syllabus_topics', data_get($lastRequest, 'syllabus_topics')) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Learning Objectives</label>
                                    <textarea class="form-control" name="learning_objectives" rows="2" placeholder="Key skills or outcomes"
                                        @disabled($generationBlocked)>{{ old('learning_objectives', data_get($lastRequest, 'learning_objectives')) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Difficulty</label>
                                    <select class="form-select" name="difficulty" @disabled($generationBlocked)>
                                        @php $difficulty = old('difficulty', data_get($lastRequest, 'difficulty', 'balanced')); @endphp
                                        <option value="foundation" {{ $difficulty === 'foundation' ? 'selected' : '' }}>
                                            Foundation</option>
                                        <option value="balanced" {{ $difficulty === 'balanced' ? 'selected' : '' }}>
                                            Balanced</option>
                                        <option value="advanced" {{ $difficulty === 'advanced' ? 'selected' : '' }}>
                                            Advanced</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Question Types</label>
                                    <div class="d-flex flex-wrap gap-2 small">
                                        @foreach (['multiple_choice' => 'Multiple Choice', 'true_false' => 'True/False', 'short_answer' => 'Short Answer', 'essay' => 'Essay'] as $value => $label)
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="question_types[]"
                                                    value="{{ $value }}"
                                                    {{ in_array($value, old('question_types', $selectedTypes ?? []), true) ? 'checked' : '' }}
                                                    @disabled($generationBlocked)>
                                                <span class="form-check-label">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" @disabled($generationBlocked)>
                                    @if ($generationBlocked)
                                        <span
                                            class="spinner-border spinner-border-sm me-2"></span>{{ __('Generation in progress') }}
                                    @else
                                        <i class="bi bi-magic me-2"></i>{{ __('Request Blueprint') }}
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Settings Summary</h6>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Proctoring</span>
                            <i
                                class="bi bi-{{ $exam->proctored ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Shuffle Questions</span>
                            <i
                                class="bi bi-{{ $exam->shuffle_questions ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Disable Copy/Paste</span>
                            <i
                                class="bi bi-{{ $exam->disable_copy_paste ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Max Tab Switches</span>
                            <span
                                class="badge bg-secondary">{{ $exam->max_tab_switches > 0 ? $exam->max_tab_switches : 'Unlimited' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Section Modal -->
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('tenant.teacher.classroom.exams.sections.store', $exam) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required
                                placeholder="e.g., Part A: Multiple Choice">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"
                                placeholder="Optional instructions for this section"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Section Modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSectionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="editSectionTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editSectionDescription" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Section</button>
                    </div>
                </form>
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
                <form id="addQuestionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Question Type</label>
                            <select class="form-select" name="type" id="questionType" required>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                                <option value="fill_blank">Fill in the Blank</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="question_text" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marks <span class="text-danger">*</span></label>
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
                            <textarea class="form-control" name="explanation" rows="2"
                                placeholder="Explain why this is the correct answer"></textarea>
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
    <!-- Edit Question Modal -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editQuestionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Question Type</label>
                            <select class="form-select" name="type" id="editQuestionType" required>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                                <option value="fill_blank">Fill in the Blank</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="question_text" id="editQuestionText" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="marks" id="editQuestionMarks"
                                min="1" required>
                        </div>

                        <!-- Multiple Choice Options -->
                        <div id="editMcOptions" class="mb-3">
                            <label class="form-label">Options</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" class="form-control" name="options[A]" id="editOptionA"
                                    placeholder="Option A">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="A" id="editCorrectA">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" class="form-control" name="options[B]" id="editOptionB"
                                    placeholder="Option B">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="B" id="editCorrectB">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">C</span>
                                <input type="text" class="form-control" name="options[C]" id="editOptionC"
                                    placeholder="Option C">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="C" id="editCorrectC">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">D</span>
                                <input type="text" class="form-control" name="options[D]" id="editOptionD"
                                    placeholder="Option D">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer"
                                        value="D" id="editCorrectD">
                                </div>
                            </div>
                        </div>

                        <!-- True/False Options -->
                        <div id="editTfOptions" class="mb-3 d-none">
                            <label class="form-label">Correct Answer</label>
                            <select class="form-select" name="correct_answer_tf" id="editTfSelect">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Explanation (Optional)</label>
                            <textarea class="form-control" name="explanation" id="editExplanation" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Section Management
        function editSection(id, title, description) {
            const form = document.getElementById('editSectionForm');
            const url =
                "{{ route('tenant.teacher.classroom.exams.sections.update', ['exam' => $exam->id, 'section' => 'SECTION_ID']) }}";
            form.action = url.replace('SECTION_ID', id);

            document.getElementById('editSectionTitle').value = title;
            document.getElementById('editSectionDescription').value = description;
            new bootstrap.Modal(document.getElementById('editSectionModal')).show();
        }

        // Question Management
        function openAddQuestionModal(sectionId) {
            const form = document.getElementById('addQuestionForm');
            const url =
                "{{ route('tenant.teacher.classroom.exams.questions.store', ['exam' => $exam->id, 'section' => 'SECTION_ID']) }}";
            form.action = url.replace('SECTION_ID', sectionId);

            // Reset form
            form.reset();

            // Trigger type change to set initial state
            const typeEvent = new Event('change');
            document.getElementById('questionType').dispatchEvent(typeEvent);

            new bootstrap.Modal(document.getElementById('addQuestionModal')).show();
        }

        function editQuestion(sectionId, questionId, question) {
            const form = document.getElementById('editQuestionForm');
            const url =
                "{{ route('tenant.teacher.classroom.exams.questions.update', ['exam' => $exam->id, 'section' => 'SECTION_ID', 'question' => 'QUESTION_ID']) }}";
            form.action = url.replace('SECTION_ID', sectionId).replace('QUESTION_ID', questionId);

            document.getElementById('editQuestionType').value = question.type;
            document.getElementById('editQuestionText').value = question.question;
            document.getElementById('editQuestionMarks').value = question.marks;
            document.getElementById('editExplanation').value = question.explanation || '';

            // Trigger type change to show correct fields
            const typeEvent = new Event('change');
            document.getElementById('editQuestionType').dispatchEvent(typeEvent);

            if (question.type === 'multiple_choice') {
                if (question.options) {
                    document.getElementById('editOptionA').value = question.options.A || '';
                    document.getElementById('editOptionB').value = question.options.B || '';
                    document.getElementById('editOptionC').value = question.options.C || '';
                    document.getElementById('editOptionD').value = question.options.D || '';
                }

                if (question.correct_answer) {
                    const radio = document.getElementById(`editCorrect${question.correct_answer}`);
                    if (radio) radio.checked = true;
                }
            } else if (question.type === 'true_false') {
                document.getElementById('editTfSelect').value = question.correct_answer;
            }

            new bootstrap.Modal(document.getElementById('editQuestionModal')).show();
        }

        // Question Type Toggle (Add)
        document.getElementById('questionType').addEventListener('change', function() {
            const type = this.value;
            const mcOptions = document.getElementById('mcOptions');
            const tfOptions = document.getElementById('tfOptions');
            const tfSelect = document.getElementById('tfSelect');

            // Get all inputs in MC options
            const mcInputs = mcOptions.querySelectorAll('input');

            if (type === 'multiple_choice') {
                mcOptions.classList.remove('d-none');
                tfOptions.classList.add('d-none');

                // Enable MC inputs
                mcInputs.forEach(input => input.disabled = false);

                // Disable TF select
                tfSelect.disabled = true;
                tfSelect.name = 'correct_answer_tf'; // Just in case
            } else if (type === 'true_false') {
                mcOptions.classList.add('d-none');
                tfOptions.classList.remove('d-none');

                // Disable MC inputs
                mcInputs.forEach(input => input.disabled = true);

                // Enable TF select
                tfSelect.disabled = false;
                tfSelect.name = 'correct_answer';
            } else {
                mcOptions.classList.add('d-none');
                tfOptions.classList.add('d-none');

                // Disable both
                mcInputs.forEach(input => input.disabled = true);
                tfSelect.disabled = true;
            }
        });

        // Question Type Toggle (Edit)
        document.getElementById('editQuestionType').addEventListener('change', function() {
            const type = this.value;
            const mcOptions = document.getElementById('editMcOptions');
            const tfOptions = document.getElementById('editTfOptions');
            const tfSelect = document.getElementById('editTfSelect');

            // Get all inputs in MC options
            const mcInputs = mcOptions.querySelectorAll('input');

            if (type === 'multiple_choice') {
                mcOptions.classList.remove('d-none');
                tfOptions.classList.add('d-none');

                mcInputs.forEach(input => input.disabled = false);
                tfSelect.disabled = true;
                tfSelect.name = 'correct_answer_tf';
            } else if (type === 'true_false') {
                mcOptions.classList.add('d-none');
                tfOptions.classList.remove('d-none');

                mcInputs.forEach(input => input.disabled = true);
                tfSelect.disabled = false;
                tfSelect.name = 'correct_answer';
            } else {
                mcOptions.classList.add('d-none');
                tfOptions.classList.add('d-none');

                mcInputs.forEach(input => input.disabled = true);
                tfSelect.disabled = true;
            }
        });
    </script>
@endpush
