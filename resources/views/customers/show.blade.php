@extends('layouts.app')

@section('title', $customer->name . ' - Customer Details')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h1 class="mb-0 d-inline"><i class="bi bi-person-badge"></i> {{ $customer->name }}</h1>
    </div>
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit Information
    </a>
</div>

<div class="row mb-4">
    <!-- Customer Info Card -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Phone:</strong><br>{{ $customer->phone }}</p>
                        <p class="mb-2"><strong>Email:</strong><br>{{ $customer->email ?? 'N/A' }}</p>
                        @if($customer->contact_person)
                        <p class="mb-2"><strong>Contact Person:</strong><br>{{ $customer->contact_person }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Address:</strong><br>{{ $customer->address ?? 'N/A' }}</p>
                        @if($customer->tax_id)
                        <p class="mb-2"><strong>Tax ID:</strong><br>{{ $customer->tax_id }}</p>
                        @endif
                        <p class="mb-2"><strong>Registered:</strong><br>{{ $customer->registration_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase History -->
        <div class="card mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-receipt-cutoff"></i> Purchase History</h5>
            </div>
            <div class="card-body p-0">
                @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Sale ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $sale)
                            <tr>
                                <td><strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                <td><span class="badge bg-info">{{ $sale->items->sum('quantity') }}</span></td>
                                <td>৳{{ number_format($sale->final_amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ match($sale->payment_method) {
                                        'cash' => 'bg-success',
                                        'card' => 'bg-primary',
                                        'mobile_banking' => 'bg-info',
                                        default => 'bg-secondary'
                                    } }}">
                                        {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-outline-primary" title="View Receipt">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4">No purchases yet</p>
                @endif
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Transaction History</h5>
            </div>
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($transaction->type == 'purchase')
                                    <span class="badge bg-success">Purchase</span>
                                    @elseif($transaction->type == 'payment')
                                    <span class="badge bg-primary">Payment</span>
                                    @elseif($transaction->type == 'loyalty_earned')
                                    <span class="badge bg-info">Loyalty Earned</span>
                                    @elseif($transaction->type == 'loyalty_redeemed')
                                    <span class="badge bg-warning text-dark">Loyalty Redeemed</span>
                                    @elseif($transaction->type == 'credit_issued')
                                    <span class="badge bg-danger">Credit Issued</span>
                                    @elseif($transaction->type == 'credit_applied')
                                    <span class="badge bg-secondary">Credit Applied</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->description }}</td>
                                <td>
                                    @if($transaction->amount > 0)
                                    <span class="text-success">৳{{ number_format($transaction->amount, 2) }}</span>
                                    @elseif($transaction->amount < 0)
                                    <span class="text-danger">-৳{{ number_format(abs($transaction->amount), 2) }}</span>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->loyalty_points != 0)
                                    <span class="badge {{ $transaction->loyalty_points > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $transaction->loyalty_points > 0 ? '+' : '' }}{{ $transaction->loyalty_points }} pts
                                    </span>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $transactions->links() }}
                </div>
                @else
                <p class="text-muted text-center py-4">No transactions</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Cards -->
    <div class="col-lg-4">
        <!-- Loyalty Card -->
        <div class="card mb-4 border-{{ match($customer->tier) {
            'platinum' => 'success',
            'gold' => 'warning',
            'silver' => 'info',
            default => 'secondary'
        } }}">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-gift"></i> Loyalty Program</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Tier Status</strong></p>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ match($customer->tier) {
                            'platinum' => 'bg-success',
                            'gold' => 'bg-warning text-dark',
                            'silver' => 'bg-info',
                            default => 'bg-secondary'
                        } }} style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                            ★ {{ strtoupper($customer->tier) }}
                        </span>
                    </div>
                    <small class="text-muted d-block mt-2">{{ $customer->tier_benefits }}</small>
                </div>

                <hr>

                <div class="mb-3">
                    <p class="mb-1"><strong>Loyalty Points</strong></p>
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span>Earned:</span>
                        <span class="h5 mb-0 text-success">{{ $customer->loyalty_points }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span>Redeemed:</span>
                        <span class="h5 mb-0 text-danger">{{ $customer->points_redeemed }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 1.5rem;">
                        <div class="progress-bar bg-success" style="width: {{ min(100, ($customer->available_points / max(1, $customer->loyalty_points)) * 100) }}%">
                        </div>
                    </div>
                    <small class="text-muted">Available: <strong>{{ $customer->available_points }} pts</strong></small>
                </div>

                @if($customer->available_points > 0)
                <form action="{{ route('customers.redeem-points', $customer) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="input-group input-group-sm mb-2">
                        <input type="number" class="form-control" name="points_to_redeem" 
                            value="{{ $customer->available_points }}" max="{{ $customer->available_points }}" min="1" required>
                        <button type="submit" class="btn btn-outline-success btn-sm">Redeem</button>
                    </div>
                </form>
                @endif
            </div>
        </div>

        <!-- Credit Management Card -->
        <div class="card mb-4 {{ $customer->credit_balance > 0 ? 'border-danger' : 'border-success' }}">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-credit-card"></i> Credit Management</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><small class="text-muted">Credit Limit</small></p>
                    <p class="mb-3"><strong>৳{{ number_format($customer->credit_limit, 2) }}</strong></p>
                </div>

                <div class="mb-3">
                    <p class="mb-1"><small class="text-muted">Available Credit</small></p>
                    <p class="mb-3"><strong class="text-success">৳{{ number_format($customer->available_credit, 2) }}</strong></p>
                </div>

                <div class="mb-3">
                    <p class="mb-1"><small class="text-muted">Due Amount</small></p>
                    <p class="mb-3">
                        @if($customer->due_amount > 0)
                        <strong class="text-danger">৳{{ number_format($customer->due_amount, 2) }}</strong>
                        @else
                        <strong class="text-success">৳0.00 (Paid)</strong>
                        @endif
                    </p>
                </div>

                @if($customer->due_amount > 0)
                <!-- Pay Credit Form -->
                <form action="{{ route('customers.pay-credit', $customer) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label"><small>Payment Amount</small></label>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" name="payment_amount" 
                                value="{{ $customer->due_amount }}" max="{{ $customer->due_amount }}" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label"><small>Payment Method</small></label>
                        <select class="form-select form-select-sm mb-2" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label"><small>Reference ID (Optional)</small></label>
                        <input type="text" class="form-control form-control-sm mb-2" name="reference_id" placeholder="Check #, Transaction ID">
                    </div>
                    <button type="submit" class="btn btn-sm btn-success w-100">
                        <i class="bi bi-check-circle"></i> Record Payment
                    </button>
                </form>
                @endif

                @if($customer->available_credit > 0)
                <!-- Add Credit Form -->
                <button type="button" class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="collapse" data-bs-target="#addCreditForm">
                    <i class="bi bi-plus-circle"></i> Add Credit
                </button>
                <form action="{{ route('customers.add-credit', $customer) }}" method="POST" id="addCreditForm" class="collapse mt-2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label"><small>Credit Amount</small></label>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" name="credit_amount" 
                                max="{{ $customer->available_credit }}" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label"><small>Notes</small></label>
                        <textarea class="form-control form-control-sm mb-2" name="notes" rows="2" placeholder="Reason for credit..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Issue Credit</button>
                </form>
                @endif
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up"></i> Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><small class="text-muted">Total Purchases</small></p>
                    <p class="h5 mb-0">৳{{ number_format($customer->total_purchases, 2) }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><small class="text-muted">Total Transactions</small></p>
                    <p class="h5 mb-0">৳{{ number_format($customer->transactions()->count(), 2) }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><small class="text-muted">Average Purchase</small></p>
                    <p class="h5 mb-0">
                        @php
                        $avgPurchase = $purchases->count() > 0 
                            ? $customer->total_purchases / $purchases->count() 
                            : 0;
                        @endphp
                        ৳{{ number_format($avgPurchase, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
