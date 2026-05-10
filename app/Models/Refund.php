<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'sales_return_id',
        'refund_amount',
        'method',
        'status',
        'transaction_id',
        'processed_by',
        'processed_at',
        'failure_reason',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function process(int $processedBy, string $transactionId = null): void
    {
        $this->update([
            'status' => 'processed',
            'processed_by' => $processedBy,
            'processed_at' => now(),
            'transaction_id' => $transactionId,
        ]);
    }

    public function markFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
