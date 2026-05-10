<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'cash_register_session_id',
        'description',
        'amount',
        'category',
        'payment_method',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegisterSession(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('expense_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
