@extends('layouts.app')

@section('title', 'Stock Report - Super Shop POS')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-clipboard-data"></i> Stock Report</h1>
            <p class="text-muted mb-0">Current inventory levels and stock analysis</p>
        </div>
        <div>
            <a href="{{ route('reports.stock.download', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="{{ route('reports.stock') }}" class="row g-3">
            <div class="col-md-4">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Stock Status</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="low_stock" id="low_stock" value="1" {{ $lowStock ? 'checked' : '' }}>
                    <label class="form-check-label" for="low_stock">
                        Show only low stock items
                    </label>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stock Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h4 class="text-primary">{{ $summary['total_products'] }}</h4>
                <small class="text-muted">Total Products</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h4 class="text-success">{{ $summary['active_products'] }}</h4>
                <small class="text-muted">Active Products</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h4 class="text-warning">{{ $summary['low_stock_products'] }}</h4>
                <small class="text-muted">Low Stock Items</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <h4 class="text-danger">{{ $summary['out_of_stock'] }}</h4>
                <small class="text-muted">Out of Stock</small>
            </div>
        </div>
    </div>
</div>

<!-- Stock Value Summary -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-calculator"></i> Inventory Value Summary</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Total Inventory Value:</strong> ৳{{ number_format($summary['total_value'], 2) }}
                        </div>
                        <div class="col-md-4">
                            <strong>Average Product Value:</strong> ৳{{ number_format($summary['total_products'] > 0 ? $summary['total_value'] / $summary['total_products'] : 0, 2) }}
                        </div>
                        <div class="col-md-4">
                            <strong>Stock Turnover Ratio:</strong>
                            <?php
                            $totalSales = \App\Models\Sale::where('status', 'completed')->sum('final_amount');
                            $avgInventory = $summary['total_value'] / 2; // Rough estimate
                            $turnover = $avgInventory > 0 ? $totalSales / $avgInventory : 0;
                            ?>
                            {{ round($turnover, 1) }}x
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Table -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-box-seam"></i> Current Stock Levels</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Reorder Level</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Stock Value</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->quantity <= $product->reorder_level ? 'table-warning' : '' }}">
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if(!$product->active)
                                <span class="badge bg-secondary ms-1">Inactive</span>
                            @endif
                        </td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $product->quantity <= $product->reorder_level ? 'bg-danger' : 'bg-success' }}">
                                {{ $product->quantity }}
                            </span>
                        </td>
                        <td class="text-center">{{ $product->reorder_level }}</td>
                        <td class="text-end">৳{{ number_format($product->price, 2) }}</td>
                        <td class="text-end">
                            <strong>৳{{ number_format($product->quantity * $product->price, 2) }}</strong>
                        </td>
                        <td class="text-center">
                            @if($product->quantity == 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($product->quantity <= $product->reorder_level)
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle"></i> No products found matching the criteria
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($summary['low_stock_products'] > 0)
<div class="alert alert-warning mt-4">
    <h6><i class="bi bi-exclamation-triangle"></i> Low Stock Alert</h6>
    <p class="mb-0">{{ $summary['low_stock_products'] }} products are below their reorder level. Consider restocking these items to avoid stockouts.</p>
</div>
@endif

<!-- Export Options -->
<div class="mt-4">
    <div class="card">
        <div class="card-body">
            <h6><i class="bi bi-download"></i> Export Options</h6>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary" onclick="exportToCSV()">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export to CSV
                </button>
                <button type="button" class="btn btn-outline-success" onclick="printReport()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    let csv = 'Product,SKU,Category,Brand,Current Stock,Reorder Level,Unit Price,Stock Value,Status\n';
    @foreach($products as $product)
    csv += '"{{ addslashes($product->name) }}","{{ $product->sku }}","{{ $product->category->name ?? 'Uncategorized' }}","{{ $product->brand->name ?? 'No Brand' }}","{{ $product->quantity }}","{{ $product->reorder_level }}","{{ $product->price }}","{{ $product->quantity * $product->price }}","{{ $product->quantity == 0 ? 'Out of Stock' : ($product->quantity <= $product->reorder_level ? 'Low Stock' : 'In Stock') }}"\n';
    @endforeach

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock-report-{{ date('Y-m-d') }}.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function printReport() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .card-header .btn, .alert .btn, nav, .mb-4 .card:first-child {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
