@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('Student Enrollments') }}</h1>
        <a href="{{ route('tenant.academics.enrollments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>{{ __('New Enrollment') }}
        </a>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.academics.enrollments.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search Student') }}</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="{{ __('Name or email...') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Class') }}</label>
                    <select name="class_id" class="form-select">
                        <option value="">{{ __('All Classes') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}
                        </option>
                        <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}
                        </option>
                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>
                            {{ __('Transferred') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            {{ __('Completed') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Enrollments Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if ($enrollments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">{{ __('No enrollments found.') }}</p>
                    <a href="{{ route('tenant.academics.enrollments.create') }}" class="btn btn-sm btn-primary">
                        {{ __('Create First Enrollment') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Student') }}</th>
                                <th>{{ __('Class') }}</th>
                                <th>{{ __('Enrollment Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Fees') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $enrollment->student->name }}</div>
                                        <small class="text-muted">{{ $enrollment->student->email }}</small>
                                    </td>
                                    <td>{{ $enrollment->class->name ?? __('N/A') }}</td>
                                    <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'active' => 'bg-success-subtle text-success',
                                                'dropped' => 'bg-danger-subtle text-danger',
                                                'transferred' => 'bg-warning-subtle text-warning',
                                                'completed' => 'bg-info-subtle text-info',
                                            ];
                                            $badgeClass =
                                                $statusClasses[$enrollment->status] ??
                                                'bg-secondary-subtle text-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($enrollment->status) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ formatMoney($enrollment->fees_paid) }} /
                                            {{ formatMoney($enrollment->fees_total) }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tenant.academics.enrollments.show', $enrollment) }}"
                                                class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.academics.enrollments.edit', $enrollment) }}"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('tenant.academics.enrollments.destroy', $enrollment) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
