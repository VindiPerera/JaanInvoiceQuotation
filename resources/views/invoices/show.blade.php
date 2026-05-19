@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number)

@section('header-actions')
    <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
    <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-pencil"></i> Edit
    </a>
    @if($invoice->payment_status !== 'paid')
    <button onclick="document.getElementById('paymentModal').classList.remove('hidden')"
        class="inline-flex items-center gap-2 bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="fa-solid fa-money-bill"></i> Record Payment
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
.ipv-tbl thead tr { background:#cc1010;color:#fff; }
.ipv-tbl th  { padding:6px 8px;font-size:8pt;font-weight:bold;text-align:left; }
.ipv-tbl th.r { text-align:right; }
.ipv-tbl th.c { text-align:center; }
.ipv-tbl td  { padding:7px 8px;border:1px solid #e0e0e0;font-size:9pt;vertical-align:top; }
.ipv-tbl td.r { text-align:right; }
.ipv-tbl td.c { text-align:center; }
.ipv-tbl tbody tr:nth-child(odd)  { background:#fff; }
.ipv-tbl tbody tr:nth-child(even) { background:#f6f6f6; }
.ipv-th  { font-size:9pt;font-weight:bold;color:#1a1a1a;margin:7px 0 2px; }
.ipv-ts  { font-size:8.5pt;font-weight:bold;color:#333;margin:5px 0 2px; }
.ipv-tb  { font-size:8.5pt;color:#444;margin-bottom:3px; }
.ipv-tbu { font-size:8.5pt;color:#333;padding-left:10px;margin-bottom:3px; }
</style>

@php $logoPath = !empty($settings['company_logo']) ? public_path($settings['company_logo']) : null; @endphp

{{-- Red top bar --}}
<div style="height:9mm;background:#cc1010;"></div>

{{-- Header: Logo | Company | Contact --}}
<table width="100%" cellpadding="0" cellspacing="0" style="padding:6px 30px 5px;table-layout:fixed;">
    <tr>
        <td width="28%" style="vertical-align:middle;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" style="max-height:50px;max-width:150px;">
            @else
                <div style="display:inline-block;border:2px solid #cc1010;padding:5px 12px 4px 8px;line-height:1.15;">
                    <div style="font-size:13pt;font-weight:bold;color:#cc1010;letter-spacing:4px;">JAAN</div>
                    <div style="font-size:7pt;font-weight:bold;color:#1a1a1a;letter-spacing:2px;">Network</div>
                </div>
            @endif
        </td>
        <td width="44%" style="text-align:center;vertical-align:middle;">
            <div style="font-size:14pt;font-weight:bold;color:#cc1010;letter-spacing:.5px;line-height:1.2;">
                {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
            </div>
            <div style="font-size:8pt;color:#888;font-style:italic;margin-top:3px;">Professional IT Solutions &amp; Services</div>
        </td>
        <td width="28%" style="text-align:right;vertical-align:middle;padding-right:2px;">
            @if(!empty($settings['company_phone']))<div style="font-size:8pt;color:#444;">{{ $settings['company_phone'] }}</div>@endif
            @if(!empty($settings['company_email']))<div style="font-size:7.5pt;color:#888;margin-top:2px;">{{ $settings['company_email'] }}</div>@endif
            @if(!empty($settings['company_address']))<div style="font-size:7pt;color:#aaa;margin-top:2px;">{{ $settings['company_address'] }}</div>@endif
        </td>
    </tr>
</table>
<div style="height:2px;background:#cc1010;"></div>

{{-- Body --}}
<div style="padding:20px 30px 20px;">

    {{-- Client info + Invoice title --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="margin-bottom:14px;border-bottom:1px solid #e8e8e8;padding-bottom:12px;table-layout:fixed;">
        <tr>
            <td width="54%" style="vertical-align:top;padding-right:12px;">
                <div style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Prepared For</div>
                <div style="font-size:13pt;font-weight:bold;color:#1a1a1a;line-height:1.2;">{{ $invoice->customer_name }}</div>
                @if($invoice->customer_address)<div style="font-size:8.5pt;color:#555;margin-top:3px;">{{ $invoice->customer_address }}</div>@endif
                @if($invoice->customer_contact)<div style="font-size:8.5pt;color:#555;">{{ $invoice->customer_contact }}</div>@endif
            </td>
            <td width="46%" style="text-align:right;vertical-align:top;">
                <div style="font-size:15pt;font-weight:bold;color:#1a1a1a;line-height:1.1;margin-bottom:8px;">SALES INVOICE</div>
                <table cellpadding="3" cellspacing="0" style="margin-left:auto;border-collapse:collapse;">
                    <tr>
                        <td style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:right;padding-right:8px;">Invoice No</td>
                        <td style="font-size:10pt;font-weight:bold;color:#cc1010;text-align:right;white-space:nowrap;">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:right;padding-right:8px;">Date</td>
                        <td style="font-size:10pt;font-weight:bold;text-align:right;white-space:nowrap;">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:right;padding-right:8px;">Status</td>
                        <td style="font-size:9pt;font-weight:bold;text-align:right;white-space:nowrap;
                            color:{{ $invoice->payment_status === 'paid' ? '#16a34a' : ($invoice->payment_status === 'partial' ? '#d97706' : '#dc2626') }};">
                            {{ ucfirst($invoice->payment_status) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Items table --}}
    <table class="ipv-tbl" style="margin-bottom:14px;">
        <thead>
            <tr>
                <th width="30">Item</th>
                <th>Description</th>
                <th class="c" width="48">Qty</th>
                <th class="r" width="100">Price (LKR)</th>
                <th class="r" width="100">Total (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td style="color:#cc1010;font-weight:bold;text-align:center;">{{ $item->item_number }}</td>
                <td>{{ $item->description }}</td>
                <td class="c">{{ number_format($item->quantity, 2) }}</td>
                <td class="r">{{ number_format($item->unit_price) }}</td>
                <td class="r" style="font-weight:bold;">{{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($invoice->tax_amount > 0)
            <tr style="background:#f5f5f5;">
                <td colspan="4" style="text-align:right;padding:5px 8px;font-weight:bold;border:1px solid #e0e0e0;">Subtotal</td>
                <td class="r" style="border:1px solid #e0e0e0;">{{ number_format($invoice->subtotal) }}</td>
            </tr>
            <tr style="background:#f5f5f5;">
                <td colspan="4" style="text-align:right;padding:5px 8px;font-weight:bold;border:1px solid #e0e0e0;">Tax</td>
                <td class="r" style="border:1px solid #e0e0e0;">{{ number_format($invoice->tax_amount) }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="4" style="text-align:right;padding:9px 8px;font-weight:bold;font-size:9.5pt;
                                       border:1px solid #ddd;background:#f0f0f0;color:#333;">TOTAL (LKR)</td>
                <td style="text-align:right;padding:9px 8px;font-weight:bold;color:#cc1010;font-size:13pt;
                           white-space:nowrap;border:1px solid #ddd;background:#fff9f9;">
                    {{ number_format($invoice->total_amount) }}
                </td>
            </tr>
            @if($invoice->paid_amount > 0)
            <tr style="background:#f0fdf4;">
                <td colspan="4" style="text-align:right;padding:6px 8px;font-weight:bold;border:1px solid #e0e0e0;color:#16a34a;">Paid (LKR)</td>
                <td style="text-align:right;padding:6px 8px;font-weight:bold;border:1px solid #e0e0e0;color:#16a34a;">{{ number_format($invoice->paid_amount) }}</td>
            </tr>
            <tr style="background:{{ $invoice->balance > 0 ? '#fff9f9' : '#f0fdf4' }};">
                <td colspan="4" style="text-align:right;padding:6px 8px;font-weight:bold;border:1px solid #e0e0e0;
                                       color:{{ $invoice->balance > 0 ? '#dc2626' : '#16a34a' }};">Balance Due (LKR)</td>
                <td style="text-align:right;padding:6px 8px;font-weight:bold;border:1px solid #e0e0e0;
                           color:{{ $invoice->balance > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($invoice->balance) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    {{-- Payment Details (hidden when paid) --}}
    @if($invoice->payment_status !== 'paid')
    <div style="border:1px solid #e0e0e0;border-left:3px solid #cc1010;padding:10px 12px;margin-bottom:14px;">
        <div style="font-weight:bold;font-size:9pt;margin-bottom:6px;padding-bottom:5px;
                    border-bottom:1px solid #eee;text-transform:uppercase;letter-spacing:.5px;">Payment Details</div>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="22%" style="color:#888;font-size:8.5pt;padding-bottom:4px;">Bank Name:</td>
                <td style="font-weight:bold;font-size:8.5pt;padding-bottom:4px;">{{ $settings['bank_name'] ?? 'DFCC Bank' }}</td>
                <td width="22%" style="color:#888;font-size:8.5pt;padding-bottom:4px;padding-left:20px;">Branch:</td>
                <td style="font-weight:bold;font-size:8.5pt;padding-bottom:4px;">{{ $settings['bank_branch'] ?? 'Gampaha' }}</td>
            </tr>
            <tr>
                <td style="color:#888;font-size:8.5pt;">Account Name:</td>
                <td style="font-weight:bold;font-size:8.5pt;">{{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</td>
                <td style="color:#888;font-size:8.5pt;padding-left:20px;">Account No:</td>
                <td style="font-weight:bold;font-size:8.5pt;">{{ $settings['bank_account_number'] ?? '102003031923' }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Terms & Conditions --}}
    @if($invoice->terms_conditions)
    <div style="border-top:1px solid #e8e8e8;padding-top:10px;margin-bottom:14px;">
        <div style="font-size:8pt;font-weight:bold;text-transform:uppercase;letter-spacing:1px;
                    color:#1a1a1a;margin-bottom:6px;">Terms &amp; Conditions</div>
        @php $termsLines = array_map('rtrim', explode("\n", $invoice->terms_conditions)); $prevEmpty = true; $counter = 0; @endphp
        @foreach($termsLines as $tLine)
            @if(trim($tLine) === '')<div style="height:3pt;"></div>@php $prevEmpty = true; @endphp
            @elseif(mb_substr(ltrim($tLine), 0, 1) === '●' || mb_substr(ltrim($tLine), 0, 1) === '•')
                <div class="ipv-tbu"><span style="color:#cc1010;font-weight:bold;">&#9679;</span>&nbsp;{{ preg_replace('/^[\s●•]+/', '', $tLine) }}</div>@php $prevEmpty = false; @endphp
            @elseif($prevEmpty)@php $counter++; @endphp
                <div class="ipv-th">{{ $counter }}. {{ $tLine }}</div>@php $prevEmpty = false; @endphp
            @elseif(mb_strlen(trim($tLine)) < 60 && mb_substr(trim($tLine), -1) === ':')
                <div class="ipv-ts">{{ $tLine }}</div>@php $prevEmpty = false; @endphp
            @else<div class="ipv-tb">{{ $tLine }}</div>@php $prevEmpty = false; @endphp
            @endif
        @endforeach
    </div>
    @endif

    {{-- PAID stamp --}}
    @php $paidStampPath = public_path('images/paid-stamp.png'); @endphp
    @if($invoice->payment_status === 'paid' && file_exists($paidStampPath))
    <div style="text-align:center;margin-bottom:10px;">
        <img src="{{ $paidStampPath }}" alt="PAID" style="width:130px;height:130px;">
    </div>
    @endif

    {{-- Closing --}}
    <div style="text-align:center;padding-top:10px;border-top:1px solid #e8e8e8;">
        <div style="font-size:9.5pt;font-weight:bold;color:#cc1010;">
            Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
        </div>
        <div style="font-size:7.5pt;color:#888;margin-top:3px;">
            For any inquiries or clarifications, please don&#8217;t hesitate to contact us.
        </div>
    </div>

</div>{{-- end body --}}
<div style="height:5mm;background:#cc1010;margin-top:10px;"></div>
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
<div id="paymentModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50" x-data>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900">Record Payment</h3>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="pmt_date" class="block text-xs font-medium text-gray-500 mb-1">Date *</label>
                    <input type="date" id="pmt_date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
                </div>
                <div>
                    <label for="pmt_amount" class="block text-xs font-medium text-gray-500 mb-1">Amount (LKR) *</label>
                    <input type="number" id="pmt_amount" name="amount" value="{{ $invoice->balance }}" step="0.01" min="0.01"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
                </div>
            </div>
            <div>
                <label for="pmt_method" class="block text-xs font-medium text-gray-500 mb-1">Payment Method *</label>
                <select id="pmt_method" name="payment_method" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
                    @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'card' => 'Card', 'cheque' => 'Cheque'] as $v => $l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pmt_ref" class="block text-xs font-medium text-gray-500 mb-1">Reference Number</label>
                <input type="text" id="pmt_ref" name="reference_number" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Optional">
            </div>
            <div>
                <label for="pmt_notes" class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                <textarea id="pmt_notes" name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-green-700 transition">
                    Save Payment
                </button>
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 bg-white border border-gray-200 text-gray-600 text-sm py-2.5 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
