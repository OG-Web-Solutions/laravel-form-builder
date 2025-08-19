<div class="form-group {{ $field['className'] ?? '' }}">
    @php
        $headerTag = isset($field['subtype']) ? $field['subtype'] : 'h5';
    @endphp
    <{{ $headerTag }}>{{ $field['label'] }}</{{ $headerTag }}>
    @if(isset($field['description']))
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif
</div>
