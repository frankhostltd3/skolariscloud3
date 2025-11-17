@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold">{{ __('Assign Classes to :subject', ['subject' => $subject->name]) }}</h1>
            <p class="text-muted small mt-1">{{ __('Select classes that will offer this subject') }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.subjects.show', $subject) }}"><i
                class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.subjects.store_class_assignments', $subject) }}" method="POST">@csrf
                @method('PUT')
                @if ($classes->isEmpty())
                    <div class="text-center py-5"><i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">{{ __('No classes available. Create classes first.') }}</p>
                    </div>
                @else
                    <div class="mb-3">
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="select-all"
                                onclick="toggleAll(this)"><label class="form-check-label fw-semibold"
                                for="select-all">{{ __('Select All') }}</label></div>
                    </div>
                    <hr>
                    @php $currentLevel = null; @endphp
                    @foreach ($classes as $class)
                        @if ($currentLevel !== optional($class->educationLevel)->id)
                            @php $currentLevel = optional($class->educationLevel)->id; @endphp
                            @if (!$loop->first)
                                <hr class="my-3">
                            @endif
                            <h6 class="text-muted mb-2">{{ optional($class->educationLevel)->name ?? __('No Level') }}</h6>
                        @endif
                        <div class="form-check mb-2"><input class="form-check-input class-checkbox" type="checkbox"
                                name="classes[]" value="{{ $class->id }}" id="class-{{ $class->id }}"
                                {{ $subject->classes->contains($class->id) ? 'checked' : '' }}><label
                                class="form-check-label" for="class-{{ $class->id }}">{{ $class->name }} @if ($class->streams->count() > 0)
                                    <small class="text-muted">({{ $class->streams->count() }} {{ __('streams') }})</small>
                                @endif
                            </label></div>
                    @endforeach
                    <div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i
                                class="bi bi-check-circle me-1"></i>{{ __('Save Assignments') }}</button><a
                            href="{{ route('tenant.academics.subjects.show', $subject) }}"
                            class="btn btn-outline-secondary">{{ __('Cancel') }}</a></div>
                @endif
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.class-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.class-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                });
            });
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            if (selectAll) selectAll.checked = allChecked;
        });
    </script>
@endpush
