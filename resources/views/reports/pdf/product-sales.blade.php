<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product-wise Sales Report</title>
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
        .filters {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
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
        .performance-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .high-performance {
            background: #d4edda;
            color: #155724;
        }
        .medium-performance {
            background: #fff3cd;
            color: #856404;
        }
        .low-performance {
            background: #f8d7da;
            color: #721c24;
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
        <h1>Product-wise Sales Report</h1>
        <p>Super Shop POS System</p>
    </div>

    <!-- Filters Info -->
    <div class="filters">
        <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}
        @if($categoryId)
            | <strong>Category:</strong> {{ $categories->find($categoryId)->name ?? 'Unknown' }}
        @endif
    </div>

    <!-- Products Table -->
    <div class="section">
        <h3>Product Sales Performance</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-center">Sales Count</th>
                    <th class="text-center">Total Quantity</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Avg Price</th>
                    <th class="text-center">Performance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category_name ?? 'Uncategorized' }}</td>
                    <td class="text-center">{{ $product->sales_count }}</td>
                    <td class="text-center">{{ $product->total_quantity }}</td>
                    <td class="text-right">TK{{ number_format($product->total_amount, 2) }}</td>
                    <td class="text-right">TK{{ number_format($product->avg_price, 2) }}</td>
                    <td class="text-center">
                        @if($product->total_amount > 1000)
                            <span class="performance-badge high-performance">High</span>
                        @elseif($product->total_amount > 500)
                            <span class="performance-badge medium-performance">Medium</span>
                        @else
                            <span class="performance-badge low-performance">Low</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="no-data">No product sales data found for the selected period</td>
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