@extends('layouts.app')

@section('title', 'Edit Sale - Super Shop POS')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Sale #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</h1>
</div>

<div class="row">
    <!-- Left Side - Product Selection & Cart -->
    <div class="col-lg-8">
        <!-- Barcode/Search Section -->
        <div class="card mb-3">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-search"></i> Product Search & Barcode</h5>
            </div>
            <div class="card-body">
                <div class="input-group input-group-lg mb-3">
                    <span class="input-group-text"><i class="bi bi-barcode"></i></span>
                    <input type="text" class="form-control" id="barcodeSearch" placeholder="Scan barcode or search by product name/SKU...">
                    <button class="btn btn-outline-primary" type="button" id="searchBtn">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div id="searchResults" class="d-none">
                    <div class="list-group" id="resultsList"></div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-box"></i> Available Products</h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div class="row g-2" id="productsList">
                    @forelse($products as $product)
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-primary w-100 text-start product-btn" 
                            data-product-id="{{ $product->id }}" 
                            data-product-name="{{ $product->name }}"
                            data-product-price="{{ $product->price }}"
                            data-product-sku="{{ $product->sku }}"
                            data-product-quantity="{{ $product->quantity }}">
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <div><strong>{{ $product->name }}</strong></div>
                                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">₱{{ number_format($product->price, 2) }}</div>
                                    <span class="badge {{ $product->quantity > 5 ? 'bg-success' : ($product->quantity > 0 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $product->quantity }}
                                    </span>
                                </div>
                            </div>
                        </button>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">No products available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Cart & Summary -->
    <div class="col-lg-4">
        <form id="saleForm" action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <!-- Customer Section -->
            <div class="card mb-3">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0"><i class="bi bi-person"></i> Customer</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" id="customer_id" name="customer_id">
                        <option value="">👤 Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="card mb-3">
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-cart"></i> Shopping Cart</h5>
                    <span class="badge bg-secondary" id="itemCount">{{ $sale->items->sum('quantity') }}</span>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <div id="cartItems">
                        <div class="list-group" id="cartItemsList"></div>
                    </div>
                </div>
            </div>

            <!-- Discount Section -->
            <div class="card mb-3">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0"><i class="bi bi-percent"></i> Discounts</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Total Discount</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="discount" name="discount" value="{{ $sale->discount }}" step="0.01" min="0">
                            <select class="form-select" name="discount_type" id="discountType" style="max-width: 90px;">
                                <option value="fixed">৳ Fixed</option>
                                <option value="percent">% Percent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax/VAT Section -->
            <div class="card mb-3">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0"><i class="bi bi-receipt"></i> VAT/Tax</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="applyTax" name="apply_tax" value="1" {{ $sale->tax > 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="applyTax">
                            Apply Tax
                        </label>
                    </div>
                    <div class="input-group">
                        <input type="number" class="form-control" id="taxRate" name="tax_rate" value="{{ $sale->tax_rate }}" step="0.01" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <!-- Amount Summary -->
            <div class="card mb-3 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong id="subtotal">₱0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Item Discounts:</span>
                        <strong id="itemDiscountTotal">-₱0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Discount:</span>
                        <strong id="discountAmount">-₱0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax:</span>
                        <strong id="taxAmount2">+₱0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Final Amount:</h5>
                        <h4 class="mb-0 text-primary" id="finalAmount">₱0.00</h4>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card mb-3">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0"><i class="bi bi-credit-card"></i> Payment Method</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="payment_method" id="paymentCash" value="cash" {{ $sale->payment_method == 'cash' ? 'checked' : '' }} required>
                        <label class="btn btn-outline-primary" for="paymentCash">
                            <i class="bi bi-cash-coin"></i> Cash
                        </label>

                        <input type="radio" class="btn-check" name="payment_method" id="paymentCard" value="card" {{ $sale->payment_method == 'card' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="paymentCard">
                            <i class="bi bi-credit-card"></i> Card
                        </label>

                        <input type="radio" class="btn-check" name="payment_method" id="paymentMobile" value="mobile_banking" {{ $sale->payment_method == 'mobile_banking' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="paymentMobile">
                            <i class="bi bi-phone"></i> Mobile Banking
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-3">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0"><i class="bi bi-pencil"></i> Notes</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="notes" rows="2">{{ $sale->notes }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg" name="action" value="complete">
                    <i class="bi bi-check-circle"></i> Complete Sale
                </button>
                <button type="submit" class="btn btn-warning btn-lg" name="action" value="hold">
                    <i class="bi bi-clock-history"></i> Hold Sale
                </button>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductName"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalProductId">
                <input type="hidden" id="modalProductNameHidden">
                <input type="hidden" id="modalProductPrice">
                <input type="hidden" id="modalProductStock">
                <div class="mb-3">
                    <label for="modalQuantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="modalQuantity" value="1" min="1">
                    <div class="form-text text-muted" id="modalStockInfo"></div>
                </div>
                <div class="mb-3">
                    <label for="modalItemDiscount" class="form-label">Item Discount</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="modalItemDiscount" value="0" step="0.01" min="0">
                        <select class="form-select" id="modalItemDiscountType" style="max-width: 90px;">
                            <option value="fixed">৳</option>
                            <option value="percent">%</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addToCart()">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

<script>
let cartItems = {!! json_encode($sale->items->map(function($item) {
    return [
        'product_id' => $item->product_id,
        'name' => $item->product->name,
        'price' => (float)$item->price,
        'quantity' => $item->quantity,
        'item_discount' => (float)$item->item_discount,
        'item_discount_type' => $item->item_discount_type
    ];
})) !!};

const modal = new bootstrap.Modal(document.getElementById('quickAddModal'));

// Initialize cart display
updateCartTable();
calculateTotals();

// Product search
const barcodeSearchInput = document.getElementById('barcodeSearch');
const searchBtn = document.getElementById('searchBtn');
const searchResultsContainer = document.getElementById('searchResults');
const resultsList = document.getElementById('resultsList');

barcodeSearchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        performProductSearch();
    }
});

barcodeSearchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        performProductSearch();
    }
});

searchBtn.addEventListener('click', function() {
    performProductSearch();
});

async function performProductSearch() {
    const query = barcodeSearchInput.value.trim();
    console.log('Search query:', query);

    if (query.length < 2) {
        resultsList.innerHTML = '<p class="text-muted">Enter at least 2 characters to search.</p>';
        searchResultsContainer.classList.remove('d-none');
        return;
    }

    try {
        const response = await fetch(`/sales/search-product?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Search request failed', response.status, errorText);
            throw new Error('Search request failed');
        }

        const products = await response.json();
        console.log('Received products:', products);
        resultsList.innerHTML = '';

        const normalizedQuery = query.toLowerCase();
        const exactMatch = products.length === 1 && (
            (products[0].sku && products[0].sku.toLowerCase() === normalizedQuery) ||
            (products[0].barcode && products[0].barcode.toLowerCase() === normalizedQuery)
        );

        if (exactMatch) {
            openQuickAdd(products[0]);
            barcodeSearchInput.value = '';
            searchResultsContainer.classList.add('d-none');
            return;
        }

        if (products.length === 0) {
            resultsList.innerHTML = '<p class="text-muted">No products found</p>';
        } else {
            products.forEach(product => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action text-start';
                btn.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <strong>${product.name}</strong>
                        <span class="badge bg-info">${product.quantity}</span>
                    </div>
                    <small class="text-muted">${product.sku}${product.barcode ? ' • ' + product.barcode : ''} - ₱${parseFloat(product.price).toFixed(2)}</small>
                `;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openQuickAdd(product);
                    barcodeSearchInput.value = '';
                    searchResultsContainer.classList.add('d-none');
                });
                resultsList.appendChild(btn);
            });
        }

        searchResultsContainer.classList.remove('d-none');
    } catch (error) {
        console.error('Product search error:', error);
        resultsList.innerHTML = '<p class="text-muted">Unable to search products. Try again.</p>';
        searchResultsContainer.classList.remove('d-none');
    }
}

function addProductToCart(product, quantity = 1, itemDiscount = 0, itemDiscountType = 'fixed') {
    const existing = cartItems.find(item => item.product_id == product.id);
    const availableStock = parseInt(product.stock ?? product.quantity ?? 0);

    if (existing) {
        if (existing.quantity + quantity > availableStock) {
            alert(`Cannot add ${quantity} more. Only ${availableStock - existing.quantity} item(s) available in stock.`);
            return;
        }
        existing.quantity += quantity;
        existing.item_discount = itemDiscount;
        existing.item_discount_type = itemDiscountType;
    } else {
        if (quantity > availableStock) {
            alert(`Only ${availableStock} item(s) available in stock.`);
            return;
        }
        cartItems.push({
            product_id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: quantity,
            stock: availableStock,
            item_discount: itemDiscount,
            item_discount_type: itemDiscountType
        });
    }

    updateCartTable();
    calculateTotals();
}

// Product buttons
document.querySelectorAll('.product-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const product = {
            id: this.dataset.productId,
            name: this.dataset.productName,
            price: parseFloat(this.dataset.productPrice),
            sku: this.dataset.productSku,
            quantity: parseInt(this.dataset.productQuantity)
        };
        openQuickAdd(product);
    });
});

function openQuickAdd(product) {
    const stock = parseInt(product.stock ?? product.quantity ?? 0);

    document.getElementById('modalProductId').value = product.id;
    document.getElementById('modalProductNameHidden').value = product.name;
    document.getElementById('modalProductPrice').value = product.price;
    document.getElementById('modalProductStock').value = stock;
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalPrice').textContent = `₱${parseFloat(product.price).toFixed(2)}`;
    document.getElementById('modalQuantity').value = 1;
    document.getElementById('modalItemDiscount').value = 0;
    document.getElementById('modalItemDiscountType').value = 'fixed';
    document.getElementById('modalStockInfo').textContent = `Available stock: ${stock}`;
    modal.show();
}

function addToCart() {
    const productId = document.getElementById('modalProductId').value;
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    const availableStock = parseInt(document.getElementById('modalProductStock').value || 0);
    const itemDiscount = parseFloat(document.getElementById('modalItemDiscount').value) || 0;
    const itemDiscountType = document.getElementById('modalItemDiscountType').value;

    if (quantity < 1) {
        alert('Quantity must be at least 1');
        return;
    }

    if (quantity > availableStock) {
        alert(`Only ${availableStock} item(s) available in stock.`);
        return;
    }

    const product = {
        id: productId,
        name: document.getElementById('modalProductNameHidden').value,
        price: parseFloat(document.getElementById('modalProductPrice').value),
        stock: availableStock
    };

    addProductToCart(product, quantity, itemDiscount, itemDiscountType);
    modal.hide();
}

function addProductToCart(product, quantity = 1, itemDiscount = 0, itemDiscountType = 'fixed') {
    const existing = cartItems.find(item => item.product_id == product.id);
    if (existing) {
        existing.quantity += quantity;
        existing.item_discount = itemDiscount;
        existing.item_discount_type = itemDiscountType;
    } else {
        cartItems.push({
            product_id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: quantity,
            item_discount: itemDiscount,
            item_discount_type: itemDiscountType
        });
    }

    updateCartTable();
    calculateTotals();
}

// Product buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const product = {
                id: this.dataset.productId,
                name: this.dataset.productName,
                price: parseFloat(this.dataset.productPrice),
                sku: this.dataset.productSku,
                quantity: parseInt(this.dataset.productQuantity)
            };
            openQuickAdd(product);
        });
    });
});

function updateCartTable() {
    const cartDiv = document.getElementById('cartItemsList');
    const itemCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('itemCount').textContent = itemCount;

    let html = '';
    cartItems.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        let discount = item.item_discount || 0;
        if (item.item_discount_type === 'percent') {
            discount = (subtotal * discount) / 100;
        }
        const afterDiscount = subtotal - discount;

        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <strong>${item.name}</strong>
                    <button type="button" class="btn-close btn-sm" onclick="removeFromCart(${index})"></button>
                </div>
                <small class="text-muted">₱${parseFloat(item.price).toFixed(2)} × ${item.quantity}</small>
                <div class="d-flex justify-content-between mt-1">
                    <span>₱${subtotal.toFixed(2)}</span>
                    ${discount > 0 ? `<span class="text-danger">-₱${discount.toFixed(2)}</span>` : ''}
                </div>
                <div class="d-flex justify-content-between fw-bold mt-1">
                    <span>Total:</span>
                    <span>₱${afterDiscount.toFixed(2)}</span>
                </div>
            </div>
        `;
    });
    cartDiv.innerHTML = html;
}

function removeFromCart(index) {
    cartItems.splice(index, 1);
    updateCartTable();
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let itemDiscountTotal = 0;

    cartItems.forEach(item => {
        const itemSubtotal = item.price * item.quantity;
        subtotal += itemSubtotal;

        let discount = item.item_discount || 0;
        if (item.item_discount_type === 'percent') {
            discount = (itemSubtotal * discount) / 100;
        }
        itemDiscountTotal += discount;
    });

    const subtotalAfterItemDiscount = subtotal - itemDiscountTotal;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const discountType = document.getElementById('discountType').value;
    const discountAmount = discountType === 'percent' 
        ? (subtotalAfterItemDiscount * discount) / 100 
        : discount;

    const subtotalAfterDiscount = subtotalAfterItemDiscount - discountAmount;

    const applyTax = document.getElementById('applyTax').checked;
    const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    const taxAmount = applyTax ? (subtotalAfterDiscount * taxRate) / 100 : 0;

    const finalAmount = subtotalAfterDiscount + taxAmount;

    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('itemDiscountTotal').textContent = `-₱${itemDiscountTotal.toFixed(2)}`;
    document.getElementById('discountAmount').textContent = `-₱${discountAmount.toFixed(2)}`;
    document.getElementById('taxAmount2').textContent = `+₱${taxAmount.toFixed(2)}`;
    document.getElementById('finalAmount').textContent = `₱${finalAmount.toFixed(2)}`;

    updateFormData();
}

function updateFormData() {
    const form = document.getElementById('saleForm');
    
    document.querySelectorAll('input[name^="items"]').forEach(el => el.remove());

    cartItems.forEach((item, index) => {
        const inputs = [
            ['items[' + index + '][product_id]', item.product_id],
            ['items[' + index + '][quantity]', item.quantity],
            ['items[' + index + '][item_discount]', item.item_discount || 0],
            ['items[' + index + '][item_discount_type]', item.item_discount_type || 'fixed']
        ];

        inputs.forEach(([name, value]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });
    });
}

document.getElementById('discount').addEventListener('change', calculateTotals);
document.getElementById('discountType').addEventListener('change', calculateTotals);
document.getElementById('applyTax').addEventListener('change', calculateTotals);
document.getElementById('taxRate').addEventListener('change', calculateTotals);

document.getElementById('saleForm').addEventListener('submit', function(e) {
    if (cartItems.length === 0) {
        e.preventDefault();
        alert('Please add at least one product to the cart');
    }
});
</script>

<style>
.product-btn:hover {
    background-color: #f0f0f0 !important;
}

#cartItemsList .list-group-item {
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}
</style>
@endsection
