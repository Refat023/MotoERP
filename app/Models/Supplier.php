<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'opening_balance',
        'due_balance',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'due_balance' => 'decimal:2',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
