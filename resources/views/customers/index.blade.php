@extends('layouts.app')

@section('title', 'Customers - Super Shop POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><i class="bi bi-people"></i> Customer Management</h1>
        <small class="text-muted">Total Customers: <strong>{{ count($customers) }}</strong></small>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Register Customer
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Tier</th>
                    <th>Total Purchases</th>
                    <th>Loyalty Points</th>
                    <th>Due Amount</th>
                    <th>Date Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td><strong>{{ $customer->name }}</strong></td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>
                        <span class="badge {{ match($customer->tier) {
                            'platinum' => 'bg-success',
                            'gold' => 'bg-warning text-dark',
                            'silver' => 'bg-info',
                            default => 'bg-secondary'
                        } }}">
                            {{ ucfirst($customer->tier) }}
                        </span>
                    </td>
                    <td>৳{{ number_format($customer->total_purchases, 2) }}</td>
                    <td>
                        <span title="Available: {{ $customer->available_points }}">
                            {{ $customer->loyalty_points }} pts
                        </span>
                    </td>
                    <td>
                        @if($customer->credit_balance > 0)
                        <span class="badge bg-danger">
                            ৳{{ number_format($customer->credit_balance, 2) }}
                        </span>
                        @else
                        <span class="badge bg-success">Paid</span>
                        @endif
                    </td>
                    <td>{{ $customer->registration_date?->format('M d, Y') ?? '-' }}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($customer->sales()->count() == 0)
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No customers found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $customers->links() }}
</div>
@endsection
