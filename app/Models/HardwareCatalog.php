<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HardwareCatalog extends Model
{
    protected $table = 'hardware_catalog';

    protected $fillable = [
        'name', 'description', 'category', 'unit_price', 'warranty', 'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
