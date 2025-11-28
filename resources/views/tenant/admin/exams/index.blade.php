@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Teacher Exams') }}</h1>
            <p class="mb-0 text-muted">{{ __('Review and control all teacher-created online exams before they go live.') }}
            </p>
        </div>
        <a href="{{ route('tenant.teacher.classroom.exams.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-arrow-up-right me-2"></i>{{ __('Open teacher portal') }}
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-info-subtle">
                <div class="card-body">
                    <div class="small text-muted">{{ __('Pending review') }}</div>
                    <div class="h3 mb-0">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-warning-subtle">
                <div class="card-body">
                    <div class="small text-muted">{{ __('Changes requested') }}</div>
                    <div class="h3 mb-0">{{ $stats['changes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-success-subtle">
                <div class="card-body">
                    <div class="small text-muted">{{ __('Approved') }}</div>
                    <div class="h3 mb-0">{{ $stats['approved'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-danger-subtle">
                <div class="card-body">
                    <div class="small text-muted">{{ __('Rejected') }}</div>
                    <div class="h3 mb-0">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="status" class="form-label fw-semibold">{{ __('Status filter') }}</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">{{ __('Pending + Changes') }}</option>
                        @foreach ([
            'pending_review' => __('Pending review'),
            'changes_requested' => __('Changes requested'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            'draft' => __('Draft'),
        ] as $value => $label)
                            <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                    <input type="search" class="form-control" id="search" name="search"
                        placeholder="{{ __('Exam title or teacher name') }}" value="{{ $search }}">
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-filter me-2"></i>{{ __('Apply filters') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h5 mb-0">{{ __('Exams queue') }}</h2>
                <small class="text-muted">{{ __('Newest submissions shown first') }}</small>
            </div>
            <span class="badge text-bg-light">{{ __('Total: :count', ['count' => $exams->total()]) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Exam') }}</th>
                        <th>{{ __('Teacher') }}</th>
                        <th>{{ __('Class / Subject') }}</th>
                        <th>{{ __('Window') }}</th>
                        <th>{{ __('Workflow') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exams as $exam)
                        <tr>
                            <td>
                                <a href="{{ route('admin.exams.show', $exam) }}"
                                    class="fw-semibold text-decoration-none">{{ $exam->title }}</a>
                                <div class="small text-muted">
                                    {{ __('Created via :method', ['method' => ucfirst($exam->creation_method ?? 'manual')]) }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $exam->teacher->name ?? __('Unknown teacher') }}</div>
                                <div class="small text-muted">{{ $exam->teacher->email ?? __('No email') }}</div>
                            </td>
                            <td>
                                <div>{{ $exam->class->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $exam->subject->name ?? '—' }}</div>
                            </td>
                            <td>
                                <div>{{ optional($exam->start_time)->format('M d, Y h:i A') ?? __('TBD') }}</div>
                                <div class="small text-muted">
                                    {{ optional($exam->end_time)->format('M d, Y h:i A') ?? __('—') }}</div>
                            </td>
                            <td>
                                @php
                                    $approvalClass = match ($exam->approval_status) {
                                        'approved' => 'success',
                                        'pending_review' => 'info',
                                        'changes_requested' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span
                                    class="badge bg-{{ $approvalClass }} text-uppercase">{{ str_replace('_', ' ', $exam->approval_status ?? 'draft') }}</span>
                                <div class="small text-muted">
                                    {{ __('Activation: :mode', ['mode' => ucfirst($exam->activation_mode ?? 'manual')]) }}
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>{{ __('Review') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-clipboard-check text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0 text-muted">{{ __('No exams need your attention right now.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($exams->hasPages())
            <div class="card-footer">{{ $exams->links() }}</div>
        @endif
    </div>
@endsection
