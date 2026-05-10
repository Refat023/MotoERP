<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Summary Report - {{ $date->format('F j, Y') }}</title>
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
        .register-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .register-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .register-info td {
            padding: 3px 0;
            font-size: 11px;
        }
        .register-info .label {
            font-weight: bold;
            width: 120px;
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
        .calculated-row {
            background: #d1ecf1;
        }
        .actual-row {
            background: #fff3cd;
        }
        .variance-row {
            background: #f8d7da;
        }
        .balanced-row {
            background: #d4edda;
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
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        .sales-summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .sales-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }
        .sales-value {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        .sales-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
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
        <h1>Cash Summary Report</h1>
        <p>{{ $date->format('F j, Y') }}</p>
        <p>Super Shop POS System</p>
    </div>

    <!-- Cash Register Status -->
    <div class="register-info">
        <h3 style="margin: 0 0 10px 0; font-size: 14px;">Cash Register Status</h3>
        <table>
            <tr>
                <td class="label">Status:</td>
                <td>{{ ucfirst($session->status) }}</td>
            </tr>
            <tr>
                <td class="label">Opened:</td>
                <td>{{ $session->opened_at->format('M d, Y H:i') }}</td>
            </tr>
            @if($session->closed_at)
            <tr>
                <td class="label">Closed:</td>
                <td>{{ $session->closed_at->format('M d, Y H:i') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Cashier:</td>
                <td>{{ $session->user->name }}</td>
            </tr>
        </table>
    </div>

    <!-- Cash Summary -->
    <div class="section">
        <h3>Cash Summary - {{ $date->format('F j, Y') }}</h3>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Type</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance -->
                <tr class="income-row">
                    <td><strong>Opening Balance</strong></td>
                    <td class="text-right"><strong>TK{{ number_format($session->opening_balance, 2) }}</strong></td>
                    <td class="text-center">Opening</td>
                </tr>

                <!-- Sales -->
                <tr class="income-row">
                    <td>Cash Sales</td>
                    <td class="text-right">+TK{{ number_format($session->cash_sales, 2) }}</td>
                    <td class="text-center">Income</td>
                </tr>
                <tr class="income-row">
                    <td>Card Sales</td>
                    <td class="text-right">+TK{{ number_format($session->card_sales, 2) }}</td>
                    <td class="text-center">Income</td>
                </tr>
                <tr class="income-row">
                    <td>Other Sales</td>
                    <td class="text-right">+TK{{ number_format($session->other_sales, 2) }}</td>
                    <td class="text-center">Income</td>
                </tr>

                <!-- Deductions -->
                <tr class="expense-row">
                    <td>Refunds</td>
                    <td class="text-right">-TK{{ number_format($session->refunds, 2) }}</td>
                    <td class="text-center">Deduction</td>
                </tr>
                <tr class="expense-row">
                    <td>Returns</td>
                    <td class="text-right">-TK{{ number_format($session->returns, 2) }}</td>
                    <td class="text-center">Deduction</td>
                </tr>
                <tr class="expense-row">
                    <td>Expenses</td>
                    <td class="text-right">-TK{{ number_format($session->expenses, 2) }}</td>
                    <td class="text-center">Deduction</td>
                </tr>

                <!-- Expected Balance -->
                <tr class="calculated-row">
                    <td><strong>Expected Balance</strong></td>
                    <td class="text-right"><strong>TK{{ number_format($expectedBalance, 2) }}</strong></td>
                    <td class="text-center">Calculated</td>
                </tr>

                @if($session->closing_balance)
                <!-- Actual Closing Balance -->
                <tr class="actual-row">
                    <td><strong>Actual Closing Balance</strong></td>
                    <td class="text-right"><strong>TK{{ number_format($session->closing_balance, 2) }}</strong></td>
                    <td class="text-center">Actual</td>
                </tr>

                <!-- Variance -->
                <tr class="{{ $session->getVariance() == 0 ? 'balanced-row' : 'variance-row' }}">
                    <td><strong>Variance</strong></td>
                    <td class="text-right">
                        <strong>{{ $session->getVariance() >= 0 ? '+' : '' }}TK{{ number_format($session->getVariance(), 2) }}</strong>
                    </td>
                    <td class="text-center">{{ $session->getVariance() == 0 ? 'Balanced' : 'Discrepancy' }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Sales Summary -->
    <div class="sales-summary">
        <div class="sales-cell">
            <div class="sales-value">{{ $sales->count() }}</div>
            <div class="sales-label">Total Sales</div>
        </div>
        <div class="sales-cell">
            <div class="sales-value">TK{{ number_format($actualCashSales, 2) }}</div>
            <div class="sales-label">Cash Sales</div>
        </div>
        <div class="sales-cell">
            <div class="sales-value">TK{{ number_format($actualCardSales, 2) }}</div>
            <div class="sales-label">Card Sales</div>
        </div>
        <div class="sales-cell">
            <div class="sales-value">TK{{ number_format($sales->sum(function($sale) { return $sale->items->sum('quantity'); }), 2) }}</div>
            <div class="sales-label">Items Sold</div>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="section">
        <h3>Expenses - {{ $date->format('F j, Y') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Payment Method</th>
                    <th class="text-right">Amount</th>
                    <th>Time</th>
                    <th>Added By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expensesList as $expense)
                <tr>
                    <td>{{ $expense->description }}</td>
                    <td>{{ ucfirst($expense->category) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                    <td class="text-right">৳{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->expense_date->format('H:i') }}</td>
                    <td>{{ $expense->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="no-data">No expenses recorded for this date</td>
                </tr>
                @endforelse
            </tbody>
            @if($expensesList->count() > 0)
            <tfoot>
                <tr style="background: #f8f9fa;">
                    <td colspan="3" class="text-right"><strong>Total Expenses:</strong></td>
                    <td class="text-right"><strong>TK{{ number_format($expensesList->sum('amount'), 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y \a\t H:i') }} | Super Shop POS System</p>
    </div>
</body>
</html>