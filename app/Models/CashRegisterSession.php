<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegisterSession extends Model
{
    protected $fillable = [
        'user_id',
        'opening_balance',
        'closing_balance',
        'expected_balance',
        'cash_sales',
        'card_sales',
        'other_sales',
        'refunds',
        'returns',
        'expenses',
        'status',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'card_sales' => 'decimal:2',
        'other_sales' => 'decimal:2',
        'refunds' => 'decimal:2',
        'returns' => 'decimal:2',
        'expenses' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function calculateExpectedBalance(): float
    {
        return $this->opening_balance +
               $this->cash_sales +
               $this->card_sales +
               $this->other_sales -
               $this->refunds -
               $this->returns -
               $this->expenses;
    }

    public function getVariance(): float
    {
        if (!$this->closing_balance) {
            return 0;
        }
        return $this->closing_balance - $this->calculateExpectedBalance();
    }

    public function close(float $closingBalance, string $notes = null): void
    {
        $this->update([
            'closing_balance' => $closingBalance,
            'expected_balance' => $this->calculateExpectedBalance(),
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('opened_at', today());
    }
}
