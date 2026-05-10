@extends('layouts.app')

@section('title', 'Sales Returns - Super Shop POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-arrow-counterclockwise"></i> Sales Returns & Exchanges</h1>
    <a href="{{ route('sales-returns.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Return
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="{{ route('sales-returns.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Sale # or Customer" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="return_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="return" {{ request('return_type') == 'return' ? 'selected' : '' }}>Return</option>
                    <option value="exchange" {{ request('return_type') == 'exchange' ? 'selected' : '' }}>Exchange</option>
                    <option value="refund" {{ request('return_type') == 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('sales-returns.index') }}" class="btn btn-outline-secondary w-100">
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
                    <th>Return ID</th>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                <tr>
                    <td><strong>#{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>#{{ str_pad($return->sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $return->customer->name ?? 'N/A' }}</td>
                    <td>
                        @if($return->return_type == 'return')
                            <span class="badge bg-danger">Return</span>
                        @elseif($return->return_type == 'exchange')
                            <span class="badge bg-info">Exchange</span>
                        @else
                            <span class="badge bg-warning">Refund</span>
                        @endif
                    </td>
                    <td><strong>₱{{ number_format($return->total_returned_amount, 2) }}</strong></td>
                    <td>
                        @if($return->status == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($return->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($return->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-primary">Completed</span>
                        @endif
                    </td>
                    <td>{{ $return->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('sales-returns.show', $return) }}" class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($return->status == 'pending')
                            <a href="{{ route('sales-returns.edit', $return) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No returns found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $returns->links() }}
</div>
@endsection
