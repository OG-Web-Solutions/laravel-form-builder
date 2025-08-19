<div class="form-group">
    <label for="{{ 'form-' . $form['id'] . '-' . $field['name'] }}" @if(isset($field['required'])) required @endif>{{ $field['label'] }}</label>
    @if(isset($field['description']) && $field['description'])
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif
    <input
        type="{{ $field['subtype'] ?? $field['type'] }}"
        class="{{ $field['className'] ?? 'form-control' }}"
        name="{{ $field['name'] }}"
        id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}"
        placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
        value="{{ $field['value'] ?? '' }}"
        @if(isset($field['maxlength'])) maxlength="{{ $field['maxlength'] }}" @endif
    />
</div>
