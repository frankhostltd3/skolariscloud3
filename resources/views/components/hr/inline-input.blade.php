@props(['action', 'field', 'value' => null, 'type' => 'text', 'placeholder' => ''])

<form method="POST" action="{{ $action }}" class="d-inline-flex align-items-center gap-2">
    @csrf
    @method('PUT')
    <input type="hidden" name="field" value="{{ $field }}">
    <input type="{{ $type }}" name="value" value="{{ old('value', $value) }}" placeholder="{{ $placeholder }}"
        class="form-control form-control-sm" onchange="this.form.submit()">
</form>
