@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Teacher Allocations') }}</h1>
            <p class="text-muted small mt-1">{{ __('Manage teacher assignments to classes and subjects') }}</p>
        </div>
        <div class="d-flex gap-2"><a class="btn btn-success"
                href="{{ route('tenant.academics.teacher-allocations.workload') }}"><i
                    class="bi bi-bar-chart me-1"></i>{{ __('Teacher Workload') }}</a><a class="btn btn-primary"
                href="{{ route('tenant.academics.teacher-allocations.create') }}"><i
                    class="bi bi-plus-circle me-1"></i>{{ __('Allocate Teacher') }}</a></div>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="get" class="row g-2">
                <div class="col-md-4"><select name="teacher_id" class="form-select">
                        <option value="">{{ __('All Teachers') }}</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $teacherId == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><select name="class_id" class="form-select">
                        <option value="">{{ __('All Classes') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4"><select name="subject_id" class="form-select">
                        <option value="">{{ __('All Subjects') }}</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} @if ($subject->code)
                                    ({{ $subject->code }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1"><button class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i></button>
                </div>
            </form>
        </div>
    </div>
    @if ($allocations->isEmpty() && !request()->hasAny(['teacher_id', 'class_id', 'subject_id']))
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3"><i class="bi bi-person-badge text-muted" style="font-size: 3rem;"></i></div>
                <h5 class="text-muted">{{ __('No teacher allocations yet') }}</h5>
                <p class="text-muted mb-4">{{ __('Start by allocating teachers to subjects in classes.') }}</p><a
                    class="btn btn-primary"
                    href="{{ route('tenant.academics.teacher-allocations.create') }}">{{ __('Allocate Teacher') }}</a>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($allocations->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">{{ __('No allocations found matching your filters.') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Teacher') }}</th>
                                    <th>{{ __('Class') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allocations as $allocation)
                                    <tr>
                                        <td>
                                            @if ($allocation->teacher_name)
                                                <div class="fw-semibold">{{ $allocation->teacher_name }}</div><small
                                                class="text-muted">{{ $allocation->teacher_email }}</small>@else<span
                                                    class="badge bg-warning">{{ __('Unassigned') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $allocation->class_name }}</td>
                                        <td><span class="fw-semibold">{{ $allocation->subject_name }}</span>
                                            @if ($allocation->subject_code)
                                                <code>{{ $allocation->subject_code }}</code>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                @if ($allocation->is_compulsory)
                                                    {{ __('Compulsory') }}@else{{ __('Optional') }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if ($allocation->teacher_id)
                                            <span class="badge bg-success">{{ __('Allocated') }}</span>@else<span
                                                    class="badge bg-secondary">{{ __('Available') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if ($allocation->teacher_id)
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="confirmUnassign({{ $allocation->id }})"
                                                        title="{{ __('Unassign Teacher') }}"><i
                                                            class="bi bi-person-x"></i></button>
                                                    <form id="unassign-form-{{ $allocation->id }}"
                                                        action="{{ route('tenant.academics.teacher-allocations.destroy', $allocation->id) }}"
                                                        method="POST" class="d-none">@csrf @method('DELETE')</form>
                                                @else
                                                    <a href="{{ route('tenant.academics.teacher-allocations.create', ['class_id' => $allocation->class_id, 'subject_id' => $allocation->subject_id]) }}"
                                                        class="btn btn-outline-primary"
                                                        title="{{ __('Assign Teacher') }}"><i
                                                            class="bi bi-person-plus"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $allocations->links() }}
                @endif
            </div>
        </div>
    @endif
@endsection
@push('scripts')
    <script>
        function confirmUnassign(allocationId) {
            if (confirm('{{ __('Are you sure you want to unassign this teacher?') }}')) {
                document.getElementById('unassign-form-' + allocationId).submit();
            }
        }
    </script>
@endpush
