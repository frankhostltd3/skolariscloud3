@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Notes & Learning Materials')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-journal-text me-2"></i>{{ __('Notes & Materials') }}
        </h4>
        <a href="{{ route('tenant.student.notes.personal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>{{ __('New Note') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$student)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ __('Student record not found. Please contact administrator.') }}
        </div>
    @else
        <div class="row mb-4">
            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('Teacher Materials') }}</h6>
                                <h4 class="mb-0 text-primary">{{ $statistics['total_materials'] }}</h4>
                            </div>
                            <div class="text-primary" style="font-size: 1.5rem;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('My Notes') }}</h6>
                                <h4 class="mb-0 text-success">{{ $statistics['total_personal_notes'] }}</h4>
                            </div>
                            <div class="text-success" style="font-size: 1.5rem;">
                                <i class="bi bi-journal-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('Favorites') }}</h6>
                                <h4 class="mb-0 text-warning">{{ $statistics['favorite_notes'] }}</h4>
                            </div>
                            <div class="text-warning" style="font-size: 1.5rem;">
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('Total Words') }}</h6>
                                <h4 class="mb-0 text-info">{{ number_format($statistics['total_words']) }}</h4>
                            </div>
                            <div class="text-info" style="font-size: 1.5rem;">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('tenant.student.notes.index') }}" class="row g-2">
                    <div class="col-12 col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Search notes and materials...') }}" value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="subject_id" class="form-select form-select-sm">
                            <option value="">{{ __('All Subjects') }}</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ ($subjectId ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="view" class="form-select form-select-sm">
                            <option value="materials" {{ ($view ?? 'materials') == 'materials' ? 'selected' : '' }}>{{ __('Teacher Materials') }}</option>
                            <option value="personal" {{ ($view ?? '') == 'personal' ? 'selected' : '' }}>{{ __('My Personal Notes') }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>{{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ ($view ?? 'materials') == 'materials' ? 'active' : '' }}" href="{{ route('tenant.student.notes.index', array_merge(request()->query(), ['view' => 'materials'])) }}">
                    <i class="bi bi-folder me-2"></i>{{ __('Teacher Materials') }}
                    <span class="badge bg-primary ms-1">{{ $materials->total() }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ ($view ?? '') == 'personal' ? 'active' : '' }}" href="{{ route('tenant.student.notes.index', array_merge(request()->query(), ['view' => 'personal'])) }}">
                    <i class="bi bi-journal me-2"></i>{{ __('My Notes') }}
                    <span class="badge bg-success ms-1">{{ $personalNotes->total() }}</span>
                </a>
            </li>
        </ul>

        @if(($view ?? 'materials') == 'materials')
            @if($materials->count() > 0)
                <div class="row">
                    @foreach($materials as $material)
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <span class="badge bg-{{ $material->type == 'document' ? 'primary' : ($material->type == 'video' ? 'danger' : 'info') }} mb-2">
                                                <i class="bi bi-{{ $material->type == 'document' ? 'file-text' : ($material->type == 'video' ? 'play-circle' : 'link-45deg') }} me-1"></i>
                                                {{ $material->type_label }}
                                            </span>
                                            <h6 class="mb-1">
                                                <a href="{{ route('tenant.student.notes.show', $material->id) }}" class="text-decoration-none text-dark">{{ $material->title }}</a>
                                            </h6>
                                        </div>
                                    </div>
                                    @if($material->description)
                                        <p class="small text-muted mb-2">{{ Str::limit($material->description, 100) }}</p>
                                    @endif
                                    <div class="row g-2 small text-muted mb-2">
                                        <div class="col-6"><i class="bi bi-book me-1"></i>{{ $material->subject->name ?? 'N/A' }}</div>
                                        <div class="col-6"><i class="bi bi-person me-1"></i>{{ $material->teacher->name ?? 'N/A' }}</div>
                                        @if($material->file_size)
                                            <div class="col-6"><i class="bi bi-hdd me-1"></i>{{ $material->file_size_formatted }}</div>
                                        @endif
                                        <div class="col-6"><i class="bi bi-eye me-1"></i>{{ $material->views_count }} {{ __('views') }}</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('tenant.student.notes.show', $material->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                            <i class="bi bi-eye me-1"></i>{{ __('View') }}
                                        </a>
                                        @if($material->is_downloadable && $material->file_path)
                                            <a href="{{ route('tenant.student.notes.download', $material->id) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $materials->appends(request()->query())->links() }}
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-folder text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">{{ __('No Materials Found') }}</h5>
                        <p class="text-muted">{{ __('No learning materials have been shared yet.') }}</p>
                    </div>
                </div>
            @endif
        @endif

        @if(($view ?? '') == 'personal')
            @if($personalNotes->count() > 0)
                <div class="row">
                    @foreach($personalNotes as $note)
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm hover-shadow" style="border-left: 4px solid {{ $note->color ?? '#0d6efd' }} !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $note->title }}</h6>
                                            @if($note->subject)
                                                <span class="badge bg-secondary small">{{ $note->subject->name }}</span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link p-0 favorite-btn" data-note-id="{{ $note->id }}" data-favorite="{{ $note->is_favorite ? 'true' : 'false' }}">
                                            <i class="bi bi-star{{ $note->is_favorite ? '-fill text-warning' : '' }}" style="font-size: 1.2rem;"></i>
                                        </button>
                                    </div>
                                    <p class="small text-muted mb-2">{{ $note->excerpt }}</p>
                                    @if($note->tags && count($note->tags) > 0)
                                        <div class="mb-2">
                                            @foreach($note->tags as $tag)
                                                <span class="badge bg-light text-dark small me-1">#{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-clock me-1"></i>{{ $note->updated_at->diffForHumans() }}
                                        <span class="ms-2"><i class="bi bi-file-text me-1"></i>{{ $note->word_count }} {{ __('words') }}</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('tenant.student.notes.personal.edit', $note->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('tenant.student.notes.personal.destroy', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this note?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $personalNotes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-journal-plus text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">{{ __('No Personal Notes') }}</h5>
                        <p class="text-muted">{{ __('Start taking notes to keep track of your learning!') }}</p>
                        <a href="{{ route('tenant.student.notes.personal.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-2"></i>{{ __('Create Your First Note') }}
                        </a>
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>

<style>
.hover-shadow { transition: all 0.3s ease; }
.hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important; }
.favorite-btn { text-decoration: none; }
.favorite-btn:hover { opacity: 0.7; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const noteId = this.dataset.noteId;
            const icon = this.querySelector('i');
            fetch(`/student/notes/personal/${noteId}/favorite`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_favorite) {
                        icon.classList.remove('bi-star');
                        icon.classList.add('bi-star-fill', 'text-warning');
                    } else {
                        icon.classList.remove('bi-star-fill', 'text-warning');
                        icon.classList.add('bi-star');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endsection

