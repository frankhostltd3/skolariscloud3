@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('Employees') }}
            @can('create', App\Models\Teacher::class)
                <span class="badge text-bg-success ms-2">{{ __('Can create') }}</span>
            @endcan
            @cannot('create', App\Models\Teacher::class)
                <span class="badge text-bg-secondary ms-2">{{ __('Read-only') }}</span>
            @endcannot
        </h1>
        @can('create', App\Models\Teacher::class)
            <a class="btn btn-primary" href="{{ route('tenant.modules.teachers.create') }}">{{ __('Add Employee') }}</a>
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
                        placeholder="{{ __('Search name or email') }}" />
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    @if (!empty($q))
                        <a class="btn btn-link" href="{{ route('tenant.modules.teachers.index') }}">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $t)
                            <tr>
                                <td><a href="{{ route('tenant.modules.teachers.show', $t) }}">{{ $t->id }}</a></td>
                                <td><a href="{{ route('tenant.modules.teachers.show', $t) }}">{{ $t->name }}</a></td>
                                <td>{{ $t->email }}</td>
                                <td>{{ $t->phone }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route('tenant.modules.teachers.show', $t) }}">{{ __('Show') }}</a>
                                    @can('update', $t)
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('tenant.modules.teachers.edit', $t) }}">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete', $t)
                                        <form action="{{ route('tenant.modules.teachers.destroy', $t) }}" method="post"
                                            class="d-inline" onsubmit="return confirm('{{ __('Delete this teacher?') }}');">
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
                                <td colspan="5" class="text-center text-muted py-4">{{ __('No teachers yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $teachers->links() }}</div>
        </div>
    </div>
@endsection
