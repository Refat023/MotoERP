@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">Brands</h1>
        <a href="{{ route('brands.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Brand
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
                        <th>Logo</th>
                        <th>Brand Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td>
                                @if($brand->logo)
                                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" style="height: 40px;" class="rounded">
                                @else
                                    <div class="bg-light text-muted rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $brand->name }}</span>
                            </td>
                            <td>
                                {{ Str::limit($brand->description ?? 'N/A', 40) }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $brand->products_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $brand->active ? 'success' : 'danger' }}">
                                    {{ $brand->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('brands.edit', $brand) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('brands.destroy', $brand) }}" method="POST" style="display: inline;">
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
                                No brands found. <a href="{{ route('brands.create') }}">Create one now.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $brands->links() }}
    </div>
</div>
@endsection
