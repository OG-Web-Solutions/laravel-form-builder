<div class="form-group">
    <input
        type="hidden"
        name="{{ $field['name'] }}"
        id="{{ 'form-' . $form['id'] . '-' . $field['name'] }}"
        value="{{ $field['value'] ?? '' }}"
    />
</div>
