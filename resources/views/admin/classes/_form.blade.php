@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Class Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $class->name ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Education Level</label>
        <select name="education_level_id" class="form-select" required>
            <option value="">Select Level</option>
            @foreach($levels as $level)
                <option value="{{ $level->id }}"
                    @selected(old('education_level_id', $class->education_level_id ?? null) == $level->id)>
                    {{ $level->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Stream (Optional)</label>
        <select name="class_stream_id" class="form-select">
            <option value="">No Stream</option>
            @foreach($streams as $stream)
                <option value="{{ $stream->id }}"
                    @selected(old('class_stream_id', $class->class_stream_id ?? null) == $stream->id)>
                    {{ $stream->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" class="form-control" min="1" max="200"
               value="{{ old('capacity', $class->capacity ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Room</label>
        <input type="text" name="room_number" class="form-control"
               value="{{ old('room_number', $class->room_number ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Status</label>
        @php
            $isActive = old('is_active', $class->is_active ?? 1);
        @endphp
        <select name="is_active" class="form-select">
            <option value="1" @selected($isActive == 1)>Active</option>
            <option value="0" @selected($isActive == 0)>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end">
    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2-circle me-1"></i> Save
    </button>
</div>
