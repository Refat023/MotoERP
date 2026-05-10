@extends('layouts.app')

@section('title', 'Purchases')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Purchases</h1>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">New Purchase</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>GRN</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->grn_number }}</td>
                            <td>{{ optional($purchase->supplier)->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($purchase->total_amount, 2) }}</td>
                            <td>{{ number_format($purchase->amount_paid, 2) }}</td>
                            <td>{{ number_format($purchase->due_amount, 2) }}</td>
                            <td>{{ ucfirst($purchase->status) }}</td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-secondary">View</a>
                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this purchase?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No purchases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
