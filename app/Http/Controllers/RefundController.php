<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Display a listing of refunds.
     */
    public function index(Request $request)
    {
        $query = Refund::with(['salesReturn', 'processedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('salesReturn', function ($q) use ($search) {
                      $q->where('id', 'like', "%{$search}%");
                  });
        }

        $refunds = $query->latest()->paginate(15);

        return view('refunds.index', compact('refunds'));
    }

    /**
     * Show the form for processing a refund.
     */
    public function create(SalesReturn $salesReturn)
    {
        if (!$salesReturn->refund_amount || $salesReturn->refund_amount <= 0) {
            return back()->with('error', 'This return has no refund amount to process.');
        }

        // Check if refund already exists
        if ($salesReturn->refund) {
            return redirect()->route('refunds.show', $salesReturn->refund->id);
        }

        return view('refunds.create', compact('salesReturn'));
    }

    /**
     * Store and process a refund.
     */
    public function store(Request $request, SalesReturn $salesReturn)
    {
        $validated = $request->validate([
            'method' => 'required|in:cash,card,credit_note,wallet',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $refund = Refund::create([
                'sales_return_id' => $salesReturn->id,
                'refund_amount' => $salesReturn->refund_amount,
                'method' => $validated['method'],
                'status' => 'pending',
            ]);

            // Auto-process cash refunds
            if ($validated['method'] === 'cash') {
                $refund->process(Auth::id());
            }

            DB::commit();

            return redirect()
                ->route('refunds.show', $refund->id)
                ->with('success', 'Refund created. ' . ($validated['method'] === 'cash' ? 'Already processed.' : 'Awaiting processing.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating refund: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified refund.
     */
    public function show(Refund $refund)
    {
        $refund->load(['salesReturn', 'processedBy']);
        return view('refunds.show', compact('refund'));
    }

    /**
     * Process a pending refund.
     */
    public function process(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Only pending refunds can be processed.');
        }

        $validated = $request->validate([
            'transaction_id' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $refund->process(Auth::id(), $validated['transaction_id']);

            DB::commit();

            return redirect()
                ->route('refunds.show', $refund->id)
                ->with('success', 'Refund processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    /**
     * Mark a refund as failed.
     */
    public function markFailed(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Only pending refunds can be marked as failed.');
        }

        $validated = $request->validate([
            'failure_reason' => 'required|string|max:500',
        ]);

        $refund->markFailed($validated['failure_reason']);

        return redirect()
            ->route('refunds.show', $refund->id)
            ->with('success', 'Refund marked as failed.');
    }

    /**
     * Generate refund report.
     */
    public function report(Request $request)
    {
        $query = Refund::with(['salesReturn', 'processedBy']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $refunds = $query->latest()->get();

        $totals = [
            'total_refunds' => $refunds->count(),
            'total_amount' => $refunds->sum('refund_amount'),
            'processed' => $refunds->where('status', 'processed')->count(),
            'pending' => $refunds->where('status', 'pending')->count(),
            'failed' => $refunds->where('status', 'failed')->count(),
            'by_method' => $refunds->groupBy('method')->map->sum('refund_amount'),
        ];

        return view('refunds.report', compact('refunds', 'totals'));
    }

    /**
     * Delete a pending refund.
     */
    public function destroy(Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Can only delete pending refunds.');
        }

        $refund->delete();

        return back()->with('success', 'Refund deleted successfully.');
    }
}
