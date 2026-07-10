<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        // Validate payment amount
        $validation = $invoice->canAcceptPayment((float)$data['amount']);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException($validation['message']);
        }

        return DB::transaction(function () use ($invoice, $data) {
            $payment = Payment::create([
                'invoice_id'       => $invoice->id,
                'payment_date'     => $data['payment_date'],
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'created_by'       => Auth::id(),
            ]);

            // Recalculate invoice payment status
            $invoice->recalculatePaid();

            return $payment;
        });
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        $invoice = $payment->invoice;
        $oldAmount = (float)$payment->amount;
        $newAmount = (float)$data['amount'];

        // If amount changed, validate the new amount
        if ($newAmount !== $oldAmount) {
            // Temporarily remove old payment to check if new amount fits
            $tempBalance = (float)$invoice->total_amount - ((float)$invoice->paid_amount - $oldAmount);
            if ($newAmount > $tempBalance) {
                throw new \InvalidArgumentException(
                    "New payment amount exceeds remaining balance of LKR " . number_format($tempBalance, 2)
                );
            }
        }

        return DB::transaction(function () use ($payment, $data, $invoice) {
            $payment->update([
                'payment_date'     => $data['payment_date'],
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'updated_by'       => Auth::id(),
            ]);

            // Recalculate invoice payment status
            $invoice->recalculatePaid();

            return $payment;
        });
    }

    public function deletePayment(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $payment->delete();
            $invoice->recalculatePaid();
            return true;
        });
    }

    public function getPaymentHistory(Invoice $invoice)
    {
        return $invoice->payments()
            ->with('createdBy', 'updatedBy')
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function generatePaymentSummary(Invoice $invoice): array
    {
        return [
            'invoice_total'    => (float)$invoice->total_amount,
            'total_paid'       => (float)$invoice->paid_amount,
            'remaining_balance' => (float)$invoice->getRemainingBalanceAttribute(),
            'payment_status'   => $invoice->payment_status,
            'payment_count'    => $invoice->payments()->count(),
            'last_payment_date' => $invoice->payments()->max('payment_date'),
        ];
    }

    public function getPaymentStatistics(Invoice $invoice): array
    {
        $payments = $invoice->payments()->get();

        return [
            'total_transactions' => $payments->count(),
            'total_amount'       => $payments->sum('amount'),
            'average_payment'    => $payments->count() > 0 ? $payments->sum('amount') / $payments->count() : 0,
            'payment_methods'    => $payments->groupBy('payment_method')->map(fn($group) => [
                'method'       => $group->first()->formatted_method,
                'count'        => $group->count(),
                'total_amount' => $group->sum('amount'),
            ])->values(),
        ];
    }
}
