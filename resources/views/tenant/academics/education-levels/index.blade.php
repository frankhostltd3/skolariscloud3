@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Education Levels') }}</h1>
            <p class="text-muted mb-0">{{ __('Manage education levels like Primary, Secondary, O-Level, A-Level, etc.') }}
            </p>
        </div>
        <a class="btn btn-primary" href="{{ route('tenant.academics.education-levels.create') }}">
            <i class="bi bi-plus-circle me-1"></i>{{ __('Add Education Level') }}
        </a>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($educationLevels->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-mortarboard text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">{{ __('No Education Levels Yet') }}</h3>
                    <p class="text-muted mb-4">
                        {{ __('Create education levels to organize your classes by educational stages.') }}</p>
                    <a href="{{ route('tenant.academics.education-levels.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Create First Education Level') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Grade Range') }}</th>
                                <th>{{ __('Classes') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Sort Order') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($educationLevels as $level)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $level->name }}</div>
                                        @if ($level->description)
                                            <small class="text-muted">{{ Str::limit($level->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($level->code)
                                            <code class="text-muted">{{ $level->code }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($level->min_grade && $level->max_grade)
                                            {{ $level->min_grade }} - {{ $level->max_grade }}
                                        @elseif($level->min_grade)
                                            {{ $level->min_grade }}+
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $level->classes_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $level->is_active ? 'bg-success' : 'bg-warning' }}">
                                            {{ $level->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $level->sort_order }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('tenant.academics.education-levels.show', $level) }}"
                                                class="btn btn-outline-primary" title="{{ __('View Details') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.academics.education-levels.edit', $level) }}"
                                                class="btn btn-outline-secondary" title="{{ __('Edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $level->id }})" title="{{ __('Delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $level->id }}"
                                            action="{{ route('tenant.academics.education-levels.destroy', $level) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($educationLevels->hasPages())
                    <div class="mt-3">
                        {{ $educationLevels->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(levelId) {
            if (confirm(
                    '{{ __('Are you sure you want to delete this education level? This action cannot be undone.') }}')) {
                document.getElementById('delete-form-' + levelId).submit();
            }
        }
    </script>
@endpush
