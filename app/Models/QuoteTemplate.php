<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteTemplate extends Model
{
    protected $fillable = [
        'name', 'key', 'subtitle', 'project_overview', 'template_type',
        'hardware_items', 'software_features', 'additional_benefits',
        'terms_conditions',
    ];

    protected $casts = [
        'hardware_items'      => 'array',
        'software_features'   => 'array',
        'additional_benefits' => 'array',
    ];
}
