<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name', $room->name ?? '') }}" required placeholder="e.g. Block A - Room 101">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="code" class="form-label">Room Code</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                value="{{ old('code', $room->code ?? '') }}" placeholder="e.g. A-101">
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="capacity" class="form-label">Capacity</label>
            <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                name="capacity" value="{{ old('capacity', $room->capacity ?? '') }}" min="1"
                placeholder="e.g. 40">
            @error('capacity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="type" class="form-label">Room Type</label>
            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                <option value="">Select Type</option>
                @foreach (['Classroom', 'Laboratory', 'Hall', 'Library', 'Computer Lab', 'Staff Room', 'Other'] as $type)
                    <option value="{{ $type }}" {{ old('type', $room->type ?? '') == $type ? 'selected' : '' }}>
                        {{ $type }}</option>
                @endforeach
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
            {{ old('is_active', $room->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
    <div class="form-text">Inactive rooms will not appear in timetable selection.</div>
</div>
