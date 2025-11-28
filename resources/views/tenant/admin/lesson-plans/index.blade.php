@extends('tenant.layouts.app')

@section('title', __('Lesson Plan Reviews'))

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ __('Lesson Plan Reviews') }}</h1>
                <p class="text-muted mb-0">
                    {{ __('Track every submission, provide feedback, and keep instruction quality high.') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.admin') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Admin Dashboard') }}
                </a>
            </div>
        </div>

        @foreach (['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $flash => $variant)
            @if (session($flash))
                <div class="alert alert-{{ $variant }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session($flash) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="{{ __('Close') }}"></button>
                </div>
            @endif
        @endforeach

        @if (!($lessonPlansAvailable ?? false))
            <div class="alert alert-info" role="alert">
                <i class="bi bi-lock me-2"></i>
                {{ __('Lesson plan tables are not available for this tenant yet. Run tenant migrations to enable reviews.') }}
            </div>
        @else
            <div class="row g-3 mb-4">
                @foreach ($statusOptions as $value => $label)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted text-uppercase small fw-semibold">{{ $label }}</div>
                                <div class="display-6 fw-bold">{{ $statusCounts[$value] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-3 mb-4">
                @foreach ($reviewOptions as $value => $label)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted text-uppercase small fw-semibold">{{ $label }}</div>
                                <div class="display-6 fw-bold">{{ $reviewCounts[$value] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('Search') }}</label>
                            <input type="text" class="form-control" name="search" value="{{ $filters['search'] }}"
                                placeholder="{{ __('Title, notes, or teacher name') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small">{{ __('Status') }}</label>
                            <select class="form-select" name="status">
                                <option value="">{{ __('All') }}</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small">{{ __('Review') }}</label>
                            <select class="form-select" name="review_status">
                                <option value="">{{ __('All') }}</option>
                                @foreach ($reviewOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['review_status'] === $value)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small">{{ __('Class') }}</label>
                            <select class="form-select" name="class_id">
                                <option value="">{{ __('All') }}</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" @selected($filters['class_id'] == $class->id)>{{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('Subject') }}</label>
                            <select class="form-select" name="subject_id">
                                <option value="">{{ __('All') }}</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected($filters['subject_id'] == $subject->id)>{{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('Teacher') }}</label>
                            <select class="form-select" name="teacher_id">
                                <option value="">{{ __('All') }}</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" @selected($filters['teacher_id'] == $teacher->id)>{{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('From Date') }}</label>
                            <input type="date" class="form-control" name="date_from"
                                value="{{ $filters['date_from'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('To Date') }}</label>
                            <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel me-1"></i>{{ __('Apply Filters') }}
                            </button>
                            <a href="{{ route('tenant.admin.lesson-plans.index') }}" class="btn btn-outline-secondary">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Lesson Plans') }}</h5>
                        <span
                            class="text-muted small">{{ trans_choice(':count lesson|:count lessons', $lessonPlans->total(), ['count' => $lessonPlans->total()]) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Lesson') }}</th>
                                    <th>{{ __('Teacher') }}</th>
                                    <th>{{ __('Class / Subject') }}</th>
                                    <th>{{ __('Lesson Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Review') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lessonPlans as $plan)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $plan->title }}</div>
                                            <div class="text-muted small">
                                                {{ \Illuminate\Support\Str::limit($plan->notes, 80) }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $plan->teacher->name ?? __('Unknown') }}</div>
                                            <div class="text-muted small">
                                                {{ $plan->teacher->email ?? __('No email on file') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $plan->class->name ?? __('Class N/A') }}</div>
                                            <div class="text-muted small">{{ $plan->subject->name ?? __('Subject N/A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ optional($plan->lesson_date)->format('M d, Y') ?? __('TBD') }}</div>
                                            <div class="text-muted small">
                                                {{ optional($plan->submitted_at)->diffForHumans() ?: __('Not submitted') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $plan->status_badge_class ?? 'secondary' }}">{{ $plan->status_label }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $plan->review_status_badge_class ?? 'secondary' }}">{{ $plan->review_status_label }}</span>
                                            @if ($plan->review_feedback)
                                                <div class="text-muted small mt-1">
                                                    {{ \Illuminate\Support\Str::limit($plan->review_feedback, 60) }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('tenant.admin.lesson-plans.show', $plan) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="{{ __('View details') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if (
                                                    $plan->review_status === \App\Models\LessonPlan::REVIEW_PENDING ||
                                                        $plan->review_status === \App\Models\LessonPlan::REVIEW_REVISION)
                                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                                        data-bs-target="#approveModal{{ $plan->id }}">
                                                        <i class="bi bi-check2"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                        data-bs-target="#revisionModal{{ $plan->id }}">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $plan->id }}">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                @elseif(in_array($plan->review_status, [\App\Models\LessonPlan::REVIEW_APPROVED, \App\Models\LessonPlan::REVIEW_REJECTED]))
                                                    <form action="{{ route('tenant.admin.lesson-plans.reopen', $plan) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('{{ __('Reopen this review?') }}');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                            title="{{ __('Reopen review') }}">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    @include('tenant.admin.lesson-plans.partials.modals', [
                                        'plan' => $plan,
                                    ])
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-1 text-muted">
                                                {{ __('No lesson plans available for review.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    {{ $lessonPlans->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
