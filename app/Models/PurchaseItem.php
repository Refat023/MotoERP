<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'batch_number',
        'expiry_date',
        'cost_price',
        'quantity',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'cost_price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
