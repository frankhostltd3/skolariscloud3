@extends('layouts.dashboard-teacher')

@section('title', __('Lesson Plans'))

@section('content')
    @php($statusOptions = App\Models\LessonPlan::statusOptions())
    @php($reviewOptions = App\Models\LessonPlan::reviewStatusOptions())
    @php($lessonPlansAvailable = $lessonPlansAvailable ?? true)
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-journal-text me-2 text-primary"></i>{{ __('Lesson Plans') }}
                </h1>
                <p class="text-muted mb-0">{{ __('Plan, submit, and track every lesson with review visibility.') }}</p>
            </div>
            <div class="d-flex gap-2">
                @if ($lessonPlansAvailable)
                    <a href="{{ route('tenant.teacher.classroom.lessons.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('New Lesson Plan') }}
                    </a>
                @endif
                <a href="{{ route('tenant.teacher.classroom.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Classroom Hub') }}
                </a>
            </div>
        </div>

        @foreach (['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $flash => $variant)
            @if (session($flash))
                <div class="alert alert-{{ $variant }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session($flash) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        @unless ($lessonPlansAvailable)
            <div class="alert alert-info" role="alert">
                <i class="bi bi-lock me-2"></i>
                {{ __('Lesson plans are not enabled for this tenant yet. Please ask your administrator to run the tenant migrations.') }}
            </div>
        @endunless

        @if ($lessonPlansAvailable)
            <form method="GET" class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('Search') }}</label>
                            <input type="text" class="form-control" name="search" value="{{ $filters['search'] }}"
                                placeholder="{{ __('Title or notes') }}">
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
                            <label class="form-label text-muted small">{{ __('From Date') }}</label>
                            <input type="date" class="form-control" name="date_from"
                                value="{{ optional($filters['date_from'])->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">{{ __('To Date') }}</label>
                            <input type="date" class="form-control" name="date_to"
                                value="{{ optional($filters['date_to'])->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel me-2"></i>{{ __('Apply Filters') }}
                            </button>
                            <a href="{{ route('tenant.teacher.classroom.lessons.index') }}"
                                class="btn btn-outline-secondary">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>

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

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Lesson') }}</th>
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
                                            <div class="text-muted small">{{ Str::limit($plan->notes, 80) }}</div>
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
                                                @if ($plan->start_time && $plan->end_time)
                                                    {{ $plan->start_time->format('H:i') }} -
                                                    {{ $plan->end_time->format('H:i') }}
                                                @elseif($plan->start_time)
                                                    {{ $plan->start_time->format('H:i') }}
                                                @else
                                                    {{ __('No time set') }}
                                                @endif
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
                                                <span
                                                    class="d-block text-muted small mt-1">{{ Str::limit($plan->review_feedback, 60) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                                <a href="{{ route('tenant.teacher.classroom.lessons.show', $plan) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="{{ __('View details') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if ($plan->isEditable())
                                                    <a href="{{ route('tenant.teacher.classroom.lessons.edit', $plan) }}"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        title="{{ __('Edit lesson plan') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                @if ($plan->canSubmit())
                                                    <form
                                                        action="{{ route('tenant.teacher.classroom.lessons.submit', $plan) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            {{ $plan->review_status === App\Models\LessonPlan::REVIEW_REVISION ? __('Resubmit') : __('Submit') }}
                                                        </button>
                                                    </form>
                                                @endif
                                                @if (
                                                    $plan->review_status === App\Models\LessonPlan::REVIEW_APPROVED &&
                                                        $plan->status === App\Models\LessonPlan::STATUS_SCHEDULED)
                                                    <form
                                                        action="{{ route('tenant.teacher.classroom.lessons.mark-in-progress', $plan) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-info">{{ __('Start') }}</button>
                                                    </form>
                                                @endif
                                                @if ($plan->canMarkDelivered())
                                                    <form
                                                        action="{{ route('tenant.teacher.classroom.lessons.mark-completed', $plan) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-success">{{ __('Complete') }}</button>
                                                    </form>
                                                @endif
                                                <form
                                                    action="{{ route('tenant.teacher.classroom.lessons.duplicate', $plan) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-dark"
                                                        title="{{ __('Duplicate lesson plan') }}">
                                                        <i class="bi bi-files"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-1 text-muted">{{ __('No lesson plans yet') }}</p>
                                            <a href="{{ route('tenant.teacher.classroom.lessons.create') }}"
                                                class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>{{ __('Create your first plan') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if (method_exists($lessonPlans, 'links'))
                    <div class="card-footer bg-white">
                        {{ $lessonPlans->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
