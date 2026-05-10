@extends('layouts.app')

@section('title', 'Edit Sales Return - Super Shop POS')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-pencil"></i> Edit Return #{{ str_pad($salesReturn->id, 5, '0', STR_PAD_LEFT) }}</h4>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('sales-returns.update', $salesReturn) }}" id="editReturnForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason', $salesReturn->reason) }}</textarea>
                        @error('reason')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Return Items</label>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty Returned</th>
                                        <th>Condition</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesReturn->items as $index => $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity_returned }}</td>
                                        <td>
                                            <select name="items[{{ $item->id }}][condition]" class="form-select form-select-sm" required>
                                                <option value="new" {{ $item->condition == 'new' ? 'selected' : '' }}>New</option>
                                                <option value="used" {{ $item->condition == 'used' ? 'selected' : '' }}>Used</option>
                                                <option value="damaged" {{ $item->condition == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="items[{{ $item->id }}][item_notes]" class="form-control form-control-sm" rows="1">{{ $item->item_notes }}</textarea>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
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
                <h5>Return Summary</h5>
            </div>
            <div class="card-body">
                <p><strong>Sale:</strong> #{{ str_pad($salesReturn->sale->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Customer:</strong> {{ $salesReturn->customer->name }}</p>
                <p><strong>Type:</strong> 
                    @if($salesReturn->return_type == 'return')
                        <span class="badge bg-danger">Return</span>
                    @elseif($salesReturn->return_type == 'exchange')
                        <span class="badge bg-info">Exchange</span>
                    @else
                        <span class="badge bg-warning">Refund</span>
                    @endif
                </p>
                <p><strong>Total Amount:</strong> ₱{{ number_format($salesReturn->total_returned_amount, 2) }}</p>
                <p><strong>Status:</strong>
                    @if($salesReturn->status == 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
