@extends('layouts.app')

@section('title', 'Exchanges - Super Shop POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-arrow-left-right"></i> Exchanges</h1>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Exchange ID</th>
                    <th>Return ID</th>
                    <th>Returned Product</th>
                    <th>New Product</th>
                    <th>Price Difference</th>
                    <th>Method</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exchanges as $exchange)
                <tr>
                    <td><strong>#{{ str_pad($exchange->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>#{{ str_pad($exchange->salesReturn->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        {{ $exchange->returnedProduct->name }}
                        <span class="badge bg-light text-dark">x{{ $exchange->returned_quantity }}</span>
                    </td>
                    <td>
                        {{ $exchange->newProduct->name }}
                        <span class="badge bg-light text-dark">x{{ $exchange->new_quantity }}</span>
                    </td>
                    <td>
                        @if($exchange->price_difference > 0)
                            <span class="badge bg-warning">+₱{{ number_format($exchange->price_difference, 2) }}</span>
                        @elseif($exchange->price_difference < 0)
                            <span class="badge bg-success">-₱{{ number_format(abs($exchange->price_difference), 2) }}</span>
                        @else
                            <span class="badge bg-secondary">No difference</span>
                        @endif
                    </td>
                    <td>
                        @if($exchange->price_difference_method)
                            <span class="badge bg-info">{{ ucfirst($exchange->price_difference_method) }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $exchange->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('exchanges.show', $exchange) }}" class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No exchanges found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $exchanges->links() }}
</div>
@endsection
