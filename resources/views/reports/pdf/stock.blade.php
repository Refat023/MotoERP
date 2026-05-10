<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report</title>
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
            width: 20%;
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
        .inventory-summary {
            background: #e9ecef;
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
        .low-stock {
            background: #fff3cd;
        }
        .out-of-stock {
            background: #f8d7da;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .in-stock {
            background: #d4edda;
            color: #155724;
        }
        .low-stock-badge {
            background: #fff3cd;
            color: #856404;
        }
        .out-of-stock-badge {
            background: #f8d7da;
            color: #721c24;
        }
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin-top: 20px;
        }
        .alert-box h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 12px;
        }
        .alert-box p {
            margin: 0;
            font-size: 10px;
            color: #856404;
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
        <h1>Stock Report</h1>
        <p>Current inventory levels and stock analysis</p>
        <p>Super Shop POS System</p>
    </div>

    <!-- Stock Summary Cards -->
    <div class="summary-grid">
        <div class="summary-cell">
            <div class="summary-value">{{ $summary['total_products'] }}</div>
            <div class="summary-label">Total Products</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">{{ $summary['active_products'] }}</div>
            <div class="summary-label">Active Products</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">{{ $summary['low_stock_products'] }}</div>
            <div class="summary-label">Low Stock Items</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">{{ $summary['out_of_stock'] }}</div>
            <div class="summary-label">Out of Stock</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">TK{{ number_format($summary['total_value'], 2) }}</div>
            <div class="summary-label">Total Value</div>
        </div>
    </div>

    <!-- Inventory Value Summary -->
    <div class="inventory-summary">
        <strong>Inventory Value Summary:</strong>
        Total Inventory Value: TK{{ number_format($summary['total_value'], 2) }} |
        Average Product Value: TK{{ number_format($summary['total_products'] > 0 ? $summary['total_value'] / $summary['total_products'] : 0, 2) }} |
        Stock Turnover Ratio: <?php $totalSales = \App\Models\Sale::where('status', 'completed')->sum('final_amount'); $avgInventory = $summary['total_value'] / 2; $turnover = $avgInventory > 0 ? $totalSales / $avgInventory : 0; echo round($turnover, 1); ?>x
    </div>

    <!-- Stock Table -->
    <div class="section">
        <h3>Current Stock Levels</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th class="text-center">Current Stock</th>
                    <th class="text-center">Reorder Level</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Stock Value</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ $product->quantity <= $product->reorder_level ? 'low-stock' : '' }}">
                    <td>
                        {{ $product->name }}
                        @if(!$product->active)
                            <span style="color: #6c757d; font-size: 9px;">(Inactive)</span>
                        @endif
                    </td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                    <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                    <td class="text-center">{{ $product->quantity }}</td>
                    <td class="text-center">{{ $product->reorder_level }}</td>
                    <td class="text-right">TK{{ number_format($product->price, 2) }}</td>
                    <td class="text-right">TK{{ number_format($product->quantity * $product->price, 2) }}</td>
                    <td class="text-center">
                        @if($product->quantity == 0)
                            <span class="status-badge out-of-stock-badge">Out of Stock</span>
                        @elseif($product->quantity <= $product->reorder_level)
                            <span class="status-badge low-stock-badge">Low Stock</span>
                        @else
                            <span class="status-badge in-stock">In Stock</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="no-data">No products found matching the criteria</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($summary['low_stock_products'] > 0)
    <div class="alert-box">
        <h4>Low Stock Alert</h4>
        <p>{{ $summary['low_stock_products'] }} products are below their reorder level. Consider restocking these items to avoid stockouts.</p>
    </div>
    @endif

    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y \a\t H:i') }} | Super Shop POS System</p>
    </div>
</body>
</html>