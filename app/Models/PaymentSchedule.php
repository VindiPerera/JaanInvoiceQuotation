<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'invoice_id', 'step_number', 'due_date', 'amount', 'status', 'payment_date', 'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid' => '<span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">✓ Paid</span>',
            'pending' => '<span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">⏳ Pending</span>',
            'overdue' => '<span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">⚠ Overdue</span>',
            'cancelled' => '<span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">✕ Cancelled</span>',
            default => '',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date < now()->toDateString();
    }
}
