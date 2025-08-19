@extends(config('ogformbuilder.layout'))

@section('title', 'All Forms')

@section('content')
<div class="container">
    <h1 class="mb-4">Form List</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('formbuilder.create') }}" class="btn btn-primary mb-3">Create New Form</a>

    @if($forms->count())
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Form Name</th>
                    <th>Created At</th>
                    <th>Submissions</th>
                    <th>Shortcode</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                    <tr>
                        <td>{{ $form->id }}</td>
                        <td>{{ $form->title }}</td>
                        <td>{{ $form->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $form->submissions()->count() }}</td>
                        <td>{!! "@" . 'ogRenderForm('.$form->id.')' !!} </td>
                        <td>
                            @if ($form->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('formbuilder.settings', $form->id) }}" class="btn btn-sm btn-info">Settings</a>
                            <a href="{{ route('formbuilder.submissions', $form->id) }}" class="btn btn-sm btn-secondary">Submissions</a>
                            <a href="{{ route('formbuilder.edit', $form->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('formbuilder.destroy', $form->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination links -->
        <div class="d-flex justify-content-center pagination">
            {{ $forms->links() }}
        </div>
    @else
        <p>No forms found.</p>
    @endif
</div>
@endsection

@push('styles')
<style>
        /* Pagination */
        .pagination nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            font-size: 14px;
            margin-top: 1rem;
        }

        .pagination nav>div:first-child {
            margin-right: auto;
            color: #6c757d;
        }

        .pagination nav span[aria-current="page"] {
            background-color: #0d6efd;
            color: white;
            padding: 6px 12px;
            margin: 0 4px;
            border-radius: 5px;
            font-weight: bold;
            border: 1px solid #0d6efd;
        }

        .pagination nav a,
        .pagination nav span {
            text-decoration: none;
            color: #6c757d;
            margin: 0 4px;
            padding: 6px 12px;
            border-radius: 5px;
            display: inline-block;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .pagination nav a:hover {
            background-color: #f1f1f1;
            color: #343a40;
            border: 1px solid #dee2e6;
        }
</style>
@endpush

