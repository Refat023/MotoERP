<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesReturn extends Model
{
    protected $table = 'sales_returns';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'user_id',
        'return_type',
        'status',
        'reason',
        'total_returned_amount',
        'refund_amount',
        'refund_method',
        'approved_by',
        'approved_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'total_returned_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    public function canReturn(): bool
    {
        // Check if sale is within return window (e.g., 30 days)
        $returnWindow = 30; // days
        $daysSinceSale = now()->diffInDays($this->sale->created_at);
        
        return $daysSinceSale <= $returnWindow && $this->sale->status === 'completed';
    }

    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        // Restock products
        foreach ($this->items as $item) {
            $item->product->increment('quantity', $item->quantity_returned);
        }
    }

    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $reason,
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update sale return status
        $this->updateSaleReturnStatus();
    }

    private function updateSaleReturnStatus(): void
    {
        $totalReturned = $this->total_returned_amount;
        $saleTotal = $this->sale->final_amount;

        if ($totalReturned >= $saleTotal) {
            $this->sale->update([
                'return_status' => 'full',
                'returned_amount' => $totalReturned,
            ]);
        } else {
            $this->sale->update([
                'return_status' => 'partial',
                'returned_amount' => $totalReturned,
            ]);
        }
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
