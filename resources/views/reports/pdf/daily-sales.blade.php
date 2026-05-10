<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Sales Report - {{ $date->format('F j, Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Sales Report</h1>
        <p>{{ $date->format('F j, Y') }}</p>
        <p>Super Shop POS System</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-cell">
            <div class="summary-value">{{ $summary['total_sales'] }}</div>
            <div class="summary-label">Total Sales</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">TK{{ number_format($summary['total_amount'], 2) }}</div>
            <div class="summary-label">Total Revenue</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">{{ $summary['items_sold'] }}</div>
            <div class="summary-label">Items Sold</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">TK{{ number_format($summary['total_discount'], 2) }}</div>
            <div class="summary-label">Total Discounts</div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="section">
        <h3>Payment Methods</h3>
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cash</td>
                    <td class="text-right">TK{{ number_format($summary['cash_sales'], 2) }}</td>
                    <td class="text-center">{{ $summary['total_amount'] > 0 ? round(($summary['cash_sales'] / $summary['total_amount']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Card</td>
                    <td class="text-right">TK{{ number_format($summary['card_sales'], 2) }}</td>
                    <td class="text-center">{{ $summary['total_amount'] > 0 ? round(($summary['card_sales'] / $summary['total_amount']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Other</td>
                    <td class="text-right">TK{{ number_format($summary['other_sales'], 2) }}</td>
                    <td class="text-center">{{ $summary['total_amount'] > 0 ? round(($summary['other_sales'] / $summary['total_amount']) * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Hourly Sales -->
    <div class="section">
        <h3>Hourly Sales Distribution</h3>
        <table>
            <thead>
                <tr>
                    <th>Hour</th>
                    <th class="text-center">Sales Count</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hourlySales as $hour)
                <tr>
                    <td>{{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                    <td class="text-center">{{ $hour->count }}</td>
                    <td class="text-right">TK{{ number_format($hour->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="no-data">No sales data for this day</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Products -->
    <div class="section">
        <h3>Top Selling Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Quantity Sold</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td class="text-center">{{ $product->quantity }}</td>
                    <td class="text-right">TK{{ number_format($product->amount, 2) }}</td>
                    <td class="text-right">TK{{ number_format($product->amount / $product->quantity, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-data">No product sales data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Detailed Sales List -->
    <div class="section">
        <h3>Sales Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th class="text-center">Items</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Total</th>
                    <th>Payment</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td class="text-center">{{ $sale->items->sum('quantity') }}</td>
                    <td class="text-right">TK{{ number_format($sale->total_amount, 2) }}</td>
                    <td class="text-right">TK{{ number_format($sale->discount + $sale->item_discount_total, 2) }}</td>
                    <td class="text-right">TK{{ number_format($sale->tax, 2) }}</td>
                    <td class="text-right">TK{{ number_format($sale->final_amount, 2) }}</td>
                    <td>{{ ucfirst($sale->payment_method) }}</td>
                    <td>{{ $sale->created_at->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="no-data">No sales for this date</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y \a\t H:i') }} | Super Shop POS System</p>
    </div>
</body>
</html>