@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">Sub-categories</h1>
        <a href="{{ route('subcategories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Sub-category
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-lg">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sub-category Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subCategories as $subCategory)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $subCategory->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $subCategory->category->name }}</span>
                            </td>
                            <td>
                                {{ Str::limit($subCategory->description ?? 'N/A', 40) }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $subCategory->products_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $subCategory->active ? 'success' : 'danger' }}">
                                    {{ $subCategory->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('subcategories.edit', $subCategory) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('subcategories.destroy', $subCategory) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No sub-categories found. <a href="{{ route('subcategories.create') }}">Create one now.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $subCategories->links() }}
    </div>
</div>
@endsection
