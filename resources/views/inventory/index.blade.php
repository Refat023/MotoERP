@extends('layouts.app')

@section('title', 'Inventory - Super Shop POS')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-0"><i class="bi bi-graph-up"></i> Inventory Management</h1>
    </div>
</div>

@if($lowStockProducts->count() > 0)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Low Stock Alert</h4>
    <p class="mb-0">The following products have stock levels below the reorder level:</p>
    <ul class="mb-0 mt-2">
        @foreach($lowStockProducts as $product)
        <li><strong>{{ $product->name }}</strong> - Current: {{ $product->quantity }}, Reorder Level: {{ $product->reorder_level }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Barcode</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
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
                            <span class="text-muted">{{ $product->barcode }}</span>
                        @else
                            <span class="text-secondary">N/A</span>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td><span class="badge bg-secondary">{{ $product->category->name ?? 'Uncategorized' }}</span></td>
                    <td class="text-center">
                        @if($product->quantity <= $product->reorder_level)
                            <span class="badge bg-danger" style="font-size: 0.9rem;">{{ $product->quantity }}</span>
                        @elseif($product->quantity <= $product->reorder_level + 10)
                            <span class="badge bg-warning text-dark" style="font-size: 0.9rem;">{{ $product->quantity }}</span>
                        @else
                            <span class="badge bg-success" style="font-size: 0.9rem;">{{ $product->quantity }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $product->reorder_level }}</td>
                    <td>
                        @if($product->quantity <= $product->reorder_level)
                            <span class="badge bg-danger">Critical</span>
                        @elseif($product->quantity <= $product->reorder_level + 10)
                            <span class="badge bg-warning text-dark">Low</span>
                        @else
                            <span class="badge bg-success">OK</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustModal" onclick="prepareAdjustModal({{ $product->id }}, '{{ $product->name }}')">
                            <i class="bi bi-pencil"></i> Adjust
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No products found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Adjust Inventory Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Inventory - <span id="productNameModal">-</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adjustQuantity" class="form-label">Quantity Adjustment <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="adjustQuantity" name="quantity" required>
                        <small class="text-muted">Use positive numbers to add stock, negative to remove</small>
                    </div>
                    <div class="mb-3">
                        <label for="transactionType" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="transactionType" name="transaction_type" required>
                            <option value="stock_in">Stock In</option>
                            <option value="stock_out">Stock Out</option>
                            <option value="damaged">Damaged</option>
                            <option value="wastage">Wastage</option>
                            <option value="lost">Lost</option>
                            <option value="return">Return</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustReason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <select class="form-select" id="adjustReason" name="reason" required>
                            <option value="">Select a reason...</option>
                            <option value="Stock Received">Stock Received</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Wastage">Wastage</option>
                            <option value="Lost/Stolen">Lost/Stolen</option>
                            <option value="Inventory Count Adjustment">Inventory Count Adjustment</option>
                            <option value="Return">Return</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batchNumber" class="form-label">Batch Number</label>
                            <input type="text" class="form-control" id="batchNumber" name="batch_number" placeholder="e.g. BATCH-001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expiryDate" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiry_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference</label>
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="Invoice #, lot code, or note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Inventory Transactions</h5>
            </div>
            <div class="card-body">
                @if($recentTransactions->count())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Reason</th>
                                    <th>Batch</th>
                                    <th>Expiry</th>
                                    <th>Reference</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $transaction->product?->name ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type === 'in' ? 'success' : 'danger' }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type ?? $transaction->type)) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->quantity }}</td>
                                    <td>{{ $transaction->reason }}</td>
                                    <td>{{ $transaction->batch_number ?? '—' }}</td>
                                    <td>{{ $transaction->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                                    <td>{{ $transaction->reference ?? '—' }}</td>
                                    <td>{{ $transaction->user?->name ?? 'System' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No inventory activity recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function prepareAdjustModal(productId, productName) {
    document.getElementById('productNameModal').textContent = productName;
    document.getElementById('adjustQuantity').value = '';
    document.getElementById('transactionType').value = 'stock_in';
    document.getElementById('adjustReason').value = '';
    document.getElementById('batchNumber').value = '';
    document.getElementById('expiryDate').value = '';
    document.getElementById('reference').value = '';
    const form = document.getElementById('adjustForm');
    form.action = `/inventory/${productId}/adjust`;
}
</script>
@endsection
