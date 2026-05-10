@extends('layouts.app')

@section('title', 'Profit & Loss Report - Super Shop POS')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-pie-chart"></i> Profit & Loss Report</h1>
            <p class="text-muted mb-0">Financial performance analysis</p>
        </div>
        <div>
            <a href="{{ route('reports.profit-loss.download', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
</div>

<!-- Date Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="{{ route('reports.profit-loss') }}" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Profit & Loss Summary -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-graph-up"></i> Profit & Loss Summary</h5>
                <small class="text-muted">Period: {{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Revenue -->
                            <tr class="table-success">
                                <td><strong>Total Revenue</strong></td>
                                <td class="text-end"><strong>৳{{ number_format($revenue, 2) }}</strong></td>
                                <td class="text-center"><span class="badge bg-success">Income</span></td>
                            </tr>

                            <!-- Cost of Goods Sold -->
                            <tr class="table-danger">
                                <td>Cost of Goods Sold (COGS)</td>
                                <td class="text-end">-৳{{ number_format($cogs, 2) }}</td>
                                <td class="text-center"><span class="badge bg-danger">Expense</span></td>
                            </tr>

                            <!-- Gross Profit -->
                            <tr class="table-info">
                                <td><strong>Gross Profit</strong></td>
                                <td class="text-end"><strong>৳{{ number_format($grossProfit, 2) }}</strong></td>
                                <td class="text-center"><span class="badge bg-info">Profit</span></td>
                            </tr>

                            <!-- Operating Expenses -->
                            <tr class="table-warning">
                                <td>Operating Expenses</td>
                                <td class="text-end">-৳{{ number_format($expenses, 2) }}</td>
                                <td class="text-center"><span class="badge bg-warning">Expense</span></td>
                            </tr>

                            <!-- Returns & Refunds -->
                            <tr class="table-secondary">
                                <td>Returns & Refunds</td>
                                <td class="text-end">-৳{{ number_format($returns + $refunds, 2) }}</td>
                                <td class="text-center"><span class="badge bg-secondary">Loss</span></td>
                            </tr>

                            <!-- Net Profit/Loss -->
                            <tr class="{{ $netProfit >= 0 ? 'table-primary' : 'table-danger' }}">
                                <td><strong>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</strong></td>
                                <td class="text-end">
                                    <strong class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $netProfit >= 0 ? '+' : '' }}৳{{ number_format($netProfit, 2) }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-speedometer2"></i> Key Metrics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Gross Margin:</span>
                        <strong>{{ $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Net Margin:</span>
                        <strong>{{ $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Expense Ratio:</span>
                        <strong>{{ $revenue > 0 ? round(($expenses / $revenue) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Return Rate:</span>
                        <strong>{{ $revenue > 0 ? round((($returns + $refunds) / $revenue) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Indicator -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <h6>Overall Performance</h6>
                @if($netProfit > 0)
                    <div class="text-success">
                        <i class="bi bi-graph-up" style="font-size: 3rem;"></i>
                        <h4>Profitable</h4>
                    </div>
                @elseif($netProfit == 0)
                    <div class="text-warning">
                        <i class="bi bi-dash-circle" style="font-size: 3rem;"></i>
                        <h4>Break Even</h4>
                    </div>
                @else
                    <div class="text-danger">
                        <i class="bi bi-graph-down" style="font-size: 3rem;"></i>
                        <h4>Loss</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-bar-chart-line"></i> Monthly Performance Trend</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">COGS</th>
                        <th class="text-end">Expenses</th>
                        <th class="text-end">Returns</th>
                        <th class="text-end">Net Profit</th>
                        <th class="text-center">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $month)
                    <tr>
                        <td><strong>{{ $month['month'] }}</strong></td>
                        <td class="text-end">৳{{ number_format($month['revenue'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($month['cogs'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($month['expenses'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($month['returns'], 2) }}</td>
                        <td class="text-end">
                            <?php $net = $month['revenue'] - $month['cogs'] - $month['expenses'] - $month['returns']; ?>
                            <span class="{{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $net >= 0 ? '+' : '' }}৳{{ number_format($net, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <?php $margin = $month['revenue'] > 0 ? round(($net / $month['revenue']) * 100, 1) : 0; ?>
                            <span class="badge {{ $margin >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $margin }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recommendations -->
@if($netProfit < 0)
<div class="alert alert-warning">
    <h6><i class="bi bi-exclamation-triangle"></i> Recommendations</h6>
    <ul class="mb-0">
        @if($expenses > $revenue * 0.3)
            <li>Operating expenses are high ({{ round(($expenses / $revenue) * 100, 1) }}% of revenue). Consider cost reduction measures.</li>
        @endif
        @if(($returns + $refunds) > $revenue * 0.1)
            <li>Return rate is high ({{ round((($returns + $refunds) / $revenue) * 100, 1) }}%). Review product quality and customer satisfaction.</li>
        @endif
        @if($cogs > $revenue * 0.7)
            <li>Cost of goods sold is high. Review supplier pricing and inventory management.</li>
        @endif
    </ul>
</div>
@endif
@endsection
