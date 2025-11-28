@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('Students') }}
            @can('create', App\Models\Student::class)
                <span class="badge text-bg-success ms-2">{{ __('Can create') }}</span>
            @endcan
            @cannot('create', App\Models\Student::class)
                <span class="badge text-bg-secondary ms-2">{{ __('Read-only') }}</span>
            @endcannot
        </h1>
        @can('create', App\Models\Student::class)
            <a class="btn btn-primary" href="{{ route('tenant.modules.students.create') }}">{{ __('Add Student') }}</a>
        @endcan
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form method="get" class="row g-2 mb-3">
                <div class="col-auto">
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control"
                        placeholder="{{ __('Search name, admission, email') }}" />
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    @if (!empty($q))
                        <a class="btn btn-link" href="{{ route('tenant.modules.students.index') }}">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Admission No') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('DOB') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $s)
                            <tr>
                                <td><a href="{{ route('tenant.modules.students.show', $s) }}">{{ $s->id }}</a></td>
                                <td><a href="{{ route('tenant.modules.students.show', $s) }}">{{ $s->name }}</a></td>
                                <td>{{ $s->admission_no }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->dob }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route('tenant.modules.students.show', $s) }}">{{ __('Show') }}</a>
                                    @can('update', $s)
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('tenant.modules.students.edit', $s) }}">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete', $s)
                                        <form action="{{ route('tenant.modules.students.destroy', $s) }}" method="post"
                                            class="d-inline" onsubmit="return confirm('{{ __('Delete this student?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                type="submit">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('No students yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $students->links() }}</div>
        </div>
    </div>
@endsection
