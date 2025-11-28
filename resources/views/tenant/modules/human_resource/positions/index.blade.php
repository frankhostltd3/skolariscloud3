@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Positions') }}</h1>
            <div class="small text-secondary">{{ __('Map roles to departments and assign responsibilities.') }}</div>
        </div>
        <a href="{{ route('tenant.modules.human-resource.positions.create') }}"
            class="btn btn-primary btn-sm">{{ __('Add position') }}</a>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center justify-content-between">
        <form method="GET" action="{{ route('tenant.modules.human-resource.positions.index') }}" class="flex-grow-1 me-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search positions..."
                    value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                Import
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importExcelModal">Import
                        Excel</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importCsvModal">Import
                        CSV</a></li>
            </ul>
            <a href="{{ route('tenant.modules.human-resource.positions.exportTemplate') }}"
                class="btn btn-outline-info btn-sm">Download Excel Template</a>
            <a href="{{ route('tenant.modules.human-resource.positions.export', ['format' => 'excel']) }}"
                class="btn btn-outline-success btn-sm">Export Excel</a>
            <a href="{{ route('tenant.modules.human-resource.positions.export', ['format' => 'pdf']) }}"
                class="btn btn-outline-danger btn-sm">Export PDF</a>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('tenant.modules.human-resource.positions.import', ['format' => 'excel']) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importExcelModalLabel">Import Positions from Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Import CSV Modal -->
    <div class="modal fade" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('tenant.modules.human-resource.positions.import', ['format' => 'csv']) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importCsvModalLabel">Import Positions from CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr>
                            <td>{{ $position->title }}</td>
                            <td>{{ $position->department ? $position->department->name : '' }}</td>
                            <td>{{ $position->code }}</td>
                            <td>{{ Str::limit(strip_tags($position->description), 50) }}</td>
                            <td class="text-end">
                                <a href="{{ route('tenant.modules.human-resource.positions.show', $position) }}"
                                    class="btn btn-outline-secondary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('tenant.modules.human-resource.positions.edit', $position) }}"
                                    class="btn btn-outline-primary btn-sm" title="Edit"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="{{ route('tenant.modules.human-resource.positions.destroy', $position) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this position?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">{{ __('No positions configured yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
