@extends('layouts.app')

@section('title', 'Dashboard - Super Shop POS')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboardsssadasxcccccc</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #28a745;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Revenue</h6>
                    <h3 class="mb-0 text-success">৳{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <i class="" style="font-size: 2rem; color: #28a745; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #007bff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Sales</h6>
                    <h3 class="mb-0 text-primary">{{ $totalSales }}</h3>
                </div>
                <i class="bi bi-receipt" style="font-size: 2rem; color: #007bff; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #17a2b8;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Customers</h6>
                    <h3 class="mb-0 text-info">{{ $totalCustomers }}</h3>
                </div>
                <i class="bi bi-people" style="font-size: 2rem; color: #17a2b8; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #fd7e14;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Products</h6>
                    <h3 class="mb-0" style="color: #fd7e14;">{{ $totalProducts }}</h3>
                </div>
                <i class="bi bi-box" style="font-size: 2rem; color: #fd7e14; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <strong>Low Stock Alert!</strong> {{ $lowStockProducts }} products are running low.
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recent Sales</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>#{{ $sale->id }}</td>
                                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td><span class="badge bg-primary">{{ $sale->items->count() }}</span></td>
                                <td>৳{{ number_format($sale->final_amount, 2) }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($sale->payment_method) }}</span></td>
                                <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No sales yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
