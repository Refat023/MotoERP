@extends('layouts.app')

@section('title', 'Refunds - Super Shop POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-cash-coin"></i> Refund Management</h1>
    <a href="{{ route('refunds.report') }}" class="btn btn-outline-info">
        <i class="bi bi-bar-chart"></i> Refund Report
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="{{ route('refunds.index') }}" class="row g-3">
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ request('method') == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="credit_note" {{ request('method') == 'credit_note' ? 'selected' : '' }}>Credit Note</option>
                    <option value="wallet" {{ request('method') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Transaction ID" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('refunds.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Refund ID</th>
                    <th>Return ID</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                <tr>
                    <td><strong>#{{ str_pad($refund->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                    <td><a href="{{ route('sales-returns.show', $refund->salesReturn) }}">#{{ str_pad($refund->salesReturn->id, 5, '0', STR_PAD_LEFT) }}</a></td>
                    <td><strong>₱{{ number_format($refund->refund_amount, 2) }}</strong></td>
                    <td>
                        <span class="badge 
                            @if($refund->method == 'cash') bg-success
                            @elseif($refund->method == 'card') bg-primary
                            @elseif($refund->method == 'credit_note') bg-info
                            @else bg-warning @endif">
                            {{ ucfirst($refund->method) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge 
                            @if($refund->status == 'pending') bg-warning text-dark
                            @elseif($refund->status == 'processed') bg-success
                            @else bg-danger @endif">
                            {{ ucfirst($refund->status) }}
                        </span>
                    </td>
                    <td>{{ $refund->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('refunds.show', $refund) }}" class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No refunds found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $refunds->links() }}
</div>
@endsection
