<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $lowStockProducts = Product::lowStock()->get();
        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->latest()
            ->limit(15)
            ->get();

        return view('inventory.index', compact('products', 'lowStockProducts', 'recentTransactions'));
    }

    public function adjust(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
            'transaction_type' => 'required|in:stock_in,stock_out,damaged,wastage,lost,return,adjustment',
            'batch_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
        ]);

        $transactionType = $validated['transaction_type'];
        $quantity = abs($validated['quantity']);
        $outgoingTypes = ['stock_out', 'damaged', 'wastage', 'lost'];
        $delta = in_array($transactionType, $outgoingTypes) ? -$quantity : $quantity;
        $direction = $delta > 0 ? 'in' : 'out';

        InventoryTransaction::create([
            'product_id' => $product->id,
            'type' => $direction,
            'transaction_type' => $transactionType,
            'quantity' => $quantity,
            'reason' => $validated['reason'],
            'batch_number' => $validated['batch_number'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'user_id' => auth()->id(),
        ]);

        if ($delta !== 0) {
            $product->increment('quantity', $delta);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory adjusted successfully.');
    }
}
