@extends(config('ogformbuilder.layout'))

@section('title', 'Form Submissions')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Form Submissions - {{ optional($form)->title ?? 'Untitled Form' }}</h1>
            <div class="d-flex align-items-center gap-2 mb-4">
                <a href="{{ route('formbuilder.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Forms
                </a>
                <a href="{{ route('formbuilder.settings', $form->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Settings
                </a>
                <a href="{{ route('formbuilder.edit', $form->id) }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>Edit Form
                </a>
            </div>
        </div>

        {{-- Success & Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($submissions->count())
            {{-- Search Form --}}
            <form method="GET" class="card card-body bg-light mb-4" action="{{ route('formbuilder.submissions', $form->id) }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Search by label or value" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary me-2">Search</button>
                        <a href="{{ route('formbuilder.submissions', $form->id) }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="{{ route('formbuilder.submissions.export', $form->id) }}" class="btn btn-success me-2">
                        <i class="bi bi-download"></i> Export to CSV
                    </a>
                    <button id="labelSettingsBtn" class="btn btn-outline-secondary">
                        <i class="bi bi-gear"></i> Configure Columns
                    </button>
                </div>
            </div>

            {{-- Submissions Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IP Address</th>
                            <th scope="col">Submitted At</th>
                            @foreach ($uniqueLabels as $label)
                                <th scope="col">{{ $label }}</th>
                            @endforeach
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submissions as $submission)
                            <tr>
                                <td>{{ ($submissions->currentPage() - 1) * $submissions->perPage() + $loop->iteration }}</td>
                                <td>{{ $submission->ip }}</td>
                                <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>

                                @foreach ($uniqueLabels as $label)
                                    @php
                                        $field = $submission->values->firstWhere('label', $label);
                                        $fieldValue = $field ? $field->largeValue->value ?? $field->value : '-';
                                    @endphp
                                    <td>
                                        @if (preg_match('/\.(jpg|jpeg|png|gif|pdf|docx?|xlsx?|txt|csv|zip)$/i', $fieldValue))
                                            <a href="{{ asset('storage/' . $fieldValue) }}" class="text-primary"
                                                target="_blank">{{ basename($fieldValue) }}</a>
                                        @else
                                            {{ Str::limit($fieldValue, 50) }}
                                        @endif
                                    </td>
                                @endforeach

                                <td>
                                    <div class="btn-groupp" role="group">
                                        <a href="{{ route('formbuilder.submissions.show', $submission->id) }}"
                                            class="btn btn-sm btn-info me-2">View</a>
                                        <form action="{{ route('formbuilder.submissions.destroy', $submission->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this submission?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <nav aria-label="Submissions pagination" class="mt-4">
                {{ $submissions->appends(request()->query())->links('pagination::bootstrap-5') }}
            </nav>

            {{-- Modal --}}
            <div class="modal fade" id="labelSettingsModal" tabindex="-1" aria-labelledby="labelSettingsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="labelSettingsModalLabel">Select Fields to Display</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="labelSettingsForm">
                                @foreach ($allLabels as $label)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="labels[]"
                                            value="{{ $label }}" id="label_{{ $loop->index }}" checked>
                                        <label class="form-check-label" for="label_{{ $loop->index }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="labelSettingsForm" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                No submissions found.
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        const modal = document.getElementById('labelSettingsModal');
        const openBtn = document.getElementById('labelSettingsBtn');

        const modalClose = document.querySelectorAll('[data-bs-dismiss="modal"]');
        modalClose.forEach(btn => {
            btn.addEventListener('click', function() {
                modal.classList.remove('show');
                modal.style.display = 'none';
            });
        });

        openBtn.addEventListener('click', function() {
            modal.classList.add('show');
            modal.style.display = 'block';
        });

        document.getElementById('labelSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedLabels = Array.from(
                this.querySelectorAll('input[name="labels[]"]:checked')
            ).map(cb => cb.value);

            localStorage.setItem('selectedLabels', JSON.stringify(selectedLabels));

            const params = new URLSearchParams(window.location.search);
            params.set('visible_labels', selectedLabels.join(','));
            window.location.search = params.toString();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const stored = localStorage.getItem('selectedLabels');

            if (params.has('visible_labels') && stored) {
                const selected = JSON.parse(stored);
                document.querySelectorAll('#labelSettingsForm input[type="checkbox"]').forEach(cb => {
                    cb.checked = selected.includes(cb.value);
                });
            } else {
                localStorage.removeItem('selectedLabels');
            }
        });
    </script>
@endpush
