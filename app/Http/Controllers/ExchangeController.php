<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\SalesReturn;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    /**
     * Display a listing of exchanges.
     */
    public function index(Request $request)
    {
        $query = Exchange::with([
            'salesReturn',
            'returnedProduct',
            'newProduct',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('salesReturn', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            });
        }

        $exchanges = $query->latest()->paginate(15);

        return view('exchanges.index', compact('exchanges'));
    }

    /**
     * Show the form for creating a new exchange.
     */
    public function create(SalesReturn $salesReturn)
    {
        if ($salesReturn->return_type !== 'exchange' || $salesReturn->status !== 'approved') {
            return back()->with('error', 'Can only create exchanges for approved exchange returns.');
        }

        $products = Product::where('active', true)->get();
        return view('exchanges.create', compact('salesReturn', 'products'));
    }

    /**
     * Store a newly created exchange.
     */
    public function store(Request $request, SalesReturn $salesReturn)
    {
        $validated = $request->validate([
            'returned_product_id' => 'required|exists:products,id',
            'returned_quantity' => 'required|integer|min:1',
            'new_product_id' => 'required|exists:products,id',
            'new_quantity' => 'required|integer|min:1',
            'price_difference_method' => 'nullable|in:cash,credit_note,cancel',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $returnedProduct = Product::find($validated['returned_product_id']);
            $newProduct = Product::find($validated['new_product_id']);

            // Check stock
            if ($newProduct->quantity < $validated['new_quantity']) {
                throw new \Exception("Insufficient stock for {$newProduct->name}");
            }

            // Calculate price difference
            $returnedAmount = $validated['returned_quantity'] * $returnedProduct->getSalePrice();
            $newAmount = $validated['new_quantity'] * $newProduct->getSalePrice();
            $priceDifference = $newAmount - $returnedAmount;

            $exchange = Exchange::create([
                'sales_return_id' => $salesReturn->id,
                'returned_product_id' => $validated['returned_product_id'],
                'returned_quantity' => $validated['returned_quantity'],
                'new_product_id' => $validated['new_product_id'],
                'new_quantity' => $validated['new_quantity'],
                'price_difference' => $priceDifference,
                'price_difference_method' => $validated['price_difference_method'],
                'notes' => $validated['notes'],
            ]);

            // Update inventory
            $returnedProduct->increment('quantity', $validated['returned_quantity']);
            $newProduct->decrement('quantity', $validated['new_quantity']);

            DB::commit();

            return redirect()
                ->route('sales-returns.show', $salesReturn->id)
                ->with('success', 'Exchange created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating exchange: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exchange.
     */
    public function show(Exchange $exchange)
    {
        $exchange->load([
            'salesReturn',
            'returnedProduct',
            'newProduct',
        ]);

        return view('exchanges.show', compact('exchange'));
    }

    /**
     * Show the form for editing the specified exchange.
     */
    public function edit(Exchange $exchange)
    {
        $products = Product::where('active', true)->get();
        return view('exchanges.edit', compact('exchange', 'products'));
    }

    /**
     * Update the specified exchange.
     */
    public function update(Request $request, Exchange $exchange)
    {
        $validated = $request->validate([
            'price_difference_method' => 'nullable|in:cash,credit_note,cancel',
            'notes' => 'nullable|string',
        ]);

        $exchange->update($validated);

        return redirect()
            ->route('sales-returns.show', $exchange->salesReturn->id)
            ->with('success', 'Exchange updated successfully.');
    }

    /**
     * Delete the specified exchange.
     */
    public function destroy(Exchange $exchange)
    {
        try {
            DB::beginTransaction();

            // Reverse inventory changes
            $exchange->returnedProduct->decrement('quantity', $exchange->returned_quantity);
            $exchange->newProduct->increment('quantity', $exchange->new_quantity);

            $salesReturnId = $exchange->salesReturn->id;
            $exchange->delete();

            DB::commit();

            return redirect()
                ->route('sales-returns.show', $salesReturnId)
                ->with('success', 'Exchange deleted and inventory reversed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting exchange: ' . $e->getMessage());
        }
    }
}
