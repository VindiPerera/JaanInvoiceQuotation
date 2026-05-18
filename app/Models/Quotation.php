<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_number', 'quotation_date', 'customer_id', 'customer_name',
        'customer_address', 'customer_contact', 'subject', 'software_features',
        'additional_benefits', 'subtotal', 'tax_amount', 'total_amount',
        'terms_conditions', 'status',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'software_features' => 'array',
        'additional_benefits' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('item_number');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->quotation_number, 3)) + 1 : 1;
        return 'QT-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
