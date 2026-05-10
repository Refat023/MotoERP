@extends('layouts.app')

@section('title', 'Daily Sales Report - Super Shop POS')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-calendar-day"></i> Daily Sales Report</h1>
            <p class="text-muted mb-0">Sales analysis for {{ $date->format('F j, Y') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.daily-sales.download', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-success me-2">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
</div>

<!-- Date Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="{{ route('reports.daily-sales') }}" class="row g-3">
            <div class="col-md-4">
                <label for="date" class="form-label">Select Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> View Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h4 class="text-primary">{{ $summary['total_sales'] }}</h4>
                <small class="text-muted">Total Sales</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h4 class="text-success">৳{{ number_format($summary['total_amount'], 2) }}</h4>
                <small class="text-muted">Total Revenue</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-info">
            <div class="card-body">
                <h4 class="text-info">{{ $summary['items_sold'] }}</h4>
                <small class="text-muted">Items Sold</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h4 class="text-warning">৳{{ number_format($summary['total_discount'], 2) }}</h4>
                <small class="text-muted">Total Discounts</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Payment Methods -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-credit-card"></i> Payment Methods</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="bi bi-cash-coin text-success"></i> Cash</td>
                                <td>৳{{ number_format($summary['cash_sales'], 2) }}</td>
                                <td>{{ $summary['total_amount'] > 0 ? round(($summary['cash_sales'] / $summary['total_amount']) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-credit-card text-primary"></i> Card</td>
                                <td>৳{{ number_format($summary['card_sales'], 2) }}</td>
                                <td>{{ $summary['total_amount'] > 0 ? round(($summary['card_sales'] / $summary['total_amount']) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-phone text-info"></i> Other</td>
                                <td>৳{{ number_format($summary['other_sales'], 2) }}</td>
                                <td>{{ $summary['total_amount'] > 0 ? round(($summary['other_sales'] / $summary['total_amount']) * 100, 1) : 0 }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Sales -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clock"></i> Hourly Sales Distribution</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th>Sales Count</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hourlySales as $hour)
                            <tr>
                                <td>{{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                                <td>{{ $hour->count }}</td>
                                <td>৳{{ number_format($hour->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No sales data for this day</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-trophy"></i> Top Selling Products</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                        <th>Avg Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>৳{{ number_format($product->amount, 2) }}</td>
                        <td>৳{{ number_format($product->amount / $product->quantity, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No product sales data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detailed Sales List -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-list"></i> Sales Details</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td><strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $sale->items->sum('quantity') }}</td>
                        <td>৳{{ number_format($sale->total_amount, 2) }}</td>
                        <td>৳{{ number_format($sale->discount + $sale->item_discount_total, 2) }}</td>
                        <td>৳{{ number_format($sale->tax, 2) }}</td>
                        <td><strong>৳{{ number_format($sale->final_amount, 2) }}</strong></td>
                        <td>
                            @if($sale->payment_method == 'cash')
                                <span class="badge bg-success">Cash</span>
                            @elseif($sale->payment_method == 'card')
                                <span class="badge bg-primary">Card</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($sale->payment_method) }}</span>
                            @endif
                        </td>
                        <td>{{ $sale->created_at->format('H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No sales for this date</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
