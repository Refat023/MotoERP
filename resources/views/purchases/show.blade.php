@extends('layouts.app')

@section('title', 'Purchase Details')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Purchase {{ $purchase->grn_number }}</h1>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Back to purchases</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Supplier</strong>
                    <p>{{ optional($purchase->supplier)->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Purchase Date</strong>
                    <p>{{ $purchase->purchase_date->format('Y-m-d') }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Payment Method</strong>
                    <p>{{ $purchase->payment_method ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Cost</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ optional($item->product)->name ?? 'Deleted product' }}</td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ optional($item->expiry_date)->format('Y-m-d') }}</td>
                            <td>{{ number_format($item->cost_price, 2) }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card p-3">
                <strong>Subtotal</strong>
                <p>{{ number_format($purchase->subtotal, 2) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <strong>Total</strong>
                <p>{{ number_format($purchase->total_amount, 2) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <strong>Due</strong>
                <p>{{ number_format($purchase->due_amount, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
