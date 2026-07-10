@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number)

@section('header-actions')
    <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition shadow-md hover:shadow-lg">
        <i class="fa-solid fa-file-pdf text-base"></i> Download PDF
    </a>
    <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition shadow-sm">
        <i class="fa-solid fa-pencil text-base"></i> Edit
    </a>
    @if($invoice->payment_status !== 'paid')
    <button onclick="document.getElementById('paymentModal').classList.remove('hidden')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
        <i class="fa-solid fa-money-bill text-base"></i> Record Payment
    </button>
    @endif
@endsection

@section('content')
<div style="max-width:860px;">

{{-- Paper preview --}}
<div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;
            box-shadow:0 2px 8px rgba(0,0,0,0.07);margin-bottom:20px;
            font-family:Arial,sans-serif;font-size:10pt;color:#1a1a1a;line-height:1.5;">
<style>
.ipv-tbl  { width:100%;border-collapse:collapse; }
.ipv-tbl thead tr { background:#b91c1c;color:#fff; }
.ipv-tbl th  { padding:8px;font-size:8pt;font-weight:bold;text-align:left;text-transform:uppercase;letter-spacing:0.5px;border:none; }
.ipv-tbl th.r { text-align:right; }
.ipv-tbl th.c { text-align:center; }
.ipv-tbl td  { padding:7px 8px;border-bottom:1px dashed #fecaca;font-size:9pt;vertical-align:top; }
.ipv-tbl td.r { text-align:right; }
.ipv-tbl td.c { text-align:center; }
.ipv-tbl tbody tr:nth-child(odd)  { background:#fff; }
.ipv-tbl tbody tr:nth-child(even) { background:#fef2f2; }
.ipv-tbl tbody tr:last-child td { border-bottom:1px solid #111; }
.ipv-th  { font-size:9pt;font-weight:bold;color:#1a1a1a;margin:7px 0 2px; }
.ipv-ts  { font-size:8.5pt;font-weight:bold;color:#333;margin:5px 0 2px; }
.ipv-tb  { font-size:8.5pt;color:#444;margin-bottom:3px; }
.ipv-tbu { font-size:8.5pt;color:#333;padding-left:10px;margin-bottom:3px; }
.ipv-doc-title { text-align:center;font-size:16pt;font-weight:bold;letter-spacing:5px;margin:8px 0 12px;padding:8px 0;border-top:2px solid #b91c1c;border-bottom:1px solid #b91c1c;border-left:1px solid #fecaca;border-right:1px solid #fecaca;background:#fef2f2;color:#b91c1c; }
.ipv-section { font-weight:bold;font-size:8.2pt;text-transform:uppercase;letter-spacing:1.6px;margin:16px 0 4px;color:#b91c1c;display:inline-block;padding:4px 10px 4px 8px;border-left:3px solid #b91c1c;background:#fef2f2;border-radius:2px; }
.ipv-rule { border:none;border-top:1px solid #b91c1c;margin:0 0 8px; }
</style>

@php $logoPath = !empty($settings['company_logo']) ? public_path($settings['company_logo']) : null; @endphp

{{-- LETTERHEAD --}}
<table width="100%" cellpadding="0" cellspacing="0" style="padding:14px 20px 10px;border-bottom:2px solid #b91c1c;table-layout:fixed;">
    <tr>
        <td width="60%" style="vertical-align:bottom;padding-right:10px;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" style="max-height:48px;max-width:140px;display:block;margin-bottom:6px;">
            @endif
            <div style="font-size:14pt;font-weight:bold;line-height:1.15;letter-spacing:0.3px;color:#111111;">
                {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}
            </div>
            <div style="font-size:8.5pt;color:#b91c1c;margin-top:2px;">
                Professional IT Solutions &amp; Services
            </div>
        </td>
        <td width="40%" style="vertical-align:bottom;text-align:right;font-size:8.5pt;line-height:1.85;color:#111111;">
            @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
            @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
            @if(!empty($settings['company_address'])){{ $settings['company_address'] }}@endif
        </td>
    </tr>
</table>

{{-- DOCUMENT TITLE --}}
<div class="ipv-doc-title">S A L E S &nbsp; I N V O I C E</div>

{{-- BILLING + DOC DETAILS --}}
<div style="padding:0 20px 20px;">
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td width="58%" style="vertical-align:top;padding:10px 14px 10px 12px;background:#fef2f2;border:1px solid #fecaca;border-top:2px solid #b91c1c;">
            <div style="font-size:7.5pt;text-transform:uppercase;letter-spacing:1.5px;color:#7f1d1d;margin-bottom:6px;">Bill To</div>
            <div style="font-weight:bold;font-size:11pt;line-height:1.3;margin-bottom:4px;">{{ $invoice->customer_name }}</div>
            @if($invoice->customer_address)
            <div style="font-size:9pt;color:#111111;margin-bottom:2px;">{{ $invoice->customer_address }}</div>
            @endif
            @if($invoice->customer_contact)
            <div style="font-size:9pt;color:#111111;">{{ $invoice->customer_contact }}</div>
            @endif
        </td>
        <td width="42%" style="vertical-align:top;padding:10px 12px;background:#fee2e2;border:1px solid #fecaca;border-top:2px solid #b91c1c;">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:9pt;line-height:1.8;color:#111111;">
                <tr>
                    <td style="font-weight:bold;width:48%;">Invoice No</td>
                    <td style="text-align:right;">:&nbsp;{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Date</td>
                    <td style="text-align:right;">:&nbsp;{{ $invoice->invoice_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Status</td>
                    <td style="text-align:right;font-weight:bold;">
                        :&nbsp;@if($invoice->payment_status === 'paid') PAID
                        @elseif($invoice->payment_status === 'partial') PARTIAL
                        @else PENDING @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ITEMS --}}
@php $visibleItems = $invoice->items->where('is_hidden', false); @endphp
@if($visibleItems->count() > 0)
<div class="ipv-section">SOFTWARE/HARDWARE/SERVICES</div>
<hr class="ipv-rule">
<table class="ipv-tbl" style="margin-bottom:16px;">
    <thead>
        <tr>
            <th style="width:36px;">No.</th>
            <th>Description</th>
            <th class="c" style="width:52px;">Qty</th>
            <th class="r" style="width:96px;">Unit Price</th>
            <th class="r" style="width:96px;">Amount (LKR)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($visibleItems as $item)
        <tr>
            <td class="c" style="font-weight:bold;">{{ $item->item_number }}</td>
            <td>{{ $item->item_name ?? $item->description }}@if($item->warranty) <span style="color:#7f1d1d;">[{{ $item->warranty }}]</span>@endif</td>
            <td class="c">{{ number_format((float)$item->quantity, 0) }}</td>
            <td class="r">{{ number_format((float)$item->unit_price) }}</td>
            <td class="r" style="font-weight:bold;">{{ number_format((float)$item->total) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- TOTALS --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    @if($invoice->tax_amount > 0)
    <tr>
        <td width="60%"></td>
        <td style="padding:5px 8px;font-size:9pt;border-bottom:1px dashed #fecaca;">Subtotal</td>
        <td style="padding:5px 8px;text-align:right;font-size:9pt;border-bottom:1px dashed #fecaca;width:96px;">
            {{ number_format((float)$invoice->subtotal) }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="padding:5px 8px;font-size:9pt;border-bottom:1px dashed #fecaca;">Tax</td>
        <td style="padding:5px 8px;text-align:right;font-size:9pt;border-bottom:1px dashed #fecaca;">
            {{ number_format((float)$invoice->tax_amount) }}
        </td>
    </tr>
    @endif
    <tr>
        <td></td>
        <td style="padding:8px 8px;font-size:9.5pt;font-weight:bold;
                   border-top:1px solid #111111;border-bottom:3px double #111111;">TOTAL (LKR)</td>
        <td style="padding:8px 8px;text-align:right;font-size:13pt;font-weight:bold;
                   white-space:nowrap;border-top:1px solid #111111;border-bottom:3px double #111111;background:#b91c1c;color:#fff;">
            {{ number_format((float)$invoice->total_amount) }}
        </td>
    </tr>
    @if($invoice->paid_amount > 0 && $invoice->payment_status !== 'paid')
    <tr>
        <td></td>
        <td style="padding:5px 8px;font-size:9pt;border-bottom:1px dashed #fecaca;">Amount Paid</td>
        <td style="padding:5px 8px;text-align:right;font-size:9pt;border-bottom:1px dashed #fecaca;">
            {{ number_format((float)$invoice->paid_amount) }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="padding:7px 8px;font-size:9.5pt;font-weight:bold;border-bottom:1px solid #111111;">Balance Due (LKR)</td>
        <td style="padding:7px 8px;text-align:right;font-size:11pt;font-weight:bold;border-bottom:1px solid #111111;">
            {{ number_format((float)$invoice->balance) }}
        </td>
    </tr>
    @endif
</table>

{{-- PAYMENT DETAILS --}}
@if($invoice->payment_status !== 'paid')
<div class="ipv-section">Payment Details</div>
<hr class="ipv-rule">
<table width="100%" cellpadding="0" cellspacing="0" style="font-size:9pt;line-height:1.9;margin-bottom:16px;color:#111111;background:#fef2f2;border:1px solid #fecaca;padding:8px 10px;">
    <tr>
        <td width="22%" style="font-weight:bold;padding:4px 0;">Bank</td>
        <td style="padding:4px 0;">:&nbsp;{{ $settings['bank_name'] ?? 'DFCC Bank' }}</td>
        <td width="22%" style="font-weight:bold;padding:4px 0;">Branch</td>
        <td style="padding:4px 0;">:&nbsp;{{ $settings['bank_branch'] ?? 'Gampaha' }}</td>
    </tr>
    <tr>
        <td style="font-weight:bold;padding:4px 0;">Account Name</td>
        <td style="padding:4px 0;">:&nbsp;{{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</td>
        <td style="font-weight:bold;padding:4px 0;">Account No</td>
        <td style="padding:4px 0;">:&nbsp;{{ $settings['bank_account_number'] ?? '102003031923' }}</td>
    </tr>
</table>
@endif

{{-- PAID STAMP --}}
@php $paidStampPath = public_path('images/paid-stamp.png'); @endphp
@if($invoice->payment_status === 'paid' && file_exists($paidStampPath))
<div style="text-align:center;margin-top:16px;margin-bottom:8px;">
    <img src="{{ $paidStampPath }}" alt="PAID" style="width:190px;height:160px;opacity:0.7;">
</div>
@endif

<hr style="border:none;border-top:1px solid #b91c1c;margin-top:18px;margin-bottom:8px;">
<div style="text-align:center;font-size:9pt;font-weight:bold;margin-bottom:3px;color:#111111;">
    Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
</div>
<div style="text-align:center;font-size:8pt;color:#b91c1c;">
    For inquiries: {{ $settings['company_phone'] ?? '' }}@if(!empty($settings['company_email'])) &nbsp;/&nbsp; {{ $settings['company_email'] }}@endif
</div>

</div>{{-- end body --}}
</div>{{-- end paper --}}

{{-- Payment History --}}
@if($invoice->payments->count())
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-4">
    <h3 class="text-sm font-semibold text-gray-800 mb-4">Payment History</h3>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="pb-2 text-left text-xs font-medium text-gray-500">Date</th>
                <th class="pb-2 text-left text-xs font-medium text-gray-500">Method</th>
                <th class="pb-2 text-left text-xs font-medium text-gray-500">Reference</th>
                <th class="pb-2 text-right text-xs font-medium text-gray-500">Amount</th>
                <th class="pb-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($invoice->payments as $pmt)
            <tr>
                <td class="py-2 text-gray-600">{{ $pmt->payment_date->format('d M Y') }}</td>
                <td class="py-2 capitalize text-gray-600">{{ str_replace('_', ' ', $pmt->payment_method) }}</td>
                <td class="py-2 text-gray-500">{{ $pmt->reference_number ?: '—' }}</td>
                <td class="py-2 text-right font-semibold text-green-600">LKR {{ number_format($pmt->amount) }}</td>
                <td class="py-2 pl-2">
                    <form method="POST" action="{{ route('invoices.payment.delete', [$invoice, $pmt]) }}" onsubmit="return confirm('Remove this payment?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-500 text-xs"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="flex items-center gap-3 mt-2">
    <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-arrow-left mr-1"></i> Back to list
    </a>
    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-500 hover:text-red-700">
            <i class="fa-solid fa-trash mr-1"></i> Delete
        </button>
    </form>
</div>
</div>

{{-- Record Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-2 h-8 bg-green-600 rounded-full"></div>
                <h3 class="text-xl font-bold text-gray-900">Record Payment</h3>
            </div>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="pmt_date" class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
                    <input type="date" id="pmt_date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200 transition" required>
                </div>
                <div>
                    <label for="pmt_amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount (LKR) *</label>
                    <input type="number" id="pmt_amount" name="amount" value="{{ $invoice->balance }}" step="0.01" min="0.01"
                        class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200 transition" required>
                </div>
            </div>
            <div>
                <label for="pmt_method" class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
                <select id="pmt_method" name="payment_method" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200 transition" required>
                    @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'card' => 'Card', 'cheque' => 'Cheque'] as $v => $l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pmt_ref" class="block text-sm font-semibold text-gray-700 mb-2">Reference Number</label>
                <input type="text" id="pmt_ref" name="reference_number" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200 transition" placeholder="Optional">
            </div>
            <div>
                <label for="pmt_notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea id="pmt_notes" name="notes" rows="2" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200 transition resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-green-600 text-white text-sm font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-check mr-2"></i>Save Payment
                </button>
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 bg-gray-100 border-2 border-gray-200 text-gray-700 text-sm font-medium py-3 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
