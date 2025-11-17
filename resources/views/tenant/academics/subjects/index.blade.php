@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Subjects') }}</h1>
            <p class="text-muted small mt-1">{{ __('Manage academic subjects and their configurations') }}</p>
        </div>
        <a class="btn btn-primary" href="{{ route('tenant.academics.subjects.create') }}"><i
                class="bi bi-plus-circle me-1"></i>{{ __('Add Subject') }}</a>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="get" class="row g-2">
                <div class="col-md-4"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control"
                        placeholder="{{ __('Search by name or code') }}" /></div>
                <div class="col-md-2"><select name="type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="core" {{ request('type') == 'core' ? 'selected' : '' }}>{{ __('Core') }}
                        </option>
                        <option value="elective" {{ request('type') == 'elective' ? 'selected' : '' }}>{{ __('Elective') }}
                        </option>
                        <option value="optional" {{ request('type') == 'optional' ? 'selected' : '' }}>
                            {{ __('Optional') }}</option>
                    </select></div>
                <div class="col-md-3"><select name="education_level_id" class="form-select">
                        <option value="">{{ __('All Levels') }}</option>
                        @foreach ($educationLevels as $level)
                            <option value="{{ $level->id }}"
                                {{ request('education_level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}
                            </option>
                        @endforeach
                    </select></div>
                <div class="col-md-2"><select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            {{ __('Inactive') }}</option>
                    </select></div>
                <div class="col-md-1"><button class="btn btn-outline-secondary w-100">{{ __('Filter') }}</button></div>
            </form>
        </div>
    </div>
    @if ($subjects->isEmpty() && !request()->hasAny(['q', 'type', 'education_level_id', 'status']))
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3"><i class="bi bi-book text-muted" style="font-size: 3rem;"></i></div>
                <h5 class="text-muted">{{ __('No subjects yet') }}</h5>
                <p class="text-muted mb-4">{{ __('Get started by creating your first subject.') }}</p><a
                    class="btn btn-primary"
                    href="{{ route('tenant.academics.subjects.create') }}">{{ __('Create Subject') }}</a>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($subjects->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">{{ __('No subjects found matching your filters.') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Level') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Pass Mark') }}</th>
                                    <th>{{ __('Classes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $subject)
                                    <tr>
                                        <td><a href="{{ route('tenant.academics.subjects.show', $subject) }}"
                                                class="text-decoration-none fw-semibold">{{ $subject->name }}</a></td>
                                        <td><code>{{ $subject->code ?? '—' }}</code></td>
                                        <td>{{ optional($subject->educationLevel)->name ?? '—' }}</td>
                                        <td><span
                                                class="badge bg-{{ $subject->type_badge_color }}">{{ $subject->type_label }}</span>
                                        </td>
                                        <td>{{ $subject->pass_mark }}/{{ $subject->max_marks }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $subject->classes_count }}</span>
                                        </td>
                                        <td><span
                                                class="badge {{ $subject->status_badge }}">{{ $subject->status_text }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('tenant.academics.subjects.show', $subject) }}"
                                                    class="btn btn-outline-primary" title="{{ __('View') }}"><i
                                                        class="bi bi-eye"></i></a>
                                                <a href="{{ route('tenant.academics.subjects.edit', $subject) }}"
                                                    class="btn btn-outline-secondary" title="{{ __('Edit') }}"><i
                                                        class="bi bi-pencil"></i></a>
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmDelete({{ $subject->id }})"
                                                    title="{{ __('Delete') }}"><i class="bi bi-trash"></i></button>
                                            </div>
                                            <form id="delete-form-{{ $subject->id }}"
                                                action="{{ route('tenant.academics.subjects.destroy', $subject) }}"
                                                method="POST" class="d-none">@csrf @method('DELETE')</form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $subjects->links() }}
                @endif
            </div>
        </div>
    @endif
@endsection
@push('scripts')
    <script>
        function confirmDelete(subjectId) {
            if (confirm('{{ __('Are you sure you want to delete this subject?') }}')) {
                document.getElementById('delete-form-' + subjectId).submit();
            }
        }
    </script>
@endpush
