<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id', 'payment_date', 'amount', 'payment_method', 'reference_number', 'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float)$this->amount, 2);
    }

    public function getFormattedMethodAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'card' => 'Card',
            'cheque' => 'Cheque',
            'online' => 'Online',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }
}
