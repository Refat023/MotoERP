<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\ReturnItem;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;

class RefundService
{
    /**
     * Calculate refund amount based on return items
     */
    public function calculateRefundAmount(SalesReturn $salesReturn): float
    {
        return $salesReturn->items->sum(function ($item) {
            return $item->quantity_returned * $item->unit_price;
        });
    }

    /**
     * Process a refund
     */
    public function processRefund(Refund $refund, ?string $transactionId = null): bool
    {
        try {
            DB::beginTransaction();

            // Update refund status
            $refund->process(auth()->id(), $transactionId);

            // Update sales return refund status
            $salesReturn = $refund->salesReturn;
            $salesReturn->update([
                'refund_method' => $refund->method,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if a sale can be returned
     */
    public function canReturnSale(Sale $sale, int $returnWindowDays = 30): bool
    {
        if ($sale->status !== 'completed') {
            return false;
        }

        $daysSinceSale = now()->diffInDays($sale->created_at);
        return $daysSinceSale <= $returnWindowDays;
    }

    /**
     * Get refund eligibility message
     */
    public function getRefundEligibilityMessage(Sale $sale, int $returnWindowDays = 30): string
    {
        if ($sale->status !== 'completed') {
            return 'Sale must be completed to be returned.';
        }

        $daysSinceSale = now()->diffInDays($sale->created_at);
        if ($daysSinceSale > $returnWindowDays) {
            return "Return window expired. Sales can be returned within {$returnWindowDays} days.";
        }

        return 'Eligible for return.';
    }

    /**
     * Calculate inventory impact
     */
    public function getInventoryImpact(SalesReturn $salesReturn): array
    {
        $impact = [];
        foreach ($salesReturn->items as $item) {
            $impact[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity_restored' => $item->quantity_returned,
            ];
        }
        return $impact;
    }

    /**
     * Get refund status summary
     */
    public function getRefundStatusSummary(?\DateTime $fromDate = null, ?\DateTime $toDate = null): array
    {
        $query = Refund::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        return [
            'total_count' => $query->count(),
            'total_amount' => $query->sum('refund_amount'),
            'processed' => $query->where('status', 'processed')->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'failed' => $query->where('status', 'failed')->count(),
            'by_method' => $query->groupBy('method')
                ->selectRaw('method, COUNT(*) as count, SUM(refund_amount) as total')
                ->get()
                ->keyBy('method')
                ->map(fn($row) => [
                    'count' => $row->count,
                    'total' => $row->total,
                ]),
        ];
    }

    /**
     * Validate return request
     */
    public function validateReturnRequest(Sale $sale, array $items): array
    {
        $errors = [];

        // Check sale exists and is completed
        if (!$sale || $sale->status !== 'completed') {
            $errors[] = 'Invalid or uncompleted sale.';
        }

        // Check return window
        $daysSinceSale = now()->diffInDays($sale->created_at);
        if ($daysSinceSale > 30) {
            $errors[] = 'Return window expired (30 days).';
        }

        // Validate items
        foreach ($items as $itemData) {
            $saleItem = $sale->items()->find($itemData['sale_item_id']);
            if (!$saleItem) {
                $errors[] = 'Invalid sale item.';
                continue;
            }

            if ($itemData['quantity_returned'] > $saleItem->quantity) {
                $errors[] = "Cannot return more than {$saleItem->quantity} of {$saleItem->product->name}.";
            }
        }

        return $errors;
    }
}
