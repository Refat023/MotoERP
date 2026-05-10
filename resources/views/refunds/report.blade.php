@extends('layouts.app')

@section('title', 'Refunds Report - Super Shop POS')

@section('content')
<div class="mb-4">
    <h1><i class="bi bi-bar-chart"></i> Refunds Report</h1>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="{{ route('refunds.report') }}" class="row g-3">
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('refunds.report') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Refunds</h6>
                <h3>{{ $totals['total_refunds'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Amount</h6>
                <h3 class="text-success">৳{{ number_format($totals['total_amount'], 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Processed</h6>
                <h3 class="text-success">{{ $totals['processed'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Pending</h6>
                <h3 class="text-warning">{{ $totals['pending'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Refunds by Method</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Count</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($totals['by_method'] ?? [] as $method => $amount)
                    <tr>
                        <td>
                            <span class="badge 
                                @if($method == 'cash') bg-success
                                @elseif($method == 'card') bg-primary
                                @elseif($method == 'credit_note') bg-info
                                @else bg-warning @endif">
                                {{ ucfirst($method) }}
                            </span>
                        </td>
                        <td>{{ $refunds->where('method', $method)->count() }}</td>
                        <td><strong>৳{{ number_format($amount, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Refund Details</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Refund ID</th>
                    <th>Return ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                <tr>
                    <td><a href="{{ route('refunds.show', $refund) }}">#{{ str_pad($refund->id, 5, '0', STR_PAD_LEFT) }}</a></td>
                    <td><a href="{{ route('sales-returns.show', $refund->salesReturn) }}">#{{ str_pad($refund->salesReturn->id, 5, '0', STR_PAD_LEFT) }}</a></td>
                    <td>{{ $refund->salesReturn->customer->name }}</td>
                    <td><strong>৳{{ number_format($refund->refund_amount, 2) }}</strong></td>
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
@endsection
