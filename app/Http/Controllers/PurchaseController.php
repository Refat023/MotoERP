<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\InventoryTransaction;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.batch_number' => 'nullable|string|max:255',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $items = collect($request->input('items'));
            $subtotal = $items->sum(fn($item) => $item['cost_price'] * $item['quantity']);
            $tax_total = 0;
            $discount_total = 0;
            $total_amount = $subtotal + $tax_total - $discount_total;
            $amount_paid = $request->input('amount_paid');
            $due_amount = max(0, $total_amount - $amount_paid);

            $supplier = $request->input('supplier_id') ? Supplier::find($request->input('supplier_id')) : null;

            $purchase = Purchase::create([
                'supplier_id' => $request->input('supplier_id'),
                'grn_number' => strtoupper('GRN-'.Str::random(8)),
                'purchase_date' => $request->input('purchase_date'),
                'subtotal' => $subtotal,
                'tax_total' => $tax_total,
                'discount_total' => $discount_total,
                'total_amount' => $total_amount,
                'amount_paid' => $amount_paid,
                'due_amount' => $due_amount,
                'payment_method' => $request->input('payment_method'),
                'status' => $due_amount === 0 ? 'paid' : 'pending',
                'notes' => $request->input('notes'),
                'created_by' => auth()->id(),
            ]);

            if ($supplier && $due_amount > 0) {
                $supplier->increment('due_balance', $due_amount);
            }

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];
                $cost_price = $item['cost_price'];
                $subtotalItem = $cost_price * $quantity;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'cost_price' => $cost_price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotalItem,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $subtotalItem,
                ]);

                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'transaction_type' => 'stock_in',
                    'quantity' => $quantity,
                    'reason' => 'Purchase GRN '.$purchase->grn_number,
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'reference' => $purchase->grn_number,
                    'user_id' => auth()->id(),
                ]);

                $product->increment('quantity', $quantity);
                $product->update(['cost_price' => $cost_price]);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase entry saved successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');

        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }
}
