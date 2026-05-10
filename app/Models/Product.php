<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'reorder_level',
        'sku',
        'barcode',
        'category_id',
        'sub_category_id',
        'brand_id',
        'unit_id',
        'image',
        'active',
        'mrp',
        'wholesale_price',
        'offer_price',
        'pricing_type',
        'weight',
        'specifications',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'active' => 'boolean',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Accessors & Methods
    public function getSalePrice()
    {
        // Return offer price if available, otherwise use MRP, fallback to price
        return $this->offer_price ?? $this->mrp ?? $this->price;
    }

    public function getMarginAttribute()
    {
        if (!$this->wholesale_price) {
            return 0;
        }
        return round((($this->mrp - $this->wholesale_price) / $this->wholesale_price) * 100, 2);
    }

    public function getPriceDisplayAttribute()
    {
        if ($this->offer_price) {
            return '₱' . $this->offer_price . ' (Offer) - MRP: ₱' . $this->mrp;
        }

        $basePrice = $this->mrp !== null ? $this->mrp : $this->price;
        return '₱' . $basePrice;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeWithOffer($query)
    {
        return $query->whereNotNull('offer_price');
    }
}
