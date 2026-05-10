@extends('layouts.app')

@section('title', 'Product-wise Sales Report - Super Shop POS')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-bar-chart"></i> Product-wise Sales Report</h1>
            <p class="text-muted mb-0">Sales performance analysis by product</p>
        </div>
        <div>
            <a href="{{ route('reports.product-sales.download', request()->query()) }}" class="btn btn-success me-2">
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
        <form method="get" action="{{ route('reports.product-sales') }}" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
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
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}
            @if($categoryId)
                | <strong>Category:</strong> {{ $categories->find($categoryId)->name ?? 'Unknown' }}
            @endif
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-box"></i> Product Sales Performance</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th class="text-center">Sales Count</th>
                        <th class="text-center">Total Quantity</th>
                        <th class="text-end">Total Revenue</th>
                        <th class="text-end">Avg Price</th>
                        <th class="text-center">Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                        </td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->category_name ?? 'Uncategorized' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $product->sales_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $product->total_quantity }}</span>
                        </td>
                        <td class="text-end">
                            <strong>৳{{ number_format($product->total_amount, 2) }}</strong>
                        </td>
                        <td class="text-end">৳{{ number_format($product->avg_price, 2) }}</td>
                        <td class="text-center">
                            @if($product->total_amount > 1000)
                                <span class="badge bg-success">High</span>
                            @elseif($product->total_amount > 500)
                                <span class="badge bg-warning">Medium</span>
                            @else
                                <span class="badge bg-secondary">Low</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle"></i> No product sales data found for the selected period
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

<!-- Export Options -->
<div class="mt-4">
    <div class="card">
        <div class="card-body">
            <h6><i class="bi bi-download"></i> Export Options</h6>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary" onclick="exportToCSV()">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export to CSV
                </button>
                <button type="button" class="btn btn-outline-success" onclick="exportToPDF()">
                    <i class="bi bi-file-earmark-pdf"></i> Export to PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    // Simple CSV export - in production, you'd want a proper export library
    let csv = 'Product,SKU,Category,Sales Count,Total Quantity,Total Revenue,Avg Price\n';
    @foreach($products as $product)
    csv += '"{{ addslashes($product->name) }}","{{ $product->sku }}","{{ $product->category_name ?? 'Uncategorized' }}","{{ $product->sales_count }}","{{ $product->total_quantity }}","{{ $product->total_amount }}","{{ $product->avg_price }}"\n';
    @endforeach

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'product-sales-report-{{ $startDate }}-{{ $endDate }}.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function exportToPDF() {
    // In production, you'd implement PDF export using libraries like jsPDF or server-side PDF generation
    alert('PDF export functionality would be implemented with a proper PDF library');
}
</script>
@endsection
