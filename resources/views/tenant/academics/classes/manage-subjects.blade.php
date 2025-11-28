@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Manage Subjects') }}</h1>
            <p class="text-muted mb-0">{{ $class->name }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.classes.show', $class) }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Class') }}
        </a>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.classes.subjects.update', $class) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <p class="text-muted">{{ __('Select the subjects that are taught in this class.') }}</p>

                    @if ($subjects->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ __('No active subjects found. Please create subjects first.') }}
                            <a href="{{ route('tenant.academics.subjects.create') }}"
                                class="alert-link">{{ __('Create Subject') }}</a>
                        </div>
                    @else
                        <div class="row g-3">
                            @php
                                $assignedSubjectIds = $class->subjects->pluck('id')->toArray();
                                $groupedSubjects = $subjects->groupBy(function ($item) {
                                    return $item->educationLevel ? $item->educationLevel->name : 'General';
                                });
                            @endphp

                            @foreach ($groupedSubjects as $level => $levelSubjects)
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold border-bottom pb-2 mb-3">{{ $level }}</h6>
                                    <div class="row g-3">
                                        @foreach ($levelSubjects as $subject)
                                            <div class="col-md-4 col-lg-3">
                                                <div
                                                    class="form-check card h-100 p-3 {{ in_array($subject->id, $assignedSubjectIds) ? 'border-primary bg-light' : '' }}">
                                                    <input class="form-check-input" type="checkbox" name="subjects[]"
                                                        value="{{ $subject->id }}" id="subject_{{ $subject->id }}"
                                                        {{ in_array($subject->id, $assignedSubjectIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100 stretched-link"
                                                        for="subject_{{ $subject->id }}">
                                                        <span class="d-block fw-semibold">{{ $subject->name }}</span>
                                                        <small class="text-muted">{{ $subject->code }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('tenant.academics.classes.show', $class) }}"
                        class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary" {{ $subjects->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-1"></i>{{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.form-check-input').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.card');
                if (this.checked) {
                    card.classList.add('border-primary', 'bg-light');
                } else {
                    card.classList.remove('border-primary', 'bg-light');
                }
            });
        });
    </script>
@endpush
