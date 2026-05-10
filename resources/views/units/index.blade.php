@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">Units of Measurement</h1>
        <a href="{{ route('units.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Unit
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
                        <th>Unit Name</th>
                        <th>Abbreviation</th>
                        <th>Conversion Factor</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($units as $unit)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $unit->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $unit->abbreviation }}</span>
                            </td>
                            <td>
                                {{ number_format($unit->conversion_factor, 4) }}
                            </td>
                            <td>
                                {{ Str::limit($unit->description ?? 'N/A', 40) }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $unit->products_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $unit->active ? 'success' : 'danger' }}">
                                    {{ $unit->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('units.edit', $unit) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('units.destroy', $unit) }}" method="POST" style="display: inline;">
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                No units found. <a href="{{ route('units.create') }}">Create one now.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $units->links() }}
    </div>
</div>
@endsection
