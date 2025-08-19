<div class="form-group">
    <label for="{{ 'form-' . $form['id'] . '-' . $field['name'] }}" @if(isset($field['required'])) required @endif>{{ $field['label'] }}</label>
    @if(isset($field['description']))
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif
    <select
        class="{{ $field['className'] ?? 'form-control' }}"
        name="{{ $field['name'] }}{{ isset($field['multiple']) && $field['multiple'] ? '[]' : '' }}"
        id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}"
        {{ isset($field['multiple']) && $field['multiple'] ? 'multiple' : '' }}
        {{ isset($field['placeholder']) ? 'placeholder='.$field['placeholder'] : '' }}
    >
        @foreach($field['values'] as $option)
            <option
                value="{{ $option['value'] }}"
                {{ isset($option['selected']) && $option['selected'] ? 'selected' : '' }}
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</div>
