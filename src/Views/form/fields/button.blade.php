<div class="form-group {{ $field['subtype'] }}">
    <button
        type="{{ $field['subtype'] }}"
        id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}"
        class="btn og-builder-form og-builder-form-{{ $form['id'] }}-btn btn-{{ $field['style'] }} {{ $field['className'] }}"
        name="{{ $field['name'] }}"
        value="{{ $field['value'] ?? '' }}"
    >
        <span class="btn-text">{{ $field['label'] }}</span>
       <span class="spinner" aria-hidden="true"></span>
    </button>
</div>
