<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exchange extends Model
{
    protected $fillable = [
        'sales_return_id',
        'returned_product_id',
        'returned_quantity',
        'new_product_id',
        'new_quantity',
        'price_difference',
        'price_difference_method',
        'notes',
    ];

    protected $casts = [
        'price_difference' => 'decimal:2',
    ];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function returnedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'returned_product_id');
    }

    public function newProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'new_product_id');
    }

    public function calculatePriceDifference(): decimal
    {
        $returnedAmount = $this->returned_quantity * $this->returnedProduct->getSalePrice();
        $newAmount = $this->new_quantity * $this->newProduct->getSalePrice();

        return $newAmount - $returnedAmount;
    }
}
