@props(['action', 'field', 'value' => false, 'label' => null])

<form method="POST" action="{{ $action }}" class="form-check form-switch">
    @csrf
    @method('PUT')
    <input type="hidden" name="field" value="{{ $field }}">
    <input class="form-check-input" type="checkbox" role="switch" name="value" value="1"
        {{ $value ? 'checked' : '' }} onchange="this.form.submit()">
    @if ($label)
        <label class="form-check-label">{{ $label }}</label>
    @endif
</form>
