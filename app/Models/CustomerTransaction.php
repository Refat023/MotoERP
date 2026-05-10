<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'sale_id',
        'type',
        'amount',
        'loyalty_points',
        'description',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
