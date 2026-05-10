@extends('layouts.app')

@section('title', 'Exchange Details - Super Shop POS')

@section('content')
<div class="mb-4">
    <a href="{{ route('exchanges.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-arrow-left-right"></i> Exchange #{{ str_pad($exchange->id, 5, '0', STR_PAD_LEFT) }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Return:</strong> #{{ str_pad($exchange->salesReturn->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p><strong>Customer:</strong> {{ $exchange->salesReturn->customer->name }}</p>
                        <p><strong>Created:</strong> {{ $exchange->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                    </div>
                </div>

                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <h6>Returned Product</h6>
                                <p><strong>{{ $exchange->returnedProduct->name }}</strong></p>
                                <p><small>Quantity: {{ $exchange->returned_quantity }}</small></p>
                                <p><small>Price: ৳{{ number_format($exchange->returnedProduct->getSalePrice(), 2) }}/unit</small></p>
                                <p class="fw-bold">Value: ৳{{ number_format($exchange->returned_quantity * $exchange->returnedProduct->getSalePrice(), 2) }}</p>
                            </div>
                            <div class="col-2 text-center d-flex align-items-center justify-content-center">
                                <i class="bi bi-arrow-left-right" style="font-size: 2rem;"></i>
                            </div>
                            <div class="col-5">
                                <h6>New Product</h6>
                                <p><strong>{{ $exchange->newProduct->name }}</strong></p>
                                <p><small>Quantity: {{ $exchange->new_quantity }}</small></p>
                                <p><small>Price: ৳{{ number_format($exchange->newProduct->getSalePrice(), 2) }}/unit</small></p>
                                <p class="fw-bold">Value: ৳{{ number_format($exchange->new_quantity * $exchange->newProduct->getSalePrice(), 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h6>Price Difference</h6>
                    @if($exchange->price_difference > 0)
                        <p class="mb-0"><strong class="text-warning">+৳{{ number_format($exchange->price_difference, 2) }}</strong> - Customer to pay</p>
                    @elseif($exchange->price_difference < 0)
                        <p class="mb-0"><strong class="text-success">-৳{{ number_format(abs($exchange->price_difference), 2) }}</strong> - Customer to receive</p>
                    @else
                        <p class="mb-0"><strong>No difference</strong></p>
                    @endif
                    @if($exchange->price_difference_method)
                        <p class="mb-0">Method: <span class="badge bg-secondary">{{ ucfirst($exchange->price_difference_method) }}</span></p>
                    @endif
                </div>

                @if($exchange->notes)
                <div class="mb-4">
                    <h6>Notes</h6>
                    <p class="text-muted">{{ $exchange->notes }}</p>
                </div>
                @endif
            </div>

            <div class="card-footer">
                <a href="{{ route('exchanges.edit', $exchange) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form method="post" action="{{ route('exchanges.destroy', $exchange) }}" style="display:inline;" onsubmit="return confirm('Delete this exchange?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Related Return</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('sales-returns.show', $exchange->salesReturn) }}" class="btn btn-outline-primary w-100">
                    View Return Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
