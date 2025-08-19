<div class="form-group">
    <label for="{{ $field['name'] }}" @if(isset($field['required'])) required @endif>{{ $field['label'] }}</label>
    @if(isset($field['description']))
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif
    <input
        type="text"
        class="{{ $field['className'] }}"
        name="{{ $field['name'] }}"
        id="{{ $form->id.'_'.$field['name'] }}"
        placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
        list="{{ $field['name'] }}-datalist"
        {{ isset($field['requireValidOption']) && $field['requireValidOption'] ? 'data-require-valid-option="true"' : '' }}
    />
    <datalist id="{{ $field['name'] }}-datalist">
        @foreach($field['values'] as $option)
            <option
                value="{{ $option['value'] }}"
                {{ $option['selected'] ? 'selected' : '' }}
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </datalist>
</div>


