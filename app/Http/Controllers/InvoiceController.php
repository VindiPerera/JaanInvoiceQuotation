<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\HardwareCatalog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Setting;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhere('customer_name', 'like', "%$search%");
            });
        }
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->date_from) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $totalsQuery = Invoice::query();
        if ($request->date_from) { $totalsQuery->whereDate('invoice_date', '>=', $request->date_from); }
        if ($request->date_to)   { $totalsQuery->whereDate('invoice_date', '<=', $request->date_to); }

        $totals = [
            'count'         => (clone $totalsQuery)->count(),
            'total_amount'  => (clone $totalsQuery)->sum('total_amount'),
            'total_paid'    => (clone $totalsQuery)->sum('paid_amount'),
            'total_balance' => (clone $totalsQuery)->sum('balance'),
        ];

        return view('invoices.index', compact('invoices', 'totals'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $nextNumber = Invoice::generateNumber();
        $settings = Setting::pluck('value', 'key');
        $quotation = null;

        if ($request->quotation_id) {
            $quotation = Quotation::with('items')->find($request->quotation_id);
        }

        $hardware = HardwareCatalog::active()->orderBy('name')->get(['id', 'name', 'description', 'unit_price', 'warranty']);

        return view('invoices.create', compact('customers', 'nextNumber', 'settings', 'quotation', 'hardware'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date'           => 'required|date',
            'customer_name'          => 'required|string|max:255',
            'advance_amount'         => 'nullable|numeric|min:0',
            'advance_payment_method' => 'nullable|in:cash,bank_transfer,card,cheque,online',
            'advance_reference'      => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $status   = in_array($request->payment_status, ['pending', 'paid']) ? $request->payment_status : 'pending';
            $subtotal = 0;

            // Generate unique invoice number (regenerate if duplicate exists)
            $invoiceNumber = $request->invoice_number;
            if (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                $invoiceNumber = Invoice::generateNumber();
            }

            $invoice = Invoice::create([
                'invoice_number'   => $invoiceNumber,
                'invoice_date'     => $request->invoice_date,
                'customer_id'      => $request->customer_id ?: null,
                'quotation_id'     => $request->quotation_id ?: null,
                'customer_name'    => $request->customer_name,
                'customer_address' => $request->customer_address,
                'customer_contact' => $request->customer_contact,
                'tax_amount'       => $request->tax_amount ?? 0,
                'notes'            => $request->notes,
                'subtotal'         => 0,
                'total_amount'     => 0,
                'paid_amount'      => 0,
                'balance'          => 0,
                'payment_status'   => $status,
            ]);

            if ($request->items) {
                foreach ($request->items as $i => $item) {
                    if (empty($item['item_name']) && empty($item['description'])) { continue; }
                    $total = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                    $isHidden = (bool) ($item['is_hidden'] ?? false);
                    InvoiceItem::create([
                        'invoice_id'  => $invoice->id,
                        'item_number' => $i + 1,
                        'item_name'   => $item['item_name'] ?? null,
                        'description' => $item['description'] ?? '',
                        'quantity'    => $item['quantity'] ?? 1,
                        'unit_price'  => $item['unit_price'] ?? 0,
                        'warranty'    => $item['warranty'] ?? null,
                        'total'       => $total,
                        'is_hidden'   => $isHidden,
                    ]);
                    $subtotal += $total;
                }
            }

            $calculatedTotal = $subtotal + ($request->tax_amount ?? 0);
            $finalTotal = !empty($request->manual_total) ? (float) $request->manual_total : $calculatedTotal;

            $invoice->update([
                'subtotal'       => $subtotal,
                'total_amount'   => $finalTotal,
                'paid_amount'    => $status === 'paid' ? $finalTotal : 0,
                'balance'        => $status === 'paid' ? 0 : $finalTotal,
                'payment_status' => $status,
            ]);

            // Record advance payment if provided
            if ($request->has_advance && $request->advance_amount > 0) {
                Payment::create([
                    'invoice_id'       => $invoice->id,
                    'payment_date'     => $request->invoice_date,
                    'amount'           => $request->advance_amount,
                    'payment_method'   => $request->advance_payment_method ?? 'cash',
                    'reference_number' => $request->advance_reference,
                    'notes'            => 'Advance/Initial payment at invoice creation',
                    'created_by'       => auth()->id(),
                ]);

                // Recalculate paid amount and status
                $invoice->recalculatePaid();
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'customer', 'payments');
        $settings = Setting::pluck('value', 'key');
        return view('invoices.show', compact('invoice', 'settings'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::orderBy('name')->get();
        $settings = Setting::pluck('value', 'key');
        $hardware = HardwareCatalog::active()->orderBy('name')->get(['id', 'name', 'description', 'unit_price', 'warranty']);
        return view('invoices.edit', compact('invoice', 'customers', 'settings', 'hardware'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_number'         => 'required|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date'           => 'required|date',
            'customer_name'          => 'required|string|max:255',
            'advance_amount'         => 'nullable|numeric|min:0',
            'advance_payment_method' => 'nullable|in:cash,bank_transfer,card,cheque,online',
            'advance_reference'      => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $status = in_array($request->payment_status, ['pending', 'paid']) ? $request->payment_status : 'pending';

            $invoice->update([
                'invoice_number'   => $request->invoice_number,
                'invoice_date'     => $request->invoice_date,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'customer_address' => $request->customer_address,
                'customer_contact' => $request->customer_contact,
                'tax_amount'       => $request->tax_amount ?? 0,
                'notes'            => $request->notes,
                'payment_status'   => $status,
            ]);

            $invoice->items()->delete();
            $subtotal = 0;

            if ($request->items) {
                foreach ($request->items as $i => $item) {
                    if (empty($item['item_name']) && empty($item['description'])) { continue; }
                    $total = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                    $isHidden = (bool) ($item['is_hidden'] ?? false);
                    InvoiceItem::create([
                        'invoice_id'  => $invoice->id,
                        'item_number' => $i + 1,
                        'item_name'   => $item['item_name'] ?? null,
                        'description' => $item['description'] ?? '',
                        'quantity'    => $item['quantity'] ?? 1,
                        'unit_price'  => $item['unit_price'] ?? 0,
                        'warranty'    => $item['warranty'] ?? null,
                        'total'       => $total,
                        'is_hidden'   => $isHidden,
                    ]);
                    $subtotal += $total;
                }
            }

            $calculatedTotal = $subtotal + ($request->tax_amount ?? 0);
            $finalTotal = !empty($request->manual_total) ? (float) $request->manual_total : $calculatedTotal;

            // Handle advance payment editing
            if ($request->has_advance && $request->advance_amount > 0) {
                $advancePayment = $invoice->payments()->first();

                if ($advancePayment) {
                    // Update existing advance payment
                    $advancePayment->update([
                        'amount'           => $request->advance_amount,
                        'payment_method'   => $request->advance_payment_method ?? 'cash',
                        'reference_number' => $request->advance_reference,
                        'updated_by'       => auth()->id(),
                    ]);
                } else {
                    // Create new advance payment if none exists
                    Payment::create([
                        'invoice_id'       => $invoice->id,
                        'payment_date'     => $request->invoice_date,
                        'amount'           => $request->advance_amount,
                        'payment_method'   => $request->advance_payment_method ?? 'cash',
                        'reference_number' => $request->advance_reference,
                        'notes'            => 'Advance/Initial payment (edited)',
                        'created_by'       => auth()->id(),
                    ]);
                }
            } elseif (!$request->has_advance && $invoice->payments()->count() > 0) {
                // Delete advance payment if unchecked
                $invoice->payments()->delete();
            }

            $invoice->update([
                'subtotal'       => $subtotal,
                'total_amount'   => $finalTotal,
                'paid_amount'    => $status === 'paid' ? $finalTotal : $invoice->paid_amount,
                'balance'        => $status === 'paid' ? 0 : $finalTotal - $invoice->paid_amount,
                'payment_status' => $status,
            ]);

            // Recalculate if advance payment was updated
            if ($request->has_advance && $request->advance_amount > 0) {
                $invoice->recalculatePaid();
            }
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('items', 'payments');
        $settings = Setting::pluck('value', 'key');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function duplicate(Invoice $invoice)
    {
        $invoice->load('items');
        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = Invoice::generateNumber();
        $newInvoice->invoice_date = now()->toDateString();
        $newInvoice->paid_amount = 0;
        $newInvoice->balance = $invoice->total_amount;
        $newInvoice->payment_status = 'pending';
        $newInvoice->save();

        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        return redirect()->route('invoices.edit', $newInvoice)->with('success', 'Invoice duplicated.');
    }

    public function recordPayment(Request $request, Invoice $invoice, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'payment_date'   => 'required|date|date_format:Y-m-d',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque,online',
            'reference_number' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        try {
            $paymentService->recordPayment($invoice, $validated);
            return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function editPayment(Invoice $invoice, Payment $payment)
    {
        return response()->json([
            'id' => $payment->id,
            'payment_date' => $payment->payment_date->format('Y-m-d'),
            'amount' => (float)$payment->amount,
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'notes' => $payment->notes,
        ]);
    }

    public function updatePayment(Request $request, Invoice $invoice, Payment $payment, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'payment_date'   => 'required|date|date_format:Y-m-d',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque,online',
            'reference_number' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        try {
            $paymentService->updatePayment($payment, $validated);
            return redirect()->route('invoices.show', $invoice)->with('success', 'Payment updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function deletePayment(Invoice $invoice, Payment $payment, PaymentService $paymentService)
    {
        $paymentService->deletePayment($payment);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment removed successfully.');
    }

    public function paymentReceipt(Invoice $invoice)
    {
        $invoice->load('payments.createdBy');
        $pdf = Pdf::loadView('invoices.payment-receipt', compact('invoice'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('Payment-Receipt-' . $invoice->invoice_number . '.pdf');
    }

    public function paymentHistory(Invoice $invoice)
    {
        $invoice->load('payments.createdBy', 'payments.updatedBy');
        return response()->json([
            'invoice_number' => $invoice->invoice_number,
            'total_amount' => (float)$invoice->total_amount,
            'paid_amount' => (float)$invoice->paid_amount,
            'balance' => (float)$invoice->balance,
            'payment_status' => $invoice->payment_status,
            'payments' => $invoice->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'date' => $payment->payment_date->format('Y-m-d'),
                    'method' => $payment->payment_method,
                    'reference' => $payment->reference_number,
                    'amount' => (float)$payment->amount,
                    'notes' => $payment->notes,
                    'created_by' => $payment->createdBy?->name,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }
}
