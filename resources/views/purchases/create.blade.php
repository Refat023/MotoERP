@extends('layouts.app')

@section('title', 'New Purchase')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">New Purchase</h1>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Back to purchases</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method</label>
                        <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method') }}">
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Purchase Items</div>
                    <div class="card-body p-0">
                        <table class="table mb-0" id="purchase-items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Cost Price</th>
                                    <th>Qty</th>
                                    <th>Batch</th>
                                    <th>Expiry</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(old('items'))
                                    @foreach(old('items') as $index => $item)
                                        <tr>
                                            <td>
                                                <select name="items[{{ $index }}][product_id]" class="form-control">
                                                    <option value="">Select product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ isset($item['product_id']) && $item['product_id'] == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" name="items[{{ $index }}][cost_price]" class="form-control item-cost-price" value="{{ $item['cost_price'] ?? 0 }}"></td>
                                            <td><input type="number" step="0.01" name="items[{{ $index }}][quantity]" class="form-control item-quantity" value="{{ $item['quantity'] ?? 1 }}"></td>
                                            <td><input type="text" name="items[{{ $index }}][batch_number]" class="form-control" value="{{ $item['batch_number'] ?? '' }}"></td>
                                            <td><input type="date" name="items[{{ $index }}][expiry_date]" class="form-control" value="{{ $item['expiry_date'] ?? '' }}"></td>
                                            <td><input type="text" readonly class="form-control item-subtotal" value="{{ isset($item['cost_price'], $item['quantity']) ? number_format($item['cost_price'] * $item['quantity'], 2) : '0.00' }}"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-item">×</button></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <select name="items[0][product_id]" class="form-control">
                                                <option value="">Select product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="items[0][cost_price]" class="form-control item-cost-price" value="0.00"></td>
                                        <td><input type="number" step="0.01" name="items[0][quantity]" class="form-control item-quantity" value="1"></td>
                                        <td><input type="text" name="items[0][batch_number]" class="form-control"></td>
                                        <td><input type="date" name="items[0][expiry_date]" class="form-control"></td>
                                        <td><input type="text" readonly class="form-control item-subtotal" value="0.00"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-item">×</button></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-outline-primary" id="add-purchase-item">Add Item</button>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" step="0.01" name="amount_paid" class="form-control" value="{{ old('amount_paid', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-md-4 text-end">
                        <p class="mb-1"><strong>Estimated Total:</strong> <span id="estimated-total">0.00</span></p>
                        <p class="mb-0"><strong>Due Amount:</strong> <span id="estimated-due">0.00</span></p>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Save Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const purchaseProducts = @json($products->map(function ($product) {
        return ['id' => $product->id, 'name' => $product->name];
    }));

    function updateInt(value) {
        return parseFloat(value) || 0;
    }

    function recalcTotals() {
        let total = 0;
        document.querySelectorAll('#purchase-items-table tbody tr').forEach(row => {
            const cost = updateInt(row.querySelector('.item-cost-price').value);
            const qty = updateInt(row.querySelector('.item-quantity').value);
            const subtotal = cost * qty;
            row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
            total += subtotal;
        });

        document.getElementById('estimated-total').textContent = total.toFixed(2);
        const amountPaid = updateInt(document.querySelector('[name="amount_paid"]').value);
        document.getElementById('estimated-due').textContent = Math.max(0, total - amountPaid).toFixed(2);
    }

    function updateRowIndices() {
        document.querySelectorAll('#purchase-items-table tbody tr').forEach((row, index) => {
            row.querySelectorAll('select, input').forEach(input => {
                const name = input.getAttribute('name');
                if (!name) return;
                const newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                input.setAttribute('name', newName);
            });
        });
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function buildProductOptions(index) {
        return purchaseProducts.map(product => `
            <option value="${product.id}">${escapeHtml(product.name)}</option>
        `).join('');
    }

    document.addEventListener('click', function(event) {
        if (event.target.matches('#add-purchase-item')) {
            const tbody = document.querySelector('#purchase-items-table tbody');
            const rowCount = tbody.querySelectorAll('tr').length;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="items[${rowCount}][product_id]" class="form-control">
                        <option value="">Select product</option>
                        ${buildProductOptions(rowCount)}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="items[${rowCount}][cost_price]" class="form-control item-cost-price" value="0.00"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][quantity]" class="form-control item-quantity" value="1"></td>
                <td><input type="text" name="items[${rowCount}][batch_number]" class="form-control"></td>
                <td><input type="date" name="items[${rowCount}][expiry_date]" class="form-control"></td>
                <td><input type="text" readonly class="form-control item-subtotal" value="0.00"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item">×</button></td>
            `;
            tbody.appendChild(newRow);
            recalcTotals();
        }

        if (event.target.matches('.remove-item')) {
            const row = event.target.closest('tr');
            const tbody = row.closest('tbody');
            if (tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                updateRowIndices();
                recalcTotals();
            }
        }
    });

    document.addEventListener('input', function(event) {
        if (event.target.matches('.item-cost-price, .item-quantity, [name="amount_paid"]')) {
            recalcTotals();
        }
    });

    recalcTotals();
</script>
@endsection
@endsection
