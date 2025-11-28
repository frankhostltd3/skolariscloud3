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
                    <div class="d-flex gap-2">
                        <a href="{{ route('tenant.academics.timetable.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>{{ __('Manual Entry') }}
                        </a>
                        <a href="{{ route('tenant.academics.timetable.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Timetable') }}
                        </a>
                    </div>
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
                        <form action="{{ route('tenant.academics.timetable.storeGenerated') }}" method="POST"
                            id="generateForm">
                            @csrf

                            <!-- Generation Scope -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('Generation Scope') }}</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="scope" id="scope_single"
                                            value="single" checked>
                                        <label class="form-check-label" for="scope_single">
                                            {{ __('Single Class/Stream') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="scope" id="scope_all"
                                            value="all">
                                        <label class="form-check-label" for="scope_all">
                                            {{ __('All Classes & Streams') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Class & Stream Selection -->
                            <div id="single_class_selection" class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="class_id" class="form-label fw-semibold">{{ __('Select Class') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id">
                                        <option value="">{{ __('Choose a class...') }}</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="class_stream_id" class="form-label fw-semibold">{{ __('Select Stream') }}
                                        <span class="text-muted small">({{ __('Optional') }})</span></label>
                                    <select class="form-select @error('class_stream_id') is-invalid @enderror"
                                        id="class_stream_id" name="class_stream_id">
                                        <option value="">{{ __('All Streams') }}</option>
                                    </select>
                                    @error('class_stream_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Schedule Settings -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="max_periods_per_day" class="form-label">{{ __('Max Periods per Day') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('max_periods_per_day') is-invalid @enderror"
                                        id="max_periods_per_day" name="max_periods_per_day"
                                        value="{{ old('max_periods_per_day', 8) }}" min="1" max="12" required>
                                    @error('max_periods_per_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Number of teaching periods per day') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">{{ __('School Start Time') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time" value="{{ old('start_time', '08:00') }}"
                                        required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('When does school start') }}</small>
                                </div>
                            </div>

                            <!-- Period Duration -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="period_duration" class="form-label">{{ __('Period Duration (minutes)') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('period_duration') is-invalid @enderror"
                                        id="period_duration" name="period_duration"
                                        value="{{ old('period_duration', 40) }}" min="30" max="90" required>
                                    @error('period_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Duration of each period') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="break_duration" class="form-label">{{ __('Break Duration (minutes)') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('break_duration') is-invalid @enderror"
                                        id="break_duration" name="break_duration"
                                        value="{{ old('break_duration', 15) }}" min="5" max="60" required>
                                    @error('break_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Break between periods') }}</small>
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
            const streamsByClass = @json($streamsByClass);
            const classSelect = document.getElementById('class_id');
            const streamSelect = document.getElementById('class_stream_id');
            const scopeRadios = document.querySelectorAll('input[name="scope"]');
            const singleClassSelection = document.getElementById('single_class_selection');

            // Function to update streams
            function updateStreams() {
                const classId = classSelect.value;
                const currentStreamId = streamSelect.getAttribute('data-old-value');

                streamSelect.innerHTML = '<option value="">{{ __('All Streams') }}</option>';

                // Handle both array (if keys are sequential 0-based) and object
                let streams = null;
                if (streamsByClass) {
                    if (Array.isArray(streamsByClass)) {
                        streams = streamsByClass[classId];
                    } else {
                        streams = streamsByClass[classId];
                    }
                }

                if (classId && streams && streams.length > 0) {
                    streams.forEach(stream => {
                        const option = document.createElement('option');
                        option.value = stream.id;
                        option.textContent = stream.name;
                        if (currentStreamId && stream.id == currentStreamId) {
                            option.selected = true;
                        }
                        streamSelect.appendChild(option);
                    });
                    streamSelect.disabled = false;
                } else {
                    streamSelect.disabled = true;
                }
            }

            // Handle Scope Change
            scopeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'all') {
                        singleClassSelection.style.display = 'none';
                        classSelect.removeAttribute('required');
                    } else {
                        singleClassSelection.style.display = 'flex';
                        classSelect.setAttribute('required', 'required');
                    }
                });
            });

            // Handle Class Change
            classSelect.addEventListener('change', function() {
                // Clear old value when class changes manually
                streamSelect.removeAttribute('data-old-value');
                updateStreams();
            });

            // Initialize
            streamSelect.setAttribute('data-old-value', '{{ old('class_stream_id') }}');
            updateStreams();

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
        });
    </script>
@endsection
