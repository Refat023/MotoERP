<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer', 'items')
            ->where('status', '!=', 'draft')
            ->latest()
            ->paginate(15);
        $heldSales = Sale::where('status', 'on_hold')->count();
        return view('sales.index', compact('sales', 'heldSales'));
    }

    public function create()
    {
        $products = Product::active()->get();
        $customers = Customer::all();
        $heldSales = Sale::where('status', 'on_hold')->get();
        $taxRate = 15; // Default VAT rate
        return view('sales.create', compact('products', 'customers', 'heldSales', 'taxRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_discount' => 'nullable|numeric|min:0',
            'items.*.item_discount_type' => 'nullable|in:fixed,percent',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percent',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'apply_tax' => 'nullable|boolean',
            'payment_method' => 'required|in:cash,card,mobile_banking',
            'notes' => 'nullable|string',
            'action' => 'required|in:complete,hold',
        ]);

        $sale = DB::transaction(function () use ($validated, $request) {
            $totalAmount = 0;
            $itemDiscountTotal = 0;

            // Calculate subtotals with item-wise discounts
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $itemDiscount = $item['item_discount'] ?? 0;

                if (isset($item['item_discount_type']) && $item['item_discount_type'] === 'percent') {
                    $itemDiscount = ($subtotal * $itemDiscount) / 100;
                }

                $itemDiscountTotal += $itemDiscount;
                $totalAmount += $subtotal;
            }

            $totalAfterItemDiscounts = $totalAmount - $itemDiscountTotal;
            $discount = $validated['discount'] ?? 0;
            $discountType = $validated['discount_type'] ?? 'fixed';

            // Apply total discount
            if ($discountType === 'percent') {
                $discount = ($totalAfterItemDiscounts * $discount) / 100;
            }

            $subtotalAfterDiscount = $totalAfterItemDiscounts - $discount;

            // Calculate tax/VAT
            $taxRate = $validated['tax_rate'] ?? 15;
            $applyTax = $validated['apply_tax'] ?? false;
            $tax = $applyTax ? ($subtotalAfterDiscount * $taxRate) / 100 : 0;
            $finalAmount = $subtotalAfterDiscount + $tax;

            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'item_discount_total' => $itemDiscountTotal,
                'discount' => $discount,
                'tax' => $tax,
                'tax_rate' => $taxRate,
                'final_amount' => $finalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['action'] === 'hold' ? 'on_hold' : 'completed',
                'notes' => $validated['notes'] ?? null,
                'held_at' => $validated['action'] === 'hold' ? now() : null,
            ]);

            // Create sale items with item-wise discounts
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $itemDiscount = $item['item_discount'] ?? 0;
                $itemDiscountType = $item['item_discount_type'] ?? 'fixed';

                if ($itemDiscountType === 'percent') {
                    $itemDiscount = ($subtotal * $itemDiscount) / 100;
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                    'item_discount' => $itemDiscount,
                    'item_discount_type' => $itemDiscountType,
                ]);

                // Only update inventory if sale is completed, not held
                if ($validated['action'] !== 'hold') {
                    $product->decrement('quantity', $item['quantity']);

                    // Update customer total purchases and loyalty points
                    if ($sale->customer_id) {
                        $customer = $sale->customer;
                        $itemAmount = $subtotal - $itemDiscount;
                        $customer->increment('total_purchases', $itemAmount);

                        // Calculate loyalty points (1 punto = 1 peso by default, can be customized)
                        $loyaltyPoints = (int)$itemAmount;
                        $customer->increment('loyalty_points', $loyaltyPoints);

                        // Create loyalty transaction
                        CustomerTransaction::create([
                            'customer_id' => $customer->id,
                            'sale_id' => $sale->id,
                            'type' => 'loyalty_earned',
                            'loyalty_points' => $loyaltyPoints,
                            'amount' => 0,
                            'description' => 'Loyalty points earned from sale',
                        ]);

                        // Create purchase transaction
                        CustomerTransaction::create([
                            'customer_id' => $customer->id,
                            'sale_id' => $sale->id,
                            'type' => 'purchase',
                            'amount' => $itemAmount,
                            'description' => 'Purchase transaction',
                        ]);

                        // Update customer tier based on total purchases
                        $newTier = Customer::calculateTier($customer->total_purchases);
                        if ($customer->tier !== $newTier) {
                            $customer->update(['tier' => $newTier]);
                        }
                    }
                }
            }

            return $sale;
        });

        if ($validated['action'] === 'hold') {
            return redirect()->route('sales.index')
                ->with('success', 'Sale held successfully! You can resume it later.');
        }

        return redirect()->route('sales.receipt', $sale)
            ->with('success', 'Sale completed successfully!');
    }

    public function resume($saleId)
    {
        $sale = Sale::findOrFail($saleId);

        if ($sale->status !== 'on_hold') {
            return redirect()->route('sales.index')
                ->with('error', 'Only held sales can be resumed.');
        }

        // Load items into session for editing
        $sale->load('items.product');
        return redirect()->route('sales.edit', $sale);
    }

    public function edit(Sale $sale)
    {
        if ($sale->status !== 'on_hold' && $sale->status !== 'draft') {
            return redirect()->route('sales.index')
                ->with('error', 'Only draft or held sales can be edited.');
        }

        $sale->load('items.product');
        $products = Product::active()->get();
        $customers = Customer::all();
        $taxRate = $sale->tax_rate ?? 15;

        return view('sales.edit', compact('sale', 'products', 'customers', 'taxRate'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->status !== 'on_hold' && $sale->status !== 'draft') {
            return redirect()->route('sales.index')
                ->with('error', 'Only draft or held sales can be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_discount' => 'nullable|numeric|min:0',
            'items.*.item_discount_type' => 'nullable|in:fixed,percent',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percent',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'apply_tax' => 'nullable|boolean',
            'payment_method' => 'required|in:cash,card,mobile_banking',
            'notes' => 'nullable|string',
            'action' => 'required|in:complete,hold',
        ]);

        DB::transaction(function () use ($validated, $sale) {
            // Restore inventory if previously held
            if ($sale->status === 'on_hold') {
                foreach ($sale->items as $item) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }

            $totalAmount = 0;
            $itemDiscountTotal = 0;

            // Recalculate with new items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $itemDiscount = $item['item_discount'] ?? 0;

                if (isset($item['item_discount_type']) && $item['item_discount_type'] === 'percent') {
                    $itemDiscount = ($subtotal * $itemDiscount) / 100;
                }

                $itemDiscountTotal += $itemDiscount;
                $totalAmount += $subtotal;
            }

            $totalAfterItemDiscounts = $totalAmount - $itemDiscountTotal;
            $discount = $validated['discount'] ?? 0;
            $discountType = $validated['discount_type'] ?? 'fixed';

            if ($discountType === 'percent') {
                $discount = ($totalAfterItemDiscounts * $discount) / 100;
            }

            $subtotalAfterDiscount = $totalAfterItemDiscounts - $discount;

            $taxRate = $validated['tax_rate'] ?? 15;
            $applyTax = $validated['apply_tax'] ?? false;
            $tax = $applyTax ? ($subtotalAfterDiscount * $taxRate) / 100 : 0;
            $finalAmount = $subtotalAfterDiscount + $tax;

            $sale->update([
                'customer_id' => $validated['customer_id'],
                'total_amount' => $totalAmount,
                'item_discount_total' => $itemDiscountTotal,
                'discount' => $discount,
                'tax' => $tax,
                'tax_rate' => $taxRate,
                'final_amount' => $finalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['action'] === 'hold' ? 'on_hold' : 'completed',
                'notes' => $validated['notes'] ?? null,
                'held_at' => $validated['action'] === 'hold' ? now() : null,
            ]);

            // Delete old items
            $sale->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->quantity < $item['quantity'] && $validated['action'] !== 'hold') {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $itemDiscount = $item['item_discount'] ?? 0;
                $itemDiscountType = $item['item_discount_type'] ?? 'fixed';

                if ($itemDiscountType === 'percent') {
                    $itemDiscount = ($subtotal * $itemDiscount) / 100;
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                    'item_discount' => $itemDiscount,
                    'item_discount_type' => $itemDiscountType,
                ]);

                // Only update inventory if completing
                if ($validated['action'] !== 'hold') {
                    $product->decrement('quantity', $item['quantity']);

                    if ($sale->customer_id) {
                        $sale->customer->increment('total_purchases', $subtotal - $itemDiscount);
                    }
                }
            }
        });

        if ($validated['action'] === 'hold') {
            return redirect()->route('sales.index')
                ->with('success', 'Sale updated and held successfully!');
        }

        return redirect()->route('sales.receipt', $sale)
            ->with('success', 'Sale completed successfully!');
    }

    public function searchProduct(Request $request)
    {
        $query = Product::active();
        $search = trim($request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->limit(10)->get(['id', 'name', 'sku', 'price', 'quantity', 'barcode']);
        return response()->json($products);
    }

    public function receipt(Sale $sale)
    {
        $sale->load('customer', 'items.product', 'user');
        return view('sales.receipt', compact('sale'));
    }

    public function show(Sale $sale)
    {
        $sale->load('customer', 'items.product', 'user');
        return view('sales.show', compact('sale'));
    }
}
