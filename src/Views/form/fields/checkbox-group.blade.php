<div class="form-group {{ $field['className'] ?? '' }}">
     <h4 class="form-label @if(isset($field['required'])) required @endif">{{ $field['label'] }}</h4>
    @if(isset($field['description']))
        <div class="form-text">{{ $field['description'] }}</div>
    @endif
    <div class="checkbox-group {{ $field['inline'] ? 'd-flex gap-3' : '' }}">
        @foreach($field['values'] as $option)
            <div class="form-check">
                <input type="checkbox"
                    class="form-check-input"
                    name="{{ $field['name'] }}[]"
                    id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}_{{ $loop->index }}"
                    value="{{ $option['value'] }}"
                    {{ $option['selected'] ? 'checked' : '' }}
                    {{ $field['toggle'] ? 'data-toggle="toggle"' : '' }}
                    {{ $field['access'] ? 'data-access="true"' : '' }}>
                <label class="form-check-label" for="{{ 'form-' . $form['id'] . '-' . $field['name'] }}_{{ $loop->index }}">
                    {{ $option['label'] }}
                </label>
            </div>
        @endforeach
        @if($field['other'])
            <div class="form-check">
                <input type="checkbox"
                    class="form-check-input"
                    name="{{ $field['name'] }}[]"
                    id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}_other"
                    value="other">
                <label class="form-check-label" for="{{ 'form-' . $form['id'] . '-' . $field['name'] }}_other">
                    Other
                    <input type="text" class="form-control form-control-sm d-inline-block w-auto ms-2 {{ 'form-' . $form['id'] . '-' . $field['name'] }}_other_text"
                        name="{{ $field['name'] }}_other_value">
                </label>
            </div>
        @endif
    </div>
</div>
