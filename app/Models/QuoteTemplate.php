<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteTemplate extends Model
{
    protected $fillable = [
        'name', 'key', 'subtitle', 'project_overview', 'template_type',
        'hardware_items', 'software_features', 'additional_benefits',
    ];

    protected $casts = [
        'hardware_items'      => 'array',
        'software_features'   => 'array',
        'additional_benefits' => 'array',
    ];

    private function decodeJsonAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            // Handle double-encoded JSON
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return is_array($decoded) ? $decoded : [];
        }
        return $value ?? [];
    }

    protected function getHardwareItemsAttribute($value)
    {
        return $this->decodeJsonAttribute($value);
    }

    protected function getSoftwareFeaturesAttribute($value)
    {
        return $this->decodeJsonAttribute($value);
    }

    protected function getAdditionalBenefitsAttribute($value)
    {
        return $this->decodeJsonAttribute($value);
    }
}
