@extends('layouts.app')

@section('title', 'Reports & Analytics - Super Shop POS')

@section('content')
<div class="mb-4">
    <h1><i class="bi bi-bar-chart-line"></i> Reports & Analytics</h1>
    <p class="text-muted">Comprehensive reporting and analytics for your business</p>
</div>

<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <div class="card-title">
                            <i class="bi bi-receipt text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-primary">{{ \App\Models\Sale::where('status', 'completed')->count() }}</h4>
                        <small class="text-muted">Total Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <div class="card-title">
                            <i class="bi bi-cash-coin text-success" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-success">৳{{ number_format(\App\Models\Sale::where('status', 'completed')->sum('final_amount'), 2) }}</h4>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <div class="card-title">
                            <i class="bi bi-box text-info" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-info">{{ \App\Models\Product::where('active', true)->count() }}</h4>
                        <small class="text-muted">Active Products</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <div class="card-title">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-warning">{{ \App\Models\Product::whereColumn('quantity', '<=', 'reorder_level')->count() }}</h4>
                        <small class="text-muted">Low Stock Items</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="col-md-12">
        <div class="row">
            <!-- Sales Reports -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Sales Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('reports.daily-sales') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-calendar-day me-2"></i> Daily Sales Report
                                <small class="text-muted d-block">Detailed daily sales analysis</small>
                            </a>
                            <a href="{{ route('reports.product-sales') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-bar-chart me-2"></i> Product-wise Sales
                                <small class="text-muted d-block">Sales performance by product</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Reports -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Financial Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('reports.profit-loss') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-pie-chart me-2"></i> Profit & Loss Report
                                <small class="text-muted d-block">Revenue, expenses, and profitability</small>
                            </a>
                            <a href="{{ route('reports.cash-summary') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-cash-stack me-2"></i> Cash Summary
                                <small class="text-muted d-block">Opening/closing cash balance</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Reports -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Inventory Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('reports.stock') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-clipboard-data me-2"></i> Stock Report
                                <small class="text-muted d-block">Current inventory levels</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Reports -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Other Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('refunds.report') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Refunds Report
                                <small class="text-muted d-block">Refund processing summary</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
