@extends('layouts.app')

@section('title', 'Create Sales Return - Super Shop POS')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4><i class="bi bi-plus-circle"></i> Create New Return / Exchange / Refund</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('sales-returns.store') }}" id="returnForm">
                        @csrf

                        <div class="mb-3">
                            <label for="sale_id" class="form-label">Select Sale <span class="text-danger">*</span></label>
                            <select name="sale_id" id="sale_id" class="form-select @error('sale_id') is-invalid @enderror"
                                required>
                                <option value="">-- Choose a Sale --</option>
                                @foreach ($sales as $sale)
                                    <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                        #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }} -
                                        {{ $sale->customer->name ?? 'Walk-in' }} -
                                        ৳{{ number_format($sale->final_amount, 2) }}
                                        ({{ $sale->created_at->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('sale_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="return_type" class="form-label">Return Type <span
                                    class="text-danger">*</span></label>
                            <select name="return_type" id="return_type"
                                class="form-select @error('return_type') is-invalid @enderror" required>
                                <option value="return" {{ old('return_type') == 'return' ? 'selected' : '' }}>Return
                                </option>
                                <option value="exchange" {{ old('return_type') == 'exchange' ? 'selected' : '' }}>Exchange
                                </option>
                                <option value="refund" {{ old('return_type') == 'refund' ? 'selected' : '' }}>Refund
                                </option>
                            </select>
                            @error('return_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" rows="3"
                                required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="saleItemsContainer" class="mb-3">
                            <label class="form-label">Items to Return <span class="text-danger">*</span></label>
                            <div id="itemsList" class="mb-3"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Create Return
                            </button>
                            <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Sale Details</h5>
                </div>
                <div class="card-body" id="saleDetailsCard">
                    <p class="text-muted">Select a sale to view details</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sale_id').addEventListener('change', async function() {
            const saleId = this.value;
            if (!saleId) {
                document.getElementById('itemsList').innerHTML = '';
                document.getElementById('saleDetailsCard').innerHTML =
                    '<p class="text-muted">Select a sale to view details</p>';
                return;
            }

            try {
                const response = await fetch(`/sales-returns/${saleId}/details`);
                const sale = await response.json();

                // Display sale details
                let detailsHtml = `
            <p><strong>Customer:</strong> ${sale.customer?.name || 'Walk-in'}</p>
            <p><strong>Total Amount:</strong> ৳${parseFloat(sale.final_amount).toFixed(2)}</p>
            <p><strong>Date:</strong> ${new Date(sale.created_at).toLocaleDateString()}</p>
            <p><strong>Items:</strong> ${sale.items.length}</p>
        `;
                document.getElementById('saleDetailsCard').innerHTML = detailsHtml;

                // Render items
                let itemsHtml = '';
                sale.items.forEach((item, index) => {
                    itemsHtml += `
                <div class="card mb-2">
                    <div class="card-body p-2">
                        <div class="mb-2">
                            <strong>${item.product.name}</strong>
                            <span class="badge bg-light text-dark float-end">৳${parseFloat(item.price).toFixed(2)}</span>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Qty Purchased:</small> ${item.quantity}
                            </div>
                            <div class="col-6">
                                <input type="number" name="items[${index}][quantity_returned]" 
                                       class="form-control form-control-sm" min="1" max="${item.quantity}" 
                                       placeholder="Qty" value="1" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <select name="items[${index}][condition]" class="form-select form-select-sm" required>
                                <option value="new">New</option>
                                <option value="used" selected>Used</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                        <input type="hidden" name="items[${index}][sale_item_id]" value="${item.id}">
                        <textarea name="items[${index}][item_notes]" class="form-control form-control-sm" 
                                  placeholder="Notes (optional)" rows="1"></textarea>
                    </div>
                </div>
            `;
                });
                document.getElementById('itemsList').innerHTML = itemsHtml;
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading sale details');
            }
        });

        // Load initial sale if editing
        window.addEventListener('load', function() {
            const saleSelect = document.getElementById('sale_id');
            if (saleSelect.value) {
                saleSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
