<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteTemplate extends Model
{
    protected $fillable = [
        'name', 'key', 'icon', 'subtitle',
        'hardware_items', 'software_features', 'additional_benefits',
        'terms_conditions', 'sort_order',
    ];

    protected $casts = [
        'hardware_items'      => 'array',
        'software_features'   => 'array',
        'additional_benefits' => 'array',
    ];
}
