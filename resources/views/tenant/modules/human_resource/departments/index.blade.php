@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Departments') }}</h1>
            <div class="small text-secondary">{{ __('Organise the organisational structure and reporting lines.') }}</div>
        </div>
        <a href="{{ route('tenant.modules.human-resource.departments.create') }}"
            class="btn btn-primary btn-sm">{{ __('Add department') }}</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Members') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->code }}</td>
                            <td>{{ Str::limit(strip_tags($department->description), 50) }}</td>
                            <td>{{ $department->employees()->count() }}</td>
                            <td class="text-end">
                                <a href="{{ route('tenant.modules.human-resource.departments.show', $department) }}"
                                    class="btn btn-outline-secondary btn-sm" title="{{ __('View') }}"><i
                                        class="bi bi-eye"></i></a>
                                <a href="{{ route('tenant.modules.human-resource.departments.edit', $department) }}"
                                    class="btn btn-outline-primary btn-sm" title="{{ __('Edit') }}"><i
                                        class="bi bi-pencil"></i></a>
                                <form
                                    action="{{ route('tenant.modules.human-resource.departments.destroy', $department) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this department?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                        title="{{ __('Delete') }}"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">{{ __('No departments recorded yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
