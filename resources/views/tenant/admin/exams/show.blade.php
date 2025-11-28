@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                @php
                    $statusColors = [
                        'approved' => 'success',
                        'pending_review' => 'info',
                        'changes_requested' => 'warning',
                        'rejected' => 'danger',
                        'draft' => 'secondary',
                    ];
                @endphp
                <span class="badge text-bg-{{ $statusColors[$exam->approval_status] ?? 'secondary' }} text-uppercase">
                    {{ str_replace('_', ' ', $exam->approval_status ?? 'draft') }}
                </span>
                <span
                    class="badge bg-light text-dark">{{ __('Status: :status', ['status' => ucfirst($exam->status)]) }}</span>
            </div>
            <h1 class="h4 fw-semibold mb-1">{{ $exam->title }}</h1>
            <p class="mb-0 text-muted">
                {{ __('Class: :class • Subject: :subject', ['class' => $exam->class->name ?? '—', 'subject' => $exam->subject->name ?? '—']) }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('tenant.teacher.classroom.exams.show', $exam) }}" target="_blank"
                class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('Open teacher view') }}
            </a>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @foreach (['success' => 'success', 'info' => 'info'] as $flashKey => $style)
        @if (session($flashKey))
            <div class="alert alert-{{ $style }} alert-dismissible fade show" role="alert">
                {{ session($flashKey) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">{{ __('Exam overview') }}</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">{{ __('Teacher') }}</dt>
                        <dd class="col-sm-8">{{ $exam->teacher->name ?? __('Unknown') }} <span
                                class="text-muted">({{ $exam->teacher->email ?? __('no email') }})</span></dd>

                        <dt class="col-sm-4 text-muted">{{ __('Window') }}</dt>
                        <dd class="col-sm-8">
                            {{ optional($exam->start_time)->format('M d, Y h:i A') ?? __('Not set') }}
                            <span class="text-muted">→</span>
                            {{ optional($exam->end_time)->format('M d, Y h:i A') ?? __('Not set') }}
                        </dd>

                        <dt class="col-sm-4 text-muted">{{ __('Duration') }}</dt>
                        <dd class="col-sm-8">{{ $exam->duration_minutes }} {{ __('minutes') }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Creation / Activation') }}</dt>
                        <dd class="col-sm-8">
                            {{ __('Creation: :method', ['method' => ucfirst($exam->creation_method ?? 'manual')]) }}<br>
                            {{ __('Activation: :mode', ['mode' => ucfirst($exam->activation_mode ?? 'manual')]) }}
                        </dd>

                        <dt class="col-sm-4 text-muted">{{ __('Submitted') }}</dt>
                        <dd class="col-sm-8">
                            {{ optional($exam->submitted_for_review_at)->diffForHumans() ?? __('Not submitted') }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Last reviewed') }}</dt>
                        <dd class="col-sm-8">{{ optional($exam->reviewed_at)->diffForHumans() ?? __('Not reviewed yet') }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h5 mb-0">{{ __('Sections & questions') }}</h2>
                        <small
                            class="text-muted">{{ __(':sections sections, :questions questions', ['sections' => $exam->sections->count(), 'questions' => $exam->sections->sum(fn($section) => $section->questions->count())]) }}</small>
                    </div>
                    <span
                        class="badge bg-light text-dark">{{ __('Total marks: :marks', ['marks' => $exam->total_marks]) }}</span>
                </div>
                <div class="card-body">
                    @forelse ($exam->sections as $section)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h3 class="h6 mb-1">{{ $section->title }}</h3>
                                    <small
                                        class="text-muted">{{ $section->description ?: __('No description provided') }}</small>
                                </div>
                                <span
                                    class="badge bg-secondary-subtle text-dark">{{ __(':count questions', ['count' => $section->questions->count()]) }}</span>
                            </div>
                            <ol class="ps-3 mb-0">
                                @foreach ($section->questions->take(5) as $question)
                                    <li class="mb-1">
                                        <span
                                            class="fw-semibold">{{ \Illuminate\Support\Str::limit($question->question, 120) }}</span>
                                        <span class="text-muted">({{ $question->marks }} {{ __('marks') }})</span>
                                    </li>
                                @endforeach
                                @if ($section->questions->count() > 5)
                                    <li class="text-muted">
                                        {{ __('+ :count more questions', ['count' => $section->questions->count() - 5]) }}
                                    </li>
                                @endif
                            </ol>
                        </div>
                    @empty
                        <p class="mb-0 text-muted">{{ __('No sections have been added yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="h5 mb-0">{{ __('Instructions for students') }}</h2>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $exam->instructions ?: __('No instructions were provided.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">{{ __('Review actions') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.exams.approve', $exam) }}" method="POST" class="mb-3">
                        @csrf
                        <label for="approveNotes" class="form-label">{{ __('Approval notes (optional)') }}</label>
                        <textarea name="notes" id="approveNotes" rows="3" class="form-control mb-2"
                            placeholder="{{ __('Optional guidance for the teacher') }}">{{ old('notes') }}</textarea>
                        <button type="submit" class="btn btn-success w-100"
                            {{ $exam->approval_status === 'approved' ? 'disabled' : '' }}>
                            <i class="bi bi-check2-circle me-1"></i>{{ __('Approve exam') }}
                        </button>
                    </form>

                    <form action="{{ route('admin.exams.request-changes', $exam) }}" method="POST" class="mb-3">
                        @csrf
                        <label for="changesNotes" class="form-label">{{ __('Request changes (required)') }}</label>
                        <textarea name="notes" id="changesNotes" rows="3" class="form-control mb-2"
                            placeholder="{{ __('List the changes needed before approval') }}" required></textarea>
                        <button type="submit" class="btn btn-warning w-100 text-dark">
                            <i class="bi bi-pencil-square me-1"></i>{{ __('Request changes') }}
                        </button>
                    </form>

                    <form action="{{ route('admin.exams.reject', $exam) }}" method="POST"
                        onsubmit="return confirm('{{ __('Reject and archive this exam?') }}');">
                        @csrf
                        <label for="rejectNotes" class="form-label">{{ __('Reject (required)') }}</label>
                        <textarea name="notes" id="rejectNotes" rows="3" class="form-control mb-2"
                            placeholder="{{ __('Provide a reason for rejection') }}" required></textarea>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-octagon me-1"></i>{{ __('Reject exam') }}
                        </button>
                    </form>
                </div>
            </div>

            @if ($exam->approval_status === 'approved' && $exam->activation_mode === 'manual')
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="h6 mb-0">{{ __('Manual activation') }}</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            {{ __('Manual activation is available because this exam uses the "manual" activation mode.') }}
                        </p>
                        <form action="{{ route('admin.exams.activate', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100"
                                {{ $exam->status === 'active' ? 'disabled' : '' }}>
                                <i
                                    class="bi bi-play-circle me-1"></i>{{ $exam->status === 'active' ? __('Already active') : __('Activate now') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($exam->review_notes)
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">{{ __('Latest reviewer notes') }}</h2>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">{{ $exam->review_notes }}</p>
                        <small
                            class="text-muted">{{ __('Updated :time by :name', ['time' => optional($exam->reviewed_at)->diffForHumans() ?? __('n/a'), 'name' => $exam->reviewer->name ?? __('System')]) }}</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
