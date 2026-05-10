<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('sales', 'transactions')
            ->orderBy('total_purchases', 'desc')
            ->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'address' => 'nullable|string|max:500',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['registration_date'] = now();
        $validated['tier'] = 'bronze';
        $validated['loyalty_points'] = 0;
        $validated['credit_balance'] = 0;

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer registered successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load('sales', 'transactions');
        $purchases = $customer->sales()->latest()->get();
        $transactions = $customer->transactions()->latest()->paginate(10);
        
        return view('customers.show', compact('customer', 'purchases', 'transactions'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string|max:500',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function addCredit(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'credit_amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        if ($customer->credit_balance + $validated['credit_amount'] > $customer->credit_limit) {
            return redirect()->route('customers.show', $customer)
                ->with('error', 'Credit limit exceeded. Available: ₱' . $customer->available_credit);
        }

        $customer->update([
            'credit_balance' => $customer->credit_balance + $validated['credit_amount']
        ]);

        CustomerTransaction::create([
            'customer_id' => $customer->id,
            'type' => 'credit_issued',
            'amount' => $validated['credit_amount'],
            'description' => $validated['notes'] ?? 'Credit issued',
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Credit added successfully.');
    }

    public function payCredit(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,check,bank_transfer,online',
            'reference_id' => 'nullable|string',
        ]);

        if ($validated['payment_amount'] > $customer->credit_balance) {
            return redirect()->route('customers.show', $customer)
                ->with('error', 'Payment exceeds due amount.');
        }

        $customer->update([
            'credit_balance' => $customer->credit_balance - $validated['payment_amount']
        ]);

        CustomerTransaction::create([
            'customer_id' => $customer->id,
            'type' => 'payment',
            'amount' => $validated['payment_amount'],
            'description' => 'Payment received via ' . $validated['payment_method'],
            'reference_id' => $validated['reference_id'],
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Payment recorded successfully.');
    }

    public function redeemPoints(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'points_to_redeem' => 'required|integer|min:1',
        ]);

        if ($validated['points_to_redeem'] > $customer->available_points) {
            return redirect()->route('customers.show', $customer)
                ->with('error', 'Insufficient loyalty points.');
        }

        $customer->update([
            'points_redeemed' => $customer->points_redeemed + $validated['points_to_redeem']
        ]);

        CustomerTransaction::create([
            'customer_id' => $customer->id,
            'type' => 'loyalty_redeemed',
            'loyalty_points' => -$validated['points_to_redeem'],
            'amount' => 0,
            'description' => 'Loyalty points redeemed',
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', $validated['points_to_redeem'] . ' loyalty points redeemed.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer with purchase history.');
        }

        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
