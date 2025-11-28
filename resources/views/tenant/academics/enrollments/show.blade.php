@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Enrollment Details') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('tenant.academics.enrollments.index') }}">{{ __('Enrollments') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $enrollment->student->name }}
                    </li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('tenant.academics.enrollments.edit', $enrollment) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
            </a>
            <form action="{{ route('tenant.academics.enrollments.destroy', $enrollment) }}" method="POST"
                onsubmit="return confirm('{{ __('Are you sure you want to delete this enrollment?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    @php
        $statusMap = [
            'active' => ['label' => __('Active'), 'class' => 'bg-success-subtle text-success'],
            'dropped' => ['label' => __('Dropped'), 'class' => 'bg-danger-subtle text-danger'],
            'transferred' => ['label' => __('Transferred'), 'class' => 'bg-warning-subtle text-warning'],
            'completed' => ['label' => __('Completed'), 'class' => 'bg-info-subtle text-info'],
        ];
        $status = $statusMap[$enrollment->status] ?? [
            'label' => ucfirst($enrollment->status),
            'class' => 'bg-secondary-subtle text-secondary',
        ];
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="text-muted text-uppercase small mb-1">{{ __('Student') }}</p>
                            <div class="fw-semibold fs-5">{{ $enrollment->student->name }}</div>
                            <div class="text-muted">{{ $enrollment->student->email }}</div>
                        </div>
                        <span class="badge {{ $status['class'] }} px-3 py-2">{{ $status['label'] }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted text-uppercase small mb-1">{{ __('Class') }}</p>
                                <div class="fw-semibold">{{ $enrollment->class->name ?? __('Not assigned') }}</div>
                                <div class="text-muted small">{{ __('Academic Year') }}:
                                    {{ $enrollment->academicYear->name ?? __('N/A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted text-uppercase small mb-1">{{ __('Enrollment Date') }}</p>
                                <div class="fw-semibold">
                                    {{ optional($enrollment->enrollment_date)->format('M d, Y') ?? __('N/A') }}
                                </div>
                                <div class="text-muted small">{{ __('Enrolled By') }}:
                                    {{ optional($enrollment->enrolledBy)->name ?? __('System') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted text-uppercase small mb-1">{{ __('Fees Summary') }}</p>
                                <div class="fw-semibold">{{ formatMoney($enrollment->fees_paid) }} /
                                    {{ formatMoney($enrollment->fees_total) }}</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ min(100, $enrollment->fees_total > 0 ? ($enrollment->fees_paid / max($enrollment->fees_total, 1)) * 100 : 0) }}%">
                                    </div>
                                </div>
                                <div class="text-muted small mt-2">
                                    {{ __('Balance') }}:
                                    {{ formatMoney($enrollment->fees_total - $enrollment->fees_paid) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted text-uppercase small mb-1">{{ __('Status Notes') }}</p>
                                <div class="text-muted small">
                                    @switch($enrollment->status)
                                        @case('active')
                                            {{ __('Student currently attending classes.') }}
                                        @break

                                        @case('completed')
                                            {{ __('Enrollment completed successfully.') }}
                                        @break

                                        @case('transferred')
                                            {{ __('Student transferred to another institution/class.') }}
                                        @break

                                        @case('dropped')
                                            {{ __('Student is no longer attending this class.') }}
                                        @break

                                        @default
                                            {{ __('Status updated recently.') }}
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($enrollment->notes)
                        <div class="mt-4">
                            <p class="text-muted text-uppercase small mb-2">{{ __('Notes') }}</p>
                            <div class="border rounded p-3 bg-light-subtle">
                                {{ $enrollment->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">{{ __('Quick Summary') }}</div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-6 text-muted">{{ __('Student ID') }}</dt>
                        <dd class="col-6 fw-semibold">{{ $enrollment->student->id }}</dd>

                        <dt class="col-6 text-muted">{{ __('Academic Year') }}</dt>
                        <dd class="col-6 fw-semibold">{{ $enrollment->academicYear->name ?? __('N/A') }}</dd>

                        <dt class="col-6 text-muted">{{ __('Enrollment Date') }}</dt>
                        <dd class="col-6 fw-semibold">
                            {{ optional($enrollment->enrollment_date)->format('M d, Y') ?? __('N/A') }}</dd>

                        <dt class="col-6 text-muted">{{ __('Status') }}</dt>
                        <dd class="col-6 fw-semibold">{{ ucfirst($enrollment->status) }}</dd>

                        <dt class="col-6 text-muted">{{ __('Fees Total') }}</dt>
                        <dd class="col-6 fw-semibold">{{ formatMoney($enrollment->fees_total) }}</dd>

                        <dt class="col-6 text-muted">{{ __('Fees Paid') }}</dt>
                        <dd class="col-6 fw-semibold">{{ formatMoney($enrollment->fees_paid) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header fw-semibold">{{ __('Actions') }}</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('tenant.academics.enrollments.edit', $enrollment) }}"
                        class="list-group-item list-group-item-action">
                        <span class="bi bi-pencil-square me-2"></span>{{ __('Update Enrollment') }}
                    </a>
                    <a href="{{ route('tenant.academics.enrollments.index') }}"
                        class="list-group-item list-group-item-action">
                        <span class="bi bi-list-ul me-2"></span>{{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
