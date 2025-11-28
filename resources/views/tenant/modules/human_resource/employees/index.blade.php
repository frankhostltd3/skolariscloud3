@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Employees') }} ({{ $employees->count() }})</h1>
            <div class="small text-secondary">{{ __('Maintain staff records, contact details, and assignments.') }}</div>
        </div>
        <a href="{{ route('tenant.modules.human-resource.employees.create') }}"
            class="btn btn-primary btn-sm">{{ __('Add employee') }}</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form class="d-flex gap-2" method="get">
                    <input type="search" class="form-control form-control-sm" placeholder="{{ __('Search employees') }}"
                        disabled>
                    <button class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ __('Filter') }}</button>
                </form>
                <span class="text-muted small">{{ __('Integrations with payroll planned') }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Position') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php($initials = strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)))
                                        <div class="flex-shrink-0">
                                            @if ($employee->photo_path)
                                                <img src="{{ asset('storage/' . $employee->photo_path) }}" alt="Photo"
                                                    class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width:38px;height:38px;font-size:0.9rem;">{{ $initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-truncate" style="max-width:160px;">
                                                {{ $employee->first_name }} {{ $employee->last_name }}</div>
                                            <div class="small text-muted">
                                                {{ $employee->employee_number ?? 'No ID' }}
                                                @if ($employee->gender)
                                                    &middot; {{ ucfirst($employee->gender) }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->department?->name ?? __('Not Assigned') }}</td>
                                <td>{{ $employee->position?->title ?? __('Not Assigned') }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $employee->employment_status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($employee->employment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('tenant.modules.human-resource.employees.show', $employee) }}"
                                            class="btn btn-sm btn-outline-primary" title="{{ __('View Details') }}">
                                            <i class="bi bi-eye me-1"></i>{{ __('View') }}
                                        </a>
                                        <a href="{{ route('tenant.modules.human-resource.employees.edit', $employee) }}"
                                            class="btn btn-sm btn-outline-secondary" title="{{ __('Edit Employee') }}">
                                            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                        </a>
                                        <form
                                            action="{{ route('tenant.modules.human-resource.employees.destroy', $employee) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="{{ __('Delete Employee') }}"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this employee?') }}')">
                                                <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary">{{ __('No employees added yet') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Action buttons styling */
        .d-flex.gap-1 .btn {
            white-space: nowrap;
            font-weight: 500;
            border-width: 1px;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .d-flex.gap-1 .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .d-flex.gap-1 .btn i {
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .d-flex.gap-1 {
                flex-direction: column;
                align-items: stretch;
            }

            .d-flex.gap-1 .btn {
                margin-bottom: 0.25rem;
                text-align: center;
                justify-content: center;
            }
        }

        .table-responsive {
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Ensure action buttons are visible */
        .d-flex.gap-1 {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .d-flex.gap-1 .btn {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
@endsection
