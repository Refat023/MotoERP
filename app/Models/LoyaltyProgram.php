<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyProgram extends Model
{
    protected $fillable = [
        'name',
        'tier',
        'min_purchase',
        'max_purchase',
        'points_multiplier',
        'discount_percentage',
        'benefits',
    ];

    protected $casts = [
        'min_purchase' => 'decimal:2',
        'max_purchase' => 'decimal:2',
        'points_multiplier' => 'decimal:2',
    ];
}
