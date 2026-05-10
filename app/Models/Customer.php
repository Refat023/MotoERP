<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_id',
        'registration_date',
        'total_purchases',
        'loyalty_points',
        'points_redeemed',
        'credit_limit',
        'credit_balance',
        'tier',
    ];

    protected $casts = [
        'total_purchases' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'credit_balance' => 'decimal:2',
        'registration_date' => 'datetime',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    // Calculate loyalty tier based on total purchases
    public static function calculateTier($totalPurchases)
    {
        if ($totalPurchases >= 50000) {
            return 'platinum';
        } elseif ($totalPurchases >= 20000) {
            return 'gold';
        } elseif ($totalPurchases >= 5000) {
            return 'silver';
        }
        return 'bronze';
    }

    // Get available loyalty points (earned - redeemed)
    public function getAvailablePointsAttribute()
    {
        return $this->loyalty_points - $this->points_redeemed;
    }

    // Get tier benefits
    public function getTierBenefitsAttribute()
    {
        return match($this->tier) {
            'platinum' => 'Platinum: 15% discount on all purchases',
            'gold' => 'Gold: 10% discount on all purchases',
            'silver' => 'Silver: 5% discount on all purchases',
            'bronze' => 'Bronze: Regular pricing',
            default => 'No tier benefits'
        };
    }

    // Get discount percentage for tier
    public function getTierDiscountAttribute()
    {
        return match($this->tier) {
            'platinum' => 15,
            'gold' => 10,
            'silver' => 5,
            'bronze' => 0,
            default => 0
        };
    }

    // Calculate due amount (credit balance)
    public function getDueAmountAttribute()
    {
        return max(0, $this->credit_balance);
    }

    // Calculate available credit (limit - balance)
    public function getAvailableCreditAttribute()
    {
        return max(0, $this->credit_limit - $this->credit_balance);
    }
}
