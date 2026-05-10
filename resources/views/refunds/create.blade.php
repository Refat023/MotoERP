@extends('layouts.app')

@section('title', 'Process Refund - Super Shop POS')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-plus-circle"></i> Process Refund</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <strong>Return:</strong> #{{ str_pad($salesReturn->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <strong>Customer:</strong> {{ $salesReturn->customer->name }}<br>
                    <strong>Refund Amount:</strong> ৳{{ number_format($salesReturn->refund_amount, 2) }}
                </div>

                <form method="post" action="{{ route('refunds.store', $salesReturn) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="method" class="form-label">Refund Method <span class="text-danger">*</span></label>
                        <select name="method" id="method" class="form-select @error('method') is-invalid @enderror" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="credit_note" {{ old('method') == 'credit_note' ? 'selected' : '' }}>Credit Note</option>
                            <option value="wallet" {{ old('method') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                        </select>
                        @error('method')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID <span class="text-muted">(optional)</span></label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" placeholder="Reference number" value="{{ old('transaction_id') }}">
                        @error('transaction_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Refund Summary</label>
                        <div class="alert alert-light">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Items Returned:</small>
                                    <p>{{ $salesReturn->items->count() }} item(s)</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Total Amount:</small>
                                    <p><strong>৳{{ number_format($salesReturn->refund_amount, 2) }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Process Refund
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
                <h5>Return Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Return ID:</strong> #{{ str_pad($salesReturn->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Type:</strong> 
                    @if($salesReturn->return_type == 'return')
                        <span class="badge bg-danger">Return</span>
                    @elseif($salesReturn->return_type == 'exchange')
                        <span class="badge bg-info">Exchange</span>
                    @else
                        <span class="badge bg-warning">Refund</span>
                    @endif
                </p>
                <p><strong>Status:</strong> <span class="badge bg-success">Approved</span></p>
                <hr>
                <a href="{{ route('sales-returns.show', $salesReturn) }}" class="btn btn-outline-primary w-100">
                    View Full Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
