@extends('layouts.app')

@section('title', 'Cash Summary - Super Shop POS')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-cash-stack"></i> Cash Summary</h1>
            <p class="text-muted mb-0">Opening/closing cash balance management</p>
        </div>
        <div>
            <a href="{{ route('reports.cash-summary.download', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-success me-2">
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
        <form method="get" action="{{ route('reports.cash-summary') }}" class="row g-3">
            <div class="col-md-4">
                <label for="date" class="form-label">Select Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> View Summary
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Cash Register Status -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-safe"></i> Cash Register Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Status:</span>
                        <span class="badge {{ $session->status == 'open' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Opened:</span>
                        <strong>{{ $session->opened_at->format('M d, Y H:i') }}</strong>
                    </div>
                </div>
                @if($session->closed_at)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Closed:</span>
                        <strong>{{ $session->closed_at->format('M d, Y H:i') }}</strong>
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Cashier:</span>
                        <strong>{{ $session->user->name }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        @if($session->status == 'open')
        <div class="card mt-3">
            <div class="card-header">
                <h6>Quick Actions</h6>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#closeRegisterModal">
                    <i class="bi bi-safe"></i> Close Register
                </button>
                <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="bi bi-plus-circle"></i> Add Expense
                </button>
            </div>
        </div>
        @else
        <div class="card mt-3">
            <div class="card-body text-center">
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#openRegisterModal">
                    <i class="bi bi-play-circle"></i> Open Register
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Cash Summary -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-calculator"></i> Cash Summary - {{ $date->format('F j, Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Opening Balance -->
                            <tr class="table-primary">
                                <td><strong>Opening Balance</strong></td>
                                <td class="text-end"><strong>৳{{ number_format($session->opening_balance, 2) }}</strong></td>
                                <td class="text-center"><span class="badge bg-primary">Opening</span></td>
                            </tr>

                            <!-- Sales -->
                            <tr class="table-success">
                                <td>Cash Sales</td>
                                <td class="text-end">+৳{{ number_format($session->cash_sales, 2) }}</td>
                                <td class="text-center"><span class="badge bg-success">Income</span></td>
                            </tr>
                            <tr class="table-success">
                                <td>Card Sales</td>
                                <td class="text-end">+৳{{ number_format($session->card_sales, 2) }}</td>
                                <td class="text-center"><span class="badge bg-success">Income</span></td>
                            </tr>
                            <tr class="table-success">
                                <td>Other Sales</td>
                                <td class="text-end">+৳{{ number_format($session->other_sales, 2) }}</td>
                                <td class="text-center"><span class="badge bg-success">Income</span></td>
                            </tr>

                            <!-- Deductions -->
                            <tr class="table-danger">
                                <td>Refunds</td>
                                <td class="text-end">-৳{{ number_format($session->refunds, 2) }}</td>
                                <td class="text-center"><span class="badge bg-danger">Deduction</span></td>
                            </tr>
                            <tr class="table-danger">
                                <td>Returns</td>
                                <td class="text-end">-৳{{ number_format($session->returns, 2) }}</td>
                                <td class="text-center"><span class="badge bg-danger">Deduction</span></td>
                            </tr>
                            <tr class="table-danger">
                                <td>Expenses</td>
                                <td class="text-end">-৳{{ number_format($session->expenses, 2) }}</td>
                                <td class="text-center"><span class="badge bg-danger">Deduction</span></td>
                            </tr>

                            <!-- Expected Balance -->
                            <tr class="table-info">
                                <td><strong>Expected Balance</strong></td>
                                <td class="text-end"><strong>৳{{ number_format($expectedBalance, 2) }}</strong></td>
                                <td class="text-center"><span class="badge bg-info">Calculated</span></td>
                            </tr>

                            @if($session->closing_balance)
                            <!-- Actual Closing Balance -->
                            <tr class="table-warning">
                                <td><strong>Actual Closing Balance</strong></td>
                                <td class="text-end"><strong>৳{{ number_format($session->closing_balance, 2) }}</strong></td>
                                <td class="text-center"><span class="badge bg-warning">Actual</span></td>
                            </tr>

                            <!-- Variance -->
                            <tr class="{{ $session->getVariance() == 0 ? 'table-success' : 'table-danger' }}">
                                <td><strong>Variance</strong></td>
                                <td class="text-end">
                                    <strong class="{{ $session->getVariance() >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $session->getVariance() >= 0 ? '+' : '' }}₱{{ number_format($session->getVariance(), 2) }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $session->getVariance() == 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $session->getVariance() == 0 ? 'Balanced' : 'Discrepancy' }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expenses List -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-receipt"></i> Expenses - {{ $date->format('F j, Y') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Payment Method</th>
                        <th class="text-end">Amount</th>
                        <th>Time</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expensesList as $expense)
                    <tr>
                        <td>{{ $expense->description }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($expense->category) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $expense->payment_method == 'cash' ? 'bg-success' : 'bg-primary' }}">
                                {{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}
                            </span>
                        </td>
                        <td class="text-end">৳{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->expense_date->format('H:i') }}</td>
                        <td>{{ $expense->user->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No expenses recorded for this date</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($expensesList->count() > 0)
                <tfoot>
                    <tr class="table-dark">
                        <td colspan="3" class="text-end"><strong>Total Expenses:</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($expensesList->sum('amount'), 2) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Sales Summary -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-graph-up"></i> Sales Summary - {{ $date->format('F j, Y') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center border-primary mb-3">
                    <div class="card-body">
                        <h5 class="text-primary">{{ $sales->count() }}</h5>
                        <small class="text-muted">Total Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success mb-3">
                    <div class="card-body">
                        <h5 class="text-success">৳{{ number_format($actualCashSales, 2) }}</h5>
                        <small class="text-muted">Cash Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info mb-3">
                    <div class="card-body">
                        <h5 class="text-info">৳{{ number_format($actualCardSales, 2) }}</h5>
                        <small class="text-muted">Card Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-warning mb-3">
                    <div class="card-body">
                        <h5 class="text-warning">{{ $sales->sum(function($sale) { return $sale->items->sum('quantity'); }) }}</h5>
                        <small class="text-muted">Items Sold</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Open Register Modal -->
<div class="modal fade" id="openRegisterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('reports.open-register') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Open Cash Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="opening_balance" class="form-label">Opening Balance</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="opening_balance" id="opening_balance" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Open Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Close Register Modal -->
<div class="modal fade" id="closeRegisterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('reports.close-register') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Close Cash Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Expected Balance:</strong> ₱{{ number_format($expectedBalance, 2) }}
                    </div>
                    <div class="mb-3">
                        <label for="closing_balance" class="form-label">Actual Closing Balance</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="closing_balance" id="closing_balance" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any notes about the closing..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Close Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('reports.add-expense') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" name="description" id="description" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="utilities">Utilities</option>
                            <option value="supplies">Supplies</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="marketing">Marketing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expense_date" class="form-label">Date & Time</label>
                        <input type="datetime-local" name="expense_date" id="expense_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="expense_notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="expense_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
