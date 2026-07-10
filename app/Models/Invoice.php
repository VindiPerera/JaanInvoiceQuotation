<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number', 'invoice_date', 'customer_id', 'quotation_id',
        'customer_name', 'customer_address', 'customer_contact',
        'subtotal', 'tax_amount', 'total_amount', 'paid_amount', 'balance',
        'payment_status', 'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('item_number');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'desc');
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class)->orderBy('step_number');
    }

    public static function generateNumber(): string
    {
        // Get the highest numeric part from existing invoice numbers
        $last = static::withTrashed()->where('invoice_number', 'like', 'INV-%')->orderByRaw('CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC')->first();
        $next = $last ? ((int) substr($last->invoice_number, 4)) + 1 : 1;

        // Ensure uniqueness by incrementing if number already exists
        while (static::where('invoice_number', 'INV-' . str_pad($next, 4, '0', STR_PAD_LEFT))->exists()) {
            $next++;
        }

        return 'INV-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function recalculatePaid(): void
    {
        $paid = $this->payments()->sum('amount');
        $this->paid_amount = $paid;
        $this->balance = $this->total_amount - $paid;
        $this->payment_status = $this->calculatePaymentStatus();
        $this->save();
    }

    public function calculatePaymentStatus(): string
    {
        $paid = $this->payments()->sum('amount');
        return match (true) {
            $paid <= 0 => 'pending',
            $paid >= $this->total_amount => 'paid',
            default => 'partial',
        };
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float)$this->total_amount - (float)$this->paid_amount);
    }

    public function canAcceptPayment(float $amount): array
    {
        $remaining = $this->getRemainingBalanceAttribute();

        if ($amount <= 0) {
            return ['valid' => false, 'message' => 'Payment amount must be greater than zero.'];
        }

        if ($amount > $remaining) {
            return [
                'valid' => false,
                'message' => "Payment exceeds remaining balance of LKR " . number_format($remaining, 2),
                'remaining' => $remaining,
            ];
        }

        return ['valid' => true, 'remaining' => $remaining - $amount];
    }

    public function isFullyPaid(): bool
    {
        return (float)$this->paid_amount >= (float)$this->total_amount;
    }

    public function isPartiallPaid(): bool
    {
        return (float)$this->paid_amount > 0 && (float)$this->paid_amount < (float)$this->total_amount;
    }

    public function isPending(): bool
    {
        return (float)$this->paid_amount <= 0;
    }
}
