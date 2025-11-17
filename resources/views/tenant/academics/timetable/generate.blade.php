@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h4 fw-semibold mb-0">{{ __('Generate Timetable') }}</h1>
                        <p class="text-muted mb-0">
                            {{ __('Automatically generate a timetable for a class using intelligent scheduling algorithms') }}
                        </p>
                    </div>
                    <a href="{{ route('tenant.academics.timetable.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Timetable') }}
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Generation Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.academics.timetable.storeGenerated') }}" method="POST">
                            @csrf

                            <!-- Class Selection -->
                            <div class="mb-4">
                                <label for="class_id" class="form-label fw-semibold">{{ __('Select Class') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                    name="class_id" required>
                                    <option value="">{{ __('Choose a class...') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->subjects_count ?? 0 }} subjects)
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Generation Mode (appears when class has streams) -->
                            <div id="generation_mode_wrapper" class="mb-4" style="display:none;">
                                <label class="form-label fw-semibold">{{ __('Generation Mode') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="generation_mode" id="mode_class"
                                        value="class" checked>
                                    <label class="form-check-label"
                                        for="mode_class">{{ __('Generate one timetable for entire class') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="generation_mode" id="mode_streams"
                                        value="streams">
                                    <label class="form-check-label"
                                        for="mode_streams">{{ __('Generate separate timetables per stream') }}</label>
                                </div>
                                <div id="stream_options" class="mt-3" style="display:none;">
                                    <label class="form-label">{{ __('Stream Scope') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stream_scope" id="scope_all"
                                            value="all" checked>
                                        <label class="form-check-label" for="scope_all">{{ __('All streams') }}</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="stream_scope"
                                            id="scope_selected" value="selected">
                                        <label class="form-check-label"
                                            for="scope_selected">{{ __('Selected streams only') }}</label>
                                    </div>
                                    <div id="selected_streams_wrapper" style="display:none;">
                                        <label class="form-label">{{ __('Select Streams') }}</label>
                                        <div id="streams_checklist" class="border rounded p-2 small bg-light">
                                            {{ __('No streams loaded.') }}</div>
                                    </div>
                                    <small
                                        class="text-muted d-block mt-2">{{ __('If selected streams have no subjects assigned they will be skipped.') }}</small>
                                </div>
                            </div>

                            <!-- Schedule Settings -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="max_periods_per_day"
                                        class="form-label">{{ __('Max Periods per Day') }}</label>
                                    <input type="number"
                                        class="form-control @error('max_periods_per_day') is-invalid @enderror"
                                        id="max_periods_per_day" name="max_periods_per_day"
                                        value="{{ old('max_periods_per_day', 8) }}" min="1" max="12">
                                    @error('max_periods_per_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Number of teaching periods per day') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="max_periods_per_week"
                                        class="form-label">{{ __('Max Periods per Week') }}</label>
                                    <input type="number"
                                        class="form-control @error('max_periods_per_week') is-invalid @enderror"
                                        id="max_periods_per_week" name="max_periods_per_week"
                                        value="{{ old('max_periods_per_week', 40) }}" min="1" max="60">
                                    @error('max_periods_per_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Total periods per week for the class') }}</small>
                                </div>
                            </div>

                            <!-- Break Settings -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="break_after_periods"
                                        class="form-label">{{ __('Break After Periods') }}</label>
                                    <input type="number"
                                        class="form-control @error('break_after_periods') is-invalid @enderror"
                                        id="break_after_periods" name="break_after_periods"
                                        value="{{ old('break_after_periods', 4) }}" min="1" max="10">
                                    @error('break_after_periods')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Insert break after this many periods') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="lunch_break_slot" class="form-label">{{ __('Lunch Break Slot') }}</label>
                                    <input type="number"
                                        class="form-control @error('lunch_break_slot') is-invalid @enderror"
                                        id="lunch_break_slot" name="lunch_break_slot"
                                        value="{{ old('lunch_break_slot', 4) }}" min="1" max="10">
                                    @error('lunch_break_slot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Position of lunch break (period number)') }}</small>
                                </div>
                            </div>

                            <!-- Working Days -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('Working Days') }}</label>
                                <div class="row g-2">
                                    @php
                                        $days = [
                                            1 => __('Monday'),
                                            2 => __('Tuesday'),
                                            3 => __('Wednesday'),
                                            4 => __('Thursday'),
                                            5 => __('Friday'),
                                            6 => __('Saturday'),
                                            7 => __('Sunday'),
                                        ];
                                        $selectedDays = old('working_days', [1, 2, 3, 4, 5]);
                                    @endphp
                                    @foreach ($days as $value => $label)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="day_{{ $value }}" name="working_days[]"
                                                    value="{{ $value }}"
                                                    {{ in_array($value, $selectedDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_{{ $value }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('working_days')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Options -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="overwrite_existing"
                                        name="overwrite_existing" value="1"
                                        {{ old('overwrite_existing') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="overwrite_existing">
                                        {{ __('Overwrite existing timetable entries for this class') }}
                                    </label>
                                </div>
                                <small
                                    class="text-muted">{{ __('If checked, all existing timetable entries for the selected class will be deleted before generating new ones.') }}</small>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('tenant.academics.timetable.index') }}"
                                    class="btn btn-outline-secondary">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-magic me-2"></i>{{ __('Generate Timetable') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-select reasonable defaults based on class selection
                document.getElementById('class_id').addEventListener('change', function() {
                    const classId = this.value;
                    if (classId) {
                        loadStreams(classId);
                    }
                });
            });
        </script>

        <div class="col-lg-4">
            <!-- Information Panel -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('How It Works') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">{{ __('Algorithm Overview') }}</h6>
                        <p class="small text-muted mb-2">
                            {{ __('The system uses a genetic algorithm to create optimal timetables by:') }}</p>
                        <ul class="small text-muted mb-0">
                            <li>{{ __('Analyzing subject requirements and teacher assignments') }}</li>
                            <li>{{ __('Avoiding scheduling conflicts') }}</li>
                            <li>{{ __('Balancing teacher workload') }}</li>
                            <li>{{ __('Optimizing subject distribution across days') }}</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-success">{{ __('Key Features') }}</h6>
                        <ul class="small text-muted mb-0">
                            <li>{{ __('Automatic conflict detection') }}</li>
                            <li>{{ __('Teacher workload balancing') }}</li>
                            <li>{{ __('Flexible scheduling constraints') }}</li>
                            <li>{{ __('Room assignment optimization') }}</li>
                        </ul>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('Generation may take a few moments for complex timetables. The algorithm will try multiple solutions to find the optimal schedule.') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('System Stats') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary mb-1">{{ $classes->count() }}</div>
                            <small class="text-muted">{{ __('Classes') }}</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success mb-1">{{ $subjectsCount }}</div>
                            <small class="text-muted">{{ __('Subjects') }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-warning mb-1">{{ $teachersCount }}</div>
                            <small class="text-muted">{{ __('Teachers') }}</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-info mb-1">{{ $entriesCount }}</div>
                            <small class="text-muted">{{ __('Entries') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-select reasonable defaults based on class selection
            document.getElementById('class_id').addEventListener('change', function() {
                const classId = this.value;
                if (classId) {
                    // Could add AJAX call here to get class-specific defaults
                    console.log('Class selected:', classId);
                }
            });

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const workingDays = document.querySelectorAll('input[name="working_days[]"]:checked');
                if (workingDays.length === 0) {
                    e.preventDefault();
                    alert('{{ __('Please select at least one working day.') }}');
                    return false;
                }

                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Generating...') }}';
                submitBtn.disabled = true;

                // Re-enable after 30 seconds as fallback
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 30000);
            });

            function loadStreams(classId) {
                fetch(`{{ route('tenant.academics.class_streams.options') }}?class_id=${classId}`)
                    .then(r => r.json())
                    .then(data => {
                        const streams = data.data || [];
                        const modeWrapper = document.getElementById('generation_mode_wrapper');
                        const streamsChecklist = document.getElementById('streams_checklist');
                        if (streams.length) {
                            modeWrapper.style.display = 'block';
                            streamsChecklist.innerHTML = '';
                            streams.forEach(stream => {
                                const id = `stream_${stream.id}`;
                                const div = document.createElement('div');
                                div.className = 'form-check';
                                div.innerHTML = `
                                    <input class="form-check-input" type="checkbox" name="stream_ids[]" id="${id}" value="${stream.id}">
                                    <label class="form-check-label" for="${id}">${stream.name}</label>
                                `;
                                streamsChecklist.appendChild(div);
                            });
                        } else {
                            modeWrapper.style.display = 'none';
                            streamsChecklist.innerHTML = '{{ __('No streams available for this class.') }}';
                        }
                    });
            }

            // Mode toggling
            const modeClass = document.getElementById('mode_class');
            const modeStreams = document.getElementById('mode_streams');
            const streamOptions = document.getElementById('stream_options');
            modeClass.addEventListener('change', toggleStreamOptions);
            modeStreams.addEventListener('change', toggleStreamOptions);

            function toggleStreamOptions() {
                streamOptions.style.display = modeStreams.checked ? 'block' : 'none';
            }

            const scopeAll = document.getElementById('scope_all');
            const scopeSelected = document.getElementById('scope_selected');
            const selectedWrapper = document.getElementById('selected_streams_wrapper');
            scopeAll.addEventListener('change', toggleSelectedStreams);
            scopeSelected.addEventListener('change', toggleSelectedStreams);

            function toggleSelectedStreams() {
                selectedWrapper.style.display = scopeSelected.checked ? 'block' : 'none';
            }
        });
    </script>
@endsection
