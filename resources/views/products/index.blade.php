@extends('layouts.app')

@section('title', 'Products - Super Shop POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-box"></i> Products</h1>
    <div>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-folder"></i> Categories
        </a>
        <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-bookmark"></i> Brands
        </a>
        <a href="{{ route('units.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-rulers"></i> Units
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-sm">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Barcode</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Unit</th>
                    <th>MRP / Price</th>
                    <th>Wholesale</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product->sku }}</strong></td>
                    <td>
                        @if($product->barcode)
                            <span class="badge bg-dark" title="{{ $product->barcode }}">{{ substr($product->barcode, 0, 6) }}...</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div>
                            <strong>{{ $product->name }}</strong>
                            @if($product->offer_price)
                                <br><small class="text-danger">Offer: ৳{{ number_format($product->offer_price, 2) }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $categoryName = optional($product->category)->name ?? 'Uncategorized';
                        @endphp
                        @if($categoryName)
                            <span class="badge bg-primary">{{ $categoryName }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($product->brand)
                            <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($product->unit)
                            <span class="badge bg-info">{{ $product->unit->abbreviation }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div>
                            @if($product->mrp)
                                <strong>৳{{ number_format($product->mrp, 2) }}</strong>
                            @else
                                <strong>৳{{ number_format($product->price, 2) }}</strong>
                            @endif
                            @if($product->wholesale_price)
                                <br><small class="text-muted">Cost: ৳{{ number_format($product->wholesale_price, 2) }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($product->wholesale_price && $product->mrp)
                            ৳{{ number_format($product->wholesale_price, 2) }}
                            <br><small class="text-muted">{{ $product->margin }}% margin</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($product->quantity <= $product->reorder_level)
                            <span class="badge bg-danger">{{ $product->quantity }}</span>
                        @else
                            <span class="badge bg-success">{{ $product->quantity }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $product->active ? 'success' : 'secondary' }}">
                            {{ $product->active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline;">
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
                    <td colspan="11" class="text-center text-muted py-4">No products found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>
@endsection
