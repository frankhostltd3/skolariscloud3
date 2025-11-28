@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Salary scales') }}</h1>
            <div class="small text-secondary">{{ __('Standardise compensation bands across the organisation.') }}</div>
        </div>
        <a href="{{ route('tenant.modules.human-resource.salary-scales.create') }}"
            class="btn btn-primary btn-sm">{{ __('Add scale') }}</a>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search by name, grade, or notes') }}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">{{ __('Search') }}</button>
                    <a href="{{ route('tenant.modules.human-resource.salary-scales.index') }}"
                        class="btn btn-outline-secondary">{{ __('Clear') }}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Import/Export Buttons -->
    <div class="mb-3">
        <a href="{{ route('tenant.modules.human-resource.salary-scales.exportTemplate') }}"
            class="btn btn-info btn-sm">{{ __('Download Excel Template') }}</a>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#importModal">{{ __('Import Excel/CSV') }}</button>
        <a href="{{ route('tenant.modules.human-resource.salary-scales.export', ['format' => 'excel']) }}"
            class="btn btn-warning btn-sm">{{ __('Export Excel') }}</a>
        <a href="{{ route('tenant.modules.human-resource.salary-scales.export', ['format' => 'pdf']) }}"
            class="btn btn-danger btn-sm">{{ __('Export PDF') }}</a>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">{{ __('Import Salary Scales') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('tenant.modules.human-resource.salary-scales.import', 'excel') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('Select Excel or CSV file') }}</label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
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
                        <th>{{ __('Position') }}</th>
                        <th>{{ __('Grade') }}</th>
                        <th class="text-end">{{ __('Min - Max Amount') }}</th>
                        <th>{{ __('Notes') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaryScales as $scale)
                        <tr>
                            <td>{{ $scale->name }}</td>
                            <td>{{ $scale->grade }}</td>
                            <td class="text-end">{{ number_format($scale->min_amount) }} -
                                {{ number_format($scale->max_amount) }}</td>
                            <td>{{ Str::limit(strip_tags($scale->notes), 50) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('tenant.modules.human-resource.salary-scales.show', $scale) }}"
                                        class="btn btn-outline-info" title="{{ __('View') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.modules.human-resource.salary-scales.edit', $scale) }}"
                                        class="btn btn-outline-warning" title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('tenant.modules.human-resource.salary-scales.destroy', $scale) }}"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}"
                                            onclick="return confirm('{{ __('Are you sure?') }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">{{ __('No salary scales defined yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
