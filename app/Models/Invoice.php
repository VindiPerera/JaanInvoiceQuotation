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

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->invoice_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function recalculatePaid(): void
    {
        $paid = $this->payments()->sum('amount');
        $this->paid_amount = $paid;
        $this->balance = $this->total_amount - $paid;
        $this->payment_status = match (true) {
            $paid <= 0 => 'pending',
            $paid >= $this->total_amount => 'paid',
            default => 'partial',
        };
        $this->save();
    }
}
