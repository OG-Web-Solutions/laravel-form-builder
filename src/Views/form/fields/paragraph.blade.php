<div class="form-group">
    @if(isset($field['subtype']) )
        <{{ $field['subtype'] }} class="form-control-static{{ isset($field['className']) ? ' '.$field['className'] : '' }}" aria-hidden="{{ isset($field['access']) && $field['access'] === false ? 'true' : 'false' }}">
            {{ $field['label'] ?? '' }}
        </{{ $field['subtype'] }}>
    @endif
</div>
