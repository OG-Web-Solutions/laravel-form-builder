@extends(config('ogformbuilder.layout'))

@section('title', 'Submission Details')

@section('content')
<div class="container">
    <a href="{{ route('formbuilder.submissions', $submission->og_form_id) }}" class="btn btn-primary mb-3">Back to Submissions</a>
    <h1 class="mb-4">Submission Details - #{{ $submission->id }}</h1>
    <div class="mb-3">
        <label><strong>IP Address:</strong></label>
        <div>{{ $submission->ip }}</div>
    </div>
    <div class="mb-3">
        <label><strong>Submitted At:</strong></label>
        <div>{{ $submission->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <hr>

    @foreach($submission->values as $value)
        <div class="mb-3">
            <label><strong>{{ $value->label }}:</strong></label>
            <div>
                @php
                    $fieldValue = $value->largeValue->value ?? $value->value;
                @endphp

                @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $fieldValue))
                    <img src="{{ asset('storage/' . $fieldValue) }}" alt="{{ $value->label }}" style="max-width: 300px;">
                @elseif (preg_match('/\.(pdf|docx?|xlsx?|csv|zip|txt)$/i', $fieldValue))
                    <a href="{{ asset('storage/' . $fieldValue) }}" target="_blank">{{ basename($fieldValue) }}</a>
                @else
                    {{ $fieldValue }}
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
