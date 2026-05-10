<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'total_amount',
        'discount',
        'item_discount_total',
        'tax',
        'tax_rate',
        'final_amount',
        'payment_method',
        'status',
        'return_status',
        'returned_amount',
        'notes',
        'held_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'item_discount_total' => 'decimal:2',
        'tax' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'returned_amount' => 'decimal:2',
        'held_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }
}
