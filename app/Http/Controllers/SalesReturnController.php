<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\ReturnItem;
use App\Models\Exchange;
use App\Models\Refund;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of sales returns.
     */
    public function index(Request $request)
    {
        $query = SalesReturn::with(['sale', 'customer', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by return type
        if ($request->filled('return_type')) {
            $query->where('return_type', $request->return_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by sale number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sale', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            })->orWhereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $returns = $query->latest()->paginate(15);

        return view('returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new sales return.
     */
    public function create()
    {
        $sales = Sale::where('status', 'completed')->latest()->get();
        return view('returns.create', compact('sales'));
    }

    /**
     * Store a newly created sales return.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_type' => 'required|in:return,exchange,refund',
            'reason' => 'required|string',
            'items' => 'required|array',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.condition' => 'required|in:new,used,damaged',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::find($validated['sale_id']);
            
            // Calculate total returned amount
            $totalReturned = 0;
            foreach ($validated['items'] as $item) {
                $saleItem = $sale->items()->find($item['sale_item_id']);
                if ($saleItem->quantity < $item['quantity_returned']) {
                    throw new \Exception("Invalid quantity for {$saleItem->product->name}");
                }
                $totalReturned += $saleItem->price * $item['quantity_returned'];
            }

            // Create sales return record
            $salesReturn = SalesReturn::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'user_id' => Auth::id(),
                'return_type' => $validated['return_type'],
                'status' => 'pending',
                'reason' => $validated['reason'],
                'total_returned_amount' => $totalReturned,
                'refund_amount' => $validated['return_type'] !== 'exchange' ? $totalReturned : 0,
            ]);

            // Create return items
            foreach ($validated['items'] as $item) {
                $saleItem = $sale->items()->find($item['sale_item_id']);
                
                ReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'quantity_returned' => $item['quantity_returned'],
                    'unit_price' => $saleItem->price,
                    'return_amount' => $saleItem->price * $item['quantity_returned'],
                    'condition' => $item['condition'],
                    'item_notes' => $item['item_notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('sales-returns.show', $salesReturn->id)
                ->with('success', 'Sales return created successfully. Awaiting approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating return: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified sales return.
     */
    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['sale', 'customer', 'user', 'items', 'exchanges', 'refund']);
        return view('returns.show', compact('salesReturn'));
    }

    /**
     * Show the form for editing the specified sales return.
     */
    public function edit(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Can only edit pending returns.');
        }

        $salesReturn->load(['sale', 'items']);
        return view('returns.edit', compact('salesReturn'));
    }

    /**
     * Update the specified sales return.
     */
    public function update(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Can only edit pending returns.');
        }

        $validated = $request->validate([
            'reason' => 'required|string',
            'items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $totalReturned = 0;
            foreach ($validated['items'] as $itemId => $itemData) {
                $returnItem = ReturnItem::find($itemId);
                if (!$returnItem) continue;

                $totalReturned += $returnItem->quantity_returned * $returnItem->unit_price;
                $returnItem->update([
                    'condition' => $itemData['condition'] ?? $returnItem->condition,
                    'item_notes' => $itemData['item_notes'] ?? $returnItem->item_notes,
                ]);
            }

            $salesReturn->update([
                'reason' => $validated['reason'],
                'total_returned_amount' => $totalReturned,
            ]);

            DB::commit();

            return redirect()
                ->route('sales-returns.show', $salesReturn->id)
                ->with('success', 'Sales return updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating return: ' . $e->getMessage());
        }
    }

    /**
     * Approve a sales return.
     */
    public function approve(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        try {
            DB::beginTransaction();

            $salesReturn->approve(Auth::id());

            DB::commit();

            return redirect()
                ->route('sales-returns.show', $salesReturn->id)
                ->with('success', 'Sales return approved successfully. Items restocked.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving return: ' . $e->getMessage());
        }
    }

    /**
     * Reject a sales return.
     */
    public function reject(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be rejected.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $salesReturn->reject($validated['reason']);

        return redirect()
            ->route('sales-returns.show', $salesReturn->id)
            ->with('success', 'Sales return rejected.');
    }

    /**
     * Complete a sales return (mark as completed).
     */
    public function complete(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'approved') {
            return back()->with('error', 'Only approved returns can be completed.');
        }

        try {
            DB::beginTransaction();

            $salesReturn->complete();

            // If refund type, create refund record
            if ($salesReturn->return_type === 'refund' || $salesReturn->refund_amount > 0) {
                $refund = Refund::create([
                    'sales_return_id' => $salesReturn->id,
                    'refund_amount' => $salesReturn->refund_amount,
                    'method' => $salesReturn->refund_method ?? 'cash',
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('sales-returns.show', $salesReturn->id)
                ->with('success', 'Sales return completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error completing return: ' . $e->getMessage());
        }
    }

    /**
     * Delete a sales return (only if pending).
     */
    public function destroy(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Can only delete pending returns.');
        }

        $salesReturn->delete();

        return redirect()
            ->route('sales-returns.index')
            ->with('success', 'Sales return deleted successfully.');
    }

    /**
     * Get details for a specific sale.
     */
    public function getSaleDetails($saleId)
    {
        $sale = Sale::with('items.product', 'customer')->find($saleId);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        return response()->json($sale);
    }
}
