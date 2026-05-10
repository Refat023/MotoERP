<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
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
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .summary-table th {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        .summary-table td {
            font-size: 11px;
        }
        .income-row {
            background: #d4edda;
        }
        .expense-row {
            background: #f8d7da;
        }
        .profit-row {
            background: #d1ecf1;
        }
        .loss-row {
            background: #f8d7da;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
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
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .metric-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }
        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        .metric-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
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
        .performance-indicator {
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .profitable {
            background: #d4edda;
            color: #155724;
        }
        .break-even {
            background: #fff3cd;
            color: #856404;
        }
        .loss {
            background: #f8d7da;
            color: #721c24;
        }
        .recommendations {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin-top: 20px;
        }
        .recommendations h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 12px;
        }
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
        }
        .recommendations li {
            font-size: 10px;
            margin-bottom: 5px;
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
        <h1>Profit & Loss Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</p>
        <p>Super Shop POS System</p>
    </div>

    <!-- Profit & Loss Summary -->
    <div class="section">
        <h3>Profit & Loss Summary</h3>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Type</th>
                </tr>
            </thead>
            <tbody>
                <!-- Revenue -->
                <tr class="income-row">
                    <td><strong>Total Revenue</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($revenue, 2) }}</strong></td>
                    <td class="text-center">Income</td>
                </tr>

                <!-- Cost of Goods Sold -->
                <tr class="expense-row">
                    <td>Cost of Goods Sold (COGS)</td>
                    <td class="text-right">-৳{{ number_format($cogs, 2) }}</td>
                    <td class="text-center">Expense</td>
                </tr>

                <!-- Gross Profit -->
                <tr class="profit-row">
                    <td><strong>Gross Profit</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($grossProfit, 2) }}</strong></td>
                    <td class="text-center">Profit</td>
                </tr>

                <!-- Operating Expenses -->
                <tr class="expense-row">
                    <td>Operating Expenses</td>
                    <td class="text-right">-৳{{ number_format($expenses, 2) }}</td>
                    <td class="text-center">Expense</td>
                </tr>

                <!-- Returns & Refunds -->
                <tr class="expense-row">
                    <td>Returns & Refunds</td>
                    <td class="text-right">-৳{{ number_format($returns + $refunds, 2) }}</td>
                    <td class="text-center">Loss</td>
                </tr>

                <!-- Net Profit/Loss -->
                <tr class="{{ $netProfit >= 0 ? 'profit-row' : 'loss-row' }}">
                    <td><strong>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</strong></td>
                    <td class="text-right">
                        <strong>{{ $netProfit >= 0 ? '+' : '' }}৳{{ number_format($netProfit, 2) }}</strong>
                    </td>
                    <td class="text-center">{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-cell">
            <div class="metric-value">{{ $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0 }}%</div>
            <div class="metric-label">Gross Margin</div>
        </div>
        <div class="metric-cell">
            <div class="metric-value">{{ $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0 }}%</div>
            <div class="metric-label">Net Margin</div>
        </div>
        <div class="metric-cell">
            <div class="metric-value">{{ $revenue > 0 ? round(($expenses / $revenue) * 100, 1) : 0 }}%</div>
            <div class="metric-label">Expense Ratio</div>
        </div>
        <div class="metric-cell">
            <div class="metric-value">{{ $revenue > 0 ? round((($returns + $refunds) / $revenue) * 100, 1) : 0 }}%</div>
            <div class="metric-label">Return Rate</div>
        </div>
    </div>

    <!-- Performance Indicator -->
    <div class="performance-indicator {{ $netProfit > 0 ? 'profitable' : ($netProfit == 0 ? 'break-even' : 'loss') }}">
        Overall Performance: {{ $netProfit > 0 ? 'Profitable' : ($netProfit == 0 ? 'Break Even' : 'Loss') }}
    </div>

    <!-- Monthly Trend -->
    <div class="section">
        <h3>Monthly Performance Trend</h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">COGS</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right">Returns</th>
                    <th class="text-right">Net Profit</th>
                    <th class="text-center">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyData as $month)
                <tr>
                    <td><strong>{{ $month['month'] }}</strong></td>
                    <td class="text-right">৳{{ number_format($month['revenue'], 2) }}</td>
                    <td class="text-right">৳{{ number_format($month['cogs'], 2) }}</td>
                    <td class="text-right">৳{{ number_format($month['expenses'], 2) }}</td>
                    <td class="text-right">৳{{ number_format($month['returns'], 2) }}</td>
                    <td class="text-right">
                        <?php $net = $month['revenue'] - $month['cogs'] - $month['expenses'] - $month['returns']; ?>
                        <span>{{ $net >= 0 ? '+' : '' }}৳{{ number_format($net, 2) }}</span>
                    </td>
                    <td class="text-center">
                        <?php $margin = $month['revenue'] > 0 ? round(($net / $month['revenue']) * 100, 1) : 0; ?>
                        {{ $margin }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($netProfit < 0)
    <div class="recommendations">
        <h4>Recommendations</h4>
        <ul>
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

    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y \a\t H:i') }} | Super Shop POS System</p>
    </div>
</body>
</html>