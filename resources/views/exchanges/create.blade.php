@extends('layouts.app')

@section('title', 'Create Exchange - Super Shop POS')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-plus-circle"></i> Create New Exchange</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <strong>Return:</strong> #{{ str_pad($salesReturn->id, 5, '0', STR_PAD_LEFT) }} - 
                    Customer: {{ $salesReturn->customer->name }}
                </div>

                <form method="post" action="{{ route('exchanges.store', $salesReturn) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="returned_product_id" class="form-label">Returned Product <span class="text-danger">*</span></label>
                        <select name="returned_product_id" id="returned_product_id" class="form-select @error('returned_product_id') is-invalid @enderror" required>
                            <option value="">-- Select Product --</option>
                            @foreach($salesReturn->items as $item)
                            <option value="{{ $item->product_id }}" data-price="{{ $item->product->getSalePrice() }}">
                                {{ $item->product->name }} - ₱{{ number_format($item->product->getSalePrice(), 2) }}
                            </option>
                            @endforeach
                        </select>
                        @error('returned_product_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="returned_quantity" class="form-label">Quantity Returned <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="returned_quantity" id="returned_quantity" class="form-control @error('returned_quantity') is-invalid @enderror" value="1" min="1" required>
                        </div>
                        @error('returned_quantity')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_product_id" class="form-label">New Product <span class="text-danger">*</span></label>
                        <select name="new_product_id" id="new_product_id" class="form-select @error('new_product_id') is-invalid @enderror" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->getSalePrice() }}" data-stock="{{ $product->quantity }}">
                                {{ $product->name }} - ৳{{ number_format($product->getSalePrice(), 2) }} (Stock: {{ $product->quantity }})
                            </option>
                            @endforeach
                        </select>
                        @error('new_product_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="new_quantity" id="new_quantity" class="form-control @error('new_quantity') is-invalid @enderror" value="1" min="1" required>
                        @error('new_quantity')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-light">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Returned Value:</small>
                                <p id="returnedValue" class="fw-bold">৳0.00</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">New Product Value:</small>
                                <p id="newValue" class="fw-bold">৳0.00</p>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted">Price Difference:</small>
                            <p id="priceDifference" class="fw-bold">৳0.00</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="price_difference_method" class="form-label">If Price Difference</label>
                        <select name="price_difference_method" id="price_difference_method" class="form-select">
                            <option value="">-- No Action --</option>
                            <option value="cash">Customer Pays Cash</option>
                            <option value="credit_note">Credit Note</option>
                            <option value="cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Create Exchange
                        </button>
                        <a href="{{ route('sales-returns.show', $salesReturn) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Exchange Summary</h5>
            </div>
            <div class="card-body">
                <p><strong>Return ID:</strong> #{{ str_pad($salesReturn->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Customer:</strong> {{ $salesReturn->customer->name }}</p>
                <p><strong>Return Items:</strong> {{ $salesReturn->items->count() }}</p>
            </div>
        </div>
    </div>
</div>

<script>
function calculateDifference() {
    const returnedQty = parseInt(document.getElementById('returned_quantity').value) || 1;
    const newQty = parseInt(document.getElementById('new_quantity').value) || 1;
    
    const returnedSelect = document.getElementById('returned_product_id');
    const newSelect = document.getElementById('new_product_id');
    
    const returnedPrice = parseFloat(returnedSelect.selectedOptions[0]?.dataset?.price) || 0;
    const newPrice = parseFloat(newSelect.selectedOptions[0]?.dataset?.price) || 0;
    
    const returnedValue = returnedQty * returnedPrice;
    const newValue = newQty * newPrice;
    const difference = newValue - returnedValue;
    
    document.getElementById('returnedValue').textContent = '৳' + returnedValue.toFixed(2);
    document.getElementById('newValue').textContent = '৳' + newValue.toFixed(2);
    
    if (difference > 0) {
        document.getElementById('priceDifference').innerHTML = '<span class="text-warning">+৳' + difference.toFixed(2) + ' (Customer pays)</span>';
    } else if (difference < 0) {
        document.getElementById('priceDifference').innerHTML = '<span class="text-success">-৳' + Math.abs(difference).toFixed(2) + ' (Refund)</span>';
    } else {
        document.getElementById('priceDifference').textContent = '৳0.00';
    }
}

document.getElementById('returned_product_id').addEventListener('change', calculateDifference);
document.getElementById('returned_quantity').addEventListener('change', calculateDifference);
document.getElementById('new_product_id').addEventListener('change', calculateDifference);
document.getElementById('new_quantity').addEventListener('change', calculateDifference);

calculateDifference();
</script>
@endsection
