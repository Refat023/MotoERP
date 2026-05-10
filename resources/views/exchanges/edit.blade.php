@extends('layouts.app')

@section('title', 'Edit Exchange - Super Shop POS')

@section('content')
<div class="mb-4">
    <a href="{{ route('exchanges.show', $exchange) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-pencil"></i> Edit Exchange #{{ str_pad($exchange->id, 5, '0', STR_PAD_LEFT) }}</h4>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('exchanges.update', $exchange) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="price_difference_method" class="form-label">Price Difference Method</label>
                        <select name="price_difference_method" id="price_difference_method" class="form-select">
                            <option value="">-- No Action --</option>
                            <option value="cash" {{ $exchange->price_difference_method == 'cash' ? 'selected' : '' }}>Customer Pays Cash</option>
                            <option value="credit_note" {{ $exchange->price_difference_method == 'credit_note' ? 'selected' : '' }}>Credit Note</option>
                            <option value="cancel" {{ $exchange->price_difference_method == 'cancel' ? 'selected' : '' }}>Cancel</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ $exchange->notes }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                        <a href="{{ route('exchanges.show', $exchange) }}" class="btn btn-secondary">Cancel</a>
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
                <p><strong>Returned:</strong> {{ $exchange->returnedProduct->name }} x{{ $exchange->returned_quantity }}</p>
                <p><strong>New:</strong> {{ $exchange->newProduct->name }} x{{ $exchange->new_quantity }}</p>
                <p><strong>Price Difference:</strong> 
                    @if($exchange->price_difference > 0)
                        <span class="badge bg-warning">+₱{{ number_format($exchange->price_difference, 2) }}</span>
                    @elseif($exchange->price_difference < 0)
                        <span class="badge bg-success">-₱{{ number_format(abs($exchange->price_difference), 2) }}</span>
                    @else
                        No difference
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
