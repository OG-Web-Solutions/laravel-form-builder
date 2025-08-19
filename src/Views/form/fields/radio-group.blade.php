<div class="form-group">
    <h4 class="form-label @if(isset($field['required'])) required @endif">{{ $field['label'] }}</h4>
    @if(isset($field['description']))
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif

    <div class="{{ isset($field['inline']) && $field['inline'] ? 'd-flex gap-3' : '' }}">
        @foreach($field['values'] as $option)
            <div class="form-check {{ isset($field['className']) ? $field['className'] : '' }}" id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}">
                <label class="form-check-label" for="{{ 'form-' . $form['id'] . '-' . $field['name'] }}_{{ $loop->index }}">
                    <input
                        id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}_{{ $loop->index }}"
                        class="form-check-input"
                        type="radio"
                        name="{{ $field['name'] }}"
                        value="{{ $option['value'] }}"
                        @checked(isset($option['selected']) && $option['selected'])
                    >
                    {{ $option['label'] }}
                </label>
            </div>
        @endforeach

        @if(isset($field['other']) && $field['other'])
            <div class="form-check" id="{{ 'form-' . $form['id'] . '-' . $field['name'] . '_other' }}">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="{{ $field['name'] }}" value="other">
                    Other
                    <input type="text" class="form-control mt-1" name="{{ $field['name'] }}_other" placeholder="Please specify">
                </label>
            </div>
        @endif
    </div>
</div>
