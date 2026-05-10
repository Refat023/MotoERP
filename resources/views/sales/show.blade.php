@extends('layouts.app')

@section('title', 'Sale Details - Super Shop POS')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="mb-0"><i class="bi bi-receipt"></i> Sale #{{ $sale->id }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0">Sale Items</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>৳{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0">Sale Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <strong>৳{{ number_format($sale->total_amount, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <strong>-৳{{ number_format($sale->discount, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>Tax:</span>
                    <strong>+৳{{ number_format($sale->tax, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="h5">Total:</span>
                    <strong class="h5">৳{{ number_format($sale->final_amount, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0">Transaction Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Sale ID:</strong> #{{ $sale->id }}</p>
                <p><strong>Cashier:</strong> {{ $sale->user->name }}</p>
                <p><strong>Customer:</strong> {{ $sale->customer->name ?? 'Walk-in' }}</p>
                <p><strong>Payment Method:</strong> <span class="badge bg-success">{{ ucfirst($sale->payment_method) }}</span></p>
                <p><strong>Date/Time:</strong> {{ $sale->created_at->format('M d, Y h:i A') }}</p>
                <p><strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($sale->status) }}</span></p>
                @if($sale->notes)
                <p><strong>Notes:</strong> {{ $sale->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Back to Sales</a>
    <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-outline-primary">
        <i class="bi bi-printer"></i> View Receipt
    </a>
    <button onclick="window.print()" class="btn btn-outline-info">
        <i class="bi bi-printer"></i> Print
    </button>
</div>
@endsection
