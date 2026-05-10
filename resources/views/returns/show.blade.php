@extends('layouts.app')

@section('title', 'Return Details - Super Shop POS')

@section('content')
<div class="mb-4">
    <a href="{{ route('sales-returns.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Return #{{ str_pad($salesReturn->id, 5, '0', STR_PAD_LEFT) }}</h4>
                    <span class="badge 
                        @if($salesReturn->status == 'pending') bg-warning text-dark
                        @elseif($salesReturn->status == 'approved') bg-success
                        @elseif($salesReturn->status == 'rejected') bg-danger
                        @else bg-primary @endif">
                        {{ ucfirst($salesReturn->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Sale Number:</strong> #{{ str_pad($salesReturn->sale->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p><strong>Customer:</strong> {{ $salesReturn->customer->name }}</p>
                        <p><strong>Return Type:</strong> 
                            @if($salesReturn->return_type == 'return')
                                <span class="badge bg-danger">Return</span>
                            @elseif($salesReturn->return_type == 'exchange')
                                <span class="badge bg-info">Exchange</span>
                            @else
                                <span class="badge bg-warning">Refund</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Created By:</strong> {{ $salesReturn->user->name }}</p>
                        <p><strong>Created:</strong> {{ $salesReturn->created_at->format('M d, Y h:i A') }}</p>
                        @if($salesReturn->approved_by)
                        <p><strong>Approved By:</strong> {{ $salesReturn->approvedBy->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Reason for Return</h5>
                    <p class="text-muted">{{ $salesReturn->reason }}</p>
                </div>

                <div class="mb-4">
                    <h5>Return Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Condition</th>
                                    <th>Return Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesReturn->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity_returned }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($item->condition == 'new') bg-success
                                            @elseif($item->condition == 'used') bg-info
                                            @else bg-danger @endif">
                                            {{ ucfirst($item->condition) }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($item->return_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No items</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>₱{{ number_format($salesReturn->total_returned_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($salesReturn->exchanges->count())
                <div class="mb-4">
                    <h5>Exchanges</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Returned Product</th>
                                    <th>Qty</th>
                                    <th>→</th>
                                    <th>New Product</th>
                                    <th>Qty</th>
                                    <th>Price Difference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesReturn->exchanges as $exchange)
                                <tr>
                                    <td>{{ $exchange->returnedProduct->name }}</td>
                                    <td>{{ $exchange->returned_quantity }}</td>
                                    <td><i class="bi bi-arrow-right"></i></td>
                                    <td>{{ $exchange->newProduct->name }}</td>
                                    <td>{{ $exchange->new_quantity }}</td>
                                    <td>
                                        @if($exchange->price_difference > 0)
                                            <span class="badge bg-warning">+₱{{ number_format($exchange->price_difference, 2) }}</span>
                                        @elseif($exchange->price_difference < 0)
                                            <span class="badge bg-success">-₱{{ number_format(abs($exchange->price_difference), 2) }}</span>
                                        @else
                                            <span class="badge bg-secondary">No difference</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($salesReturn->refund)
                <div class="mb-4">
                    <h5>Refund Information</h5>
                    <p><strong>Amount:</strong> ₱{{ number_format($salesReturn->refund->refund_amount, 2) }}</p>
                    <p><strong>Method:</strong> <span class="badge bg-info">{{ ucfirst($salesReturn->refund->method) }}</span></p>
                    <p><strong>Status:</strong> 
                        <span class="badge 
                            @if($salesReturn->refund->status == 'pending') bg-warning text-dark
                            @elseif($salesReturn->refund->status == 'processed') bg-success
                            @else bg-danger @endif">
                            {{ ucfirst($salesReturn->refund->status) }}
                        </span>
                    </p>
                </div>
                @endif
            </div>

            <div class="card-footer">
                <div class="btn-group" role="group">
                    @if($salesReturn->status == 'pending')
                        <a href="{{ route('sales-returns.edit', $salesReturn) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form method="post" action="{{ route('sales-returns.approve', $salesReturn) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Approve this return?')">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                    @elseif($salesReturn->status == 'approved')
                        @if($salesReturn->return_type == 'exchange')
                            <a href="{{ route('exchanges.create', $salesReturn) }}" class="btn btn-info">
                                <i class="bi bi-arrow-left-right"></i> Add Exchange
                            </a>
                        @endif
                        @if($salesReturn->return_type == 'refund' || $salesReturn->refund_amount > 0)
                            @if(!$salesReturn->refund)
                            <a href="{{ route('refunds.create', $salesReturn) }}" class="btn btn-primary">
                                <i class="bi bi-cash-coin"></i> Process Refund
                            </a>
                            @else
                            <a href="{{ route('refunds.show', $salesReturn->refund) }}" class="btn btn-info">
                                <i class="bi bi-eye"></i> View Refund
                            </a>
                            @endif
                        @endif
                        <form method="post" action="{{ route('sales-returns.complete', $salesReturn) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Complete this return?')">
                                <i class="bi bi-check-all"></i> Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Original Sale</h5>
            </div>
            <div class="card-body">
                <p><strong>Sale ID:</strong> #{{ str_pad($salesReturn->sale->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Date:</strong> {{ $salesReturn->sale->created_at->format('M d, Y') }}</p>
                <p><strong>Final Amount:</strong> ₱{{ number_format($salesReturn->sale->final_amount, 2) }}</p>
                <p><strong>Return Status:</strong> 
                    @if($salesReturn->sale->return_status == 'none')
                        <span class="badge bg-secondary">No Return</span>
                    @elseif($salesReturn->sale->return_status == 'partial')
                        <span class="badge bg-warning">Partial</span>
                    @else
                        <span class="badge bg-danger">Full</span>
                    @endif
                </p>
                <a href="{{ route('sales.show', $salesReturn->sale) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                    View Sale Details
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Returned:</span>
                        <strong>₱{{ number_format($salesReturn->total_returned_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Refund Amount:</span>
                        <strong>₱{{ number_format($salesReturn->refund_amount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('sales-returns.reject', $salesReturn) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason for Rejection</label>
                        <textarea name="reason" id="rejectReason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
