@php
    $editing = isset($entry);
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Day of week</label>
        <select name="day_of_week" class="form-select" required>
            <option value="">Select day</option>
            @for ($d = 1; $d <= 7; $d++)
                <option value="{{ $d }}" @selected(old('day_of_week', $editing ? $entry->day_of_week : '') == $d)>
                    {{ \Carbon\Carbon::create()->startOfWeek()->addDays($d - 1)->format('l') }}</option>
            @endfor
        </select>
        @error('day_of_week')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Starts at</label>
        <input type="time" name="starts_at" class="form-control"
            value="{{ old('starts_at', $editing ? $entry->starts_at : '') }}" required />
        @error('starts_at')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Ends at</label>
        <input type="time" name="ends_at" class="form-control"
            value="{{ old('ends_at', $editing ? $entry->ends_at : '') }}" required />
        @error('ends_at')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Room</label>
        <input type="text" name="room" class="form-control"
            value="{{ old('room', $editing ? $entry->room : '') }}" maxlength="50" />
        @error('room')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Class</label>
        <select name="class_id" class="form-select" required id="tt-class-id">
            <option value="">Select class</option>
            @foreach ($classes as $c)
                <option value="{{ $c->id }}" @selected(old('class_id', $editing ? $entry->class_id : '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('class_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Stream</label>
        <select name="class_stream_id" class="form-select" id="tt-stream-id">
            <option value="">None</option>
            @foreach ($streams as $s)
                <option value="{{ $s->id }}" data-class="{{ $s->class_id }}" @selected(old('class_stream_id', $editing ? $entry->class_stream_id : '') == $s->id)>
                    {{ $s->name }}</option>
            @endforeach
        </select>
        @error('class_stream_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-select" required>
            <option value="">Select subject</option>
            @foreach ($subjects as $sub)
                <option value="{{ $sub->id }}" @selected(old('subject_id', $editing ? $entry->subject_id : '') == $sub->id)>
                    {{ $sub->name }}
                    @if ($sub->code)
                        ({{ $sub->code }})
                    @endif
                </option>
            @endforeach
        </select>
        @error('subject_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Teacher</label>
        <select name="teacher_id" class="form-select">
            <option value="">Unassigned</option>
            @foreach ($teachers as $t)
                @php
                    $teacherName = trim($t->full_name ?? '' ?: ($t->first_name ?? '') . ' ' . ($t->last_name ?? ''));
                    if (empty($teacherName)) {
                        $teacherName = $t->name ?? __('Teacher #:id', ['id' => $t->id]);
                    }
                    $subjectIds = $t->subjects->pluck('id')->implode(',');
                    $subjectCodes = $t->subjects->pluck('code')->filter()->implode(', ');
                @endphp
                <option value="{{ $t->id }}" data-subjects="{{ $subjectIds }}"
                    data-subject-codes="{{ $subjectCodes }}" @selected(old('teacher_id', $editing ? $entry->teacher_id : '') == $t->id)>
                    {{ $teacherName }}
                    @if ($subjectCodes)
                        — {{ $subjectCodes }}
                    @endif
                </option>
            @endforeach
        </select>
        @error('teacher_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        <div class="form-text">
            <i class="bi bi-info-circle me-1"></i>
            {{ __('Only teachers allocated to the selected subject will be available.') }}
            <a href="{{ route('tenant.academics.teacher-allocations.index') }}"
                target="_blank">{{ __('Manage teacher allocations') }}</a>
        </div>
        <div id="teacher-allocation-warning" class="alert alert-warning mt-2 d-none" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ __('No teachers are currently allocated to this subject. Please assign a teacher before scheduling.') }}
            <a href="{{ route('tenant.academics.teacher-allocations.index') }}" class="alert-link"
                target="_blank">{{ __('Open allocations') }}</a>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <input type="text" name="notes" class="form-control"
            value="{{ old('notes', $editing ? $entry->notes : '') }}" maxlength="500" />
        @error('notes')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const classSelect = document.getElementById('tt-class-id');
            const streamSelect = document.getElementById('tt-stream-id');
            const subjectSelect = document.querySelector('select[name="subject_id"]');
            const teacherSelect = document.querySelector('select[name="teacher_id"]');
            const warningBox = document.getElementById('teacher-allocation-warning');

            const teacherOptions = teacherSelect ? Array.from(teacherSelect.options) : [];

            function filterTeachers() {
                if (!teacherSelect || !subjectSelect) return;
                const subjectId = subjectSelect.value;
                let availableCount = 0;

                teacherOptions.forEach(option => {
                    if (!option.value) {
                        option.hidden = false;
                        option.disabled = false;
                        return;
                    }

                    const subjectsAttr = option.dataset.subjects || '';
                    const subjects = subjectsAttr.split(',').filter(Boolean);
                    const matches = !subjectId || subjects.includes(subjectId);

                    option.hidden = !matches;
                    option.disabled = !matches;

                    if (matches) {
                        availableCount++;
                    }
                });

                if (teacherSelect.value) {
                    const currentOption = teacherSelect.options[teacherSelect.selectedIndex];
                    if (currentOption && currentOption.disabled) {
                        teacherSelect.value = '';
                    }
                }

                if (warningBox) {
                    warningBox.classList.toggle('d-none', !(subjectId && availableCount === 0));
                }
            }

            const streamsByClass = @json($streamsByClass ?? []);

            async function loadStreams(classId, initialLoad = false) {
                const keep = @json(old('class_stream_id', $editing ? $entry->class_stream_id ?? '' : ''));

                // On initial load, don't reload if streams are already rendered by Blade
                if (initialLoad) {
                    // Check if we have more than just the "None" option
                    const hasStreams = streamSelect.options.length > 1;
                    if (hasStreams && keep) {
                        // Check if the keep value is already selected
                        const existingOption = streamSelect.querySelector(`option[value="${keep}"]`);
                        if (existingOption) {
                            existingOption.selected = true; // Ensure it's selected
                            return;
                        }
                    }
                    // If editing but no streams loaded, or keep value not found, proceed with fetch
                    if (hasStreams && !keep) {
                        return; // Don't reload if we have streams and no specific value to select
                    }
                }

                streamSelect.innerHTML = '<option value="">None</option>';
                if (!classId) return;

                // First try local map for instant population
                const local = streamsByClass[classId] || [];
                if (Array.isArray(local) && local.length > 0) {
                    local.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        if (String(keep ?? '') === String(s.id)) {
                            opt.selected = true;
                        }
                        streamSelect.appendChild(opt);
                    });
                    return; // Skip fetch if we had local data
                }

                try {
                    const res = await fetch(
                        `{{ route('tenant.academics.class_streams.options') }}?class_id=${classId}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json.data || !Array.isArray(json.data)) return;

                    json.data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        if (String(keep ?? '') === String(s.id)) {
                            opt.selected = true;
                        }
                        streamSelect.appendChild(opt);
                    });
                } catch (e) {
                    // Silently fail - streams won't update but form remains functional
                }
            }

            classSelect.addEventListener('change', (e) => loadStreams(e.target.value, false));

            // On page load, check if we need to load streams
            if (classSelect.value) {
                loadStreams(classSelect.value, true);
            }

            if (subjectSelect) {
                subjectSelect.addEventListener('change', filterTeachers);
                filterTeachers();
            }
        })();
    </script>
@endpush
