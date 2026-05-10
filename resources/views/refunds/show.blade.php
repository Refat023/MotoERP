@extends('layouts.app')

@section('title', 'Refund Details - Super Shop POS')

@section('content')
<div class="mb-4">
    <a href="{{ route('refunds.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Refund #{{ str_pad($refund->id, 5, '0', STR_PAD_LEFT) }}</h4>
                    <span class="badge 
                        @if($refund->status == 'pending') bg-warning text-dark
                        @elseif($refund->status == 'processed') bg-success
                        @else bg-danger @endif">
                        {{ ucfirst($refund->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Return:</strong> <a href="{{ route('sales-returns.show', $refund->salesReturn) }}">#{{ str_pad($refund->salesReturn->id, 5, '0', STR_PAD_LEFT) }}</a></p>
                        <p><strong>Customer:</strong> {{ $refund->salesReturn->customer->name }}</p>
                        <p><strong>Refund Amount:</strong> <strong class="text-success">₱{{ number_format($refund->refund_amount, 2) }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Method:</strong> <span class="badge 
                            @if($refund->method == 'cash') bg-success
                            @elseif($refund->method == 'card') bg-primary
                            @elseif($refund->method == 'credit_note') bg-info
                            @else bg-warning @endif">{{ ucfirst($refund->method) }}</span></p>
                        <p><strong>Created:</strong> {{ $refund->created_at->format('M d, Y h:i A') }}</p>
                        @if($refund->processed_at)
                        <p><strong>Processed:</strong> {{ $refund->processed_at->format('M d, Y h:i A') }}</p>
                        @endif
                    </div>
                </div>

                <div class="alert alert-light">
                    <h6>Refund Information</h6>
                    <p><strong>Amount:</strong> ₱{{ number_format($refund->refund_amount, 2) }}</p>
                    <p><strong>Method:</strong> {{ ucfirst($refund->method) }}</p>
                    @if($refund->transaction_id)
                    <p><strong>Transaction ID:</strong> {{ $refund->transaction_id }}</p>
                    @endif
                    @if($refund->processedBy)
                    <p><strong>Processed By:</strong> {{ $refund->processedBy->name }}</p>
                    @endif
                </div>

                @if($refund->status == 'failed')
                <div class="alert alert-danger">
                    <strong>Failure Reason:</strong> {{ $refund->failure_reason }}
                </div>
                @endif

                <div class="mb-4">
                    <h6>Related Return Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refund->salesReturn->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity_returned }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td>₱{{ number_format($item->return_amount, 2) }}</td>
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
            </div>

            <div class="card-footer">
                @if($refund->status == 'pending')
                    <form method="post" action="{{ route('refunds.process', $refund) }}" style="display:inline;">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" name="transaction_id" class="form-control" placeholder="Transaction ID (optional)">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Mark as Processed
                            </button>
                        </div>
                    </form>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#failModal">
                        <i class="bi bi-x-circle"></i> Mark as Failed
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Original Sale</h5>
            </div>
            <div class="card-body">
                <p><strong>Sale ID:</strong> <a href="{{ route('sales.show', $refund->salesReturn->sale) }}">#{{ str_pad($refund->salesReturn->sale->id, 6, '0', STR_PAD_LEFT) }}</a></p>
                <p><strong>Date:</strong> {{ $refund->salesReturn->sale->created_at->format('M d, Y') }}</p>
                <p><strong>Original Amount:</strong> ₱{{ number_format($refund->salesReturn->sale->final_amount, 2) }}</p>
                <hr>
                <a href="{{ route('sales.show', $refund->salesReturn->sale) }}" class="btn btn-outline-primary w-100">
                    View Sale Details
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Failed Modal -->
<div class="modal fade" id="failModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('refunds.mark-failed', $refund) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Refund as Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="failure_reason" class="form-label">Reason for Failure</label>
                        <textarea name="failure_reason" id="failure_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Mark as Failed</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
