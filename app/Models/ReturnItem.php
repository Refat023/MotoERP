<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $fillable = [
        'sales_return_id',
        'sale_item_id',
        'product_id',
        'quantity_returned',
        'unit_price',
        'return_amount',
        'condition',
        'item_notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'return_amount' => 'decimal:2',
    ];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Calculate return amount if not already set
            if (!$model->return_amount) {
                $model->return_amount = $model->quantity_returned * $model->unit_price;
            }
        });
    }
}
