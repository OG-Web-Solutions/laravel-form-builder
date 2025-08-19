<div class="form-group">
    @if(isset($field['label']))
        <label for="{{ 'form-' . $form['id'] . '-' . $field['name'] }}" @if(isset($field['required'])) required @endif>{{ $field['label'] }}</label>
    @endif

    <input
        type="{{ $field['subtype'] ?? 'number' }}"
        class="{{ $field['className'] ?? 'form-control' }}"
        name="{{ $field['name'] }}"
        id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}"
        value="{{ $field['value'] ?? '' }}"
        @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
        @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
        @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
        @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
    />

    @if(isset($field['description']))
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif
</div>
