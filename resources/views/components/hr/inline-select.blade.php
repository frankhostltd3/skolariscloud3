@props(['action', 'field', 'value' => null, 'options' => [], 'placeholder' => null])

<form method="POST" action="{{ $action }}" class="d-inline-flex align-items-center gap-2">
    @csrf
    @method('PUT')
    <input type="hidden" name="field" value="{{ $field }}">
    <select name="value" class="form-select form-select-sm" onchange="this.form.submit()">
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $label)
            <option value="{{ $optionValue }}" {{ (string) $optionValue === (string) $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</form>
