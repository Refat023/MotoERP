@extends('layouts.app')

@section('title', 'Sales - Super Shop POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><i class="bi bi-receipt"></i> Sales Management</h1>
        @if($heldSales > 0)
        <small class="text-warning"><i class="bi bi-exclamation-circle"></i> {{ $heldSales }} held sale(s) waiting to be resumed</small>
        @endif
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Sale
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Final Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td><strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td><span class="badge bg-info">{{ $sale->items->sum('quantity') }}</span></td>
                    <td>৳{{ number_format($sale->total_amount, 2) }}</td>
                    <td>
                        @if($sale->item_discount_total > 0 || $sale->discount > 0)
                        -৳{{ number_format($sale->item_discount_total + $sale->discount, 2) }}
                        @else
                        ৳0.00
                        @endif
                    </td>
                    <td>{{ $sale->tax > 0 ? '+৳' . number_format($sale->tax, 2) : '৳0.00' }}</td>
                    <td><strong>৳{{ number_format($sale->final_amount, 2) }}</strong></td>
                    <td>
                        @if($sale->payment_method == 'cash')
                            <span class="badge bg-success"><i class="bi bi-cash-coin"></i> Cash</span>
                        @elseif($sale->payment_method == 'card')
                            <span class="badge bg-primary"><i class="bi bi-credit-card"></i> Card</span>
                        @else
                            <span class="badge bg-info"><i class="bi bi-phone"></i> Mobile Banking</span>
                        @endif
                    </td>
                    <td>
                        @if($sale->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($sale->status == 'on_hold')
                            <span class="badge bg-warning text-dark">On Hold</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($sale->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-primary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-outline-info" title="View Receipt">
                                <i class="bi bi-printer"></i>
                            </a>
                            @if($sale->status == 'on_hold')
                            <a href="{{ route('sales.resume', $sale->id) }}" class="btn btn-outline-warning" title="Resume Sale">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">No sales found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $sales->links() }}
</div>
@endsection
