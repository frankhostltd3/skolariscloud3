@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Teacher Workload') }}</h1>
            <p class="text-muted small mt-1">{{ __('View teacher assignments and subject distribution') }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.teacher-allocations.index') }}"><i
                class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-10"><label for="teacher_id" class="form-label">{{ __('Select Teacher') }}</label><select
                        name="teacher_id" id="teacher_id" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('-- Select a Teacher --') }}</option>
                        @foreach ($teachers as $t)
                            <option value="{{ $t->id }}"
                                {{ ($selectedTeacherId ?? null) == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">{{ __('Load') }}</button>
                </div>
            </form>
        </div>
    </div>
    @if (!$teacher)
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-1"></i>
            {{ __('Select a teacher to view workload details. If no teachers are available, please add teaching staff first.') }}
        </div>
    @else
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 text-primary mb-1">{{ $stats['total_subjects'] }}</div><small
                            class="text-muted">{{ __('Total Subjects') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 text-success mb-1">{{ $stats['total_classes'] }}</div><small
                            class="text-muted">{{ __('Classes Taught') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 text-info mb-1">{{ $stats['core_subjects'] }}</div><small
                            class="text-muted">{{ __('Core Subjects') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 text-warning mb-1">{{ $stats['elective_subjects'] + $stats['optional_subjects'] }}
                        </div>
                        <small class="text-muted">{{ __('Elective/Optional') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">{{ __('Teacher: :name', ['name' => $teacher->name]) }}</h5><small
                    class="text-muted">{{ $teacher->email }}</small>
            </div>
            <div class="card-body">
                @if ($allocations->isEmpty())
                    <div class="text-center py-5"><i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">{{ __('No subjects assigned to this teacher yet.') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Class') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Level') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentLevel = null; @endphp
                                @foreach ($allocations as $allocation)
                                    @if ($currentLevel !== $allocation->level_name)
                                        @php $currentLevel = $allocation->level_name; @endphp
                                        <tr class="table-light">
                                            <td colspan="5" class="fw-bold">
                                                {{ $allocation->level_name ?? __('No Level') }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><span class="fw-semibold">{{ $allocation->class_name }}</span></td>
                                        <td>{{ $allocation->subject_name }} @if ($allocation->subject_code)
                                                <code>{{ $allocation->subject_code }}</code>
                                            @endif
                                        </td>
                                        <td>{{ $allocation->level_name ?? '—' }}</td>
                                        <td>
                                            @if ($allocation->subject_type === 'core')
                                                <span class="badge bg-primary">{{ __('Core') }}</span>
                                            @elseif($allocation->subject_type === 'elective')
                                            <span class="badge bg-success">{{ __('Elective') }}</span>@else<span
                                                    class="badge bg-info">{{ __('Optional') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($allocation->is_compulsory)
                                            <span class="badge bg-success">{{ __('Compulsory') }}</span>@else<span
                                                    class="badge bg-secondary">{{ __('Optional') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
