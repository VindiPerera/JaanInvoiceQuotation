<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #1a1a1a; line-height: 1.5; }

/* Zero top margin — the inline header sits flush at the physical top of page 1.
   bottom 12mm   = space reserved for the fixed page footer             */
@page { size: A4 portrait; margin: 0 0 12mm 0; }

/* ── PAGE FOOTER — fixed in the 12mm bottom margin, repeats every page  */
#page-footer {
    position: fixed;
    bottom: -12mm; left: 0; right: 0;
    height: 12mm;
    background: #fff;
}
.pagenum:before { content: counter(page); }

/* ── ITEMS TABLE ─────────────────────────────────────────────────────── */
.items-table { width: 100%; border-collapse: collapse; }
.items-table thead tr { background: #cc1010; color: #fff; }
.items-table th { padding: 6px 8px; font-size: 8pt; font-weight: bold; text-align: left; }
.items-table th.r { text-align: right; }
.items-table th.c { text-align: center; }
.items-table td { padding: 7px 8px; border: 1px solid #e0e0e0; font-size: 9pt; vertical-align: top; }
.items-table td.r { text-align: right; }
.items-table td.c { text-align: center; }
.items-table tbody tr:nth-child(odd)  { background: #fff; }
.items-table tbody tr:nth-child(even) { background: #f6f6f6; }
.items-table tbody tr { page-break-inside: avoid; }

/* ── TERMS ───────────────────────────────────────────────────────────── */
.t-head   { font-size: 9pt;   font-weight: bold; color: #1a1a1a; margin: 7px 0 2px; page-break-after: avoid; }
.t-sub    { font-size: 8.5pt; font-weight: bold; color: #333;    margin: 5px 0 2px; page-break-after: avoid; }
.t-body   { font-size: 8.5pt; color: #444; margin-bottom: 3px; }
.t-bullet { font-size: 8.5pt; color: #333; padding-left: 10px; margin-bottom: 3px; }
</style>
</head>
<body>

{{-- ════════ INLINE HEADER — appears on page 1 ════════ --}}
@php $logoPath = !empty($settings['company_logo']) ? public_path($settings['company_logo']) : null; @endphp
<div style="margin:0;padding:0;">
    {{-- Full physical-width red top bar --}}
    <div style="height:9mm;background:#cc1010;"></div>
    {{-- Logo | Company name | Contact --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="padding:6px 14mm 5px;table-layout:fixed;">
        <tr>
            <td width="28%" style="vertical-align:middle;">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo" style="max-height:50px;max-width:150px;">
                @else
                    <div style="display:inline-block;border:2px solid #cc1010;
                                padding:5px 12px 4px 8px;line-height:1.15;">
                        <div style="font-size:13pt;font-weight:bold;color:#cc1010;letter-spacing:4px;">JAAN</div>
                        <div style="font-size:7pt;font-weight:bold;color:#1a1a1a;letter-spacing:2px;">Network</div>
                    </div>
                @endif
            </td>
            <td width="44%" style="text-align:center;vertical-align:middle;">
                <div style="font-size:14pt;font-weight:bold;color:#cc1010;letter-spacing:0.5px;line-height:1.2;">
                    {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
                </div>
                <div style="font-size:8pt;color:#888;font-style:italic;margin-top:3px;">
                    Professional IT Solutions &amp; Services
                </div>
            </td>
            <td width="28%" style="text-align:right;vertical-align:middle;padding-right:2px;">
                @if(!empty($settings['company_phone']))
                <div style="font-size:8pt;color:#444;">{{ $settings['company_phone'] }}</div>
                @endif
                @if(!empty($settings['company_email']))
                <div style="font-size:7.5pt;color:#888;margin-top:2px;">{{ $settings['company_email'] }}</div>
                @endif
                @if(!empty($settings['company_address']))
                <div style="font-size:7pt;color:#aaa;margin-top:2px;">{{ $settings['company_address'] }}</div>
                @endif
            </td>
        </tr>
    </table>
    {{-- Red divider --}}
    <div style="height:2px;background:#cc1010;"></div>
</div>

{{-- ════════ FIXED PAGE FOOTER (repeats on every page) ════════ --}}
<div id="page-footer">
    <div style="height:1px;background:#e0e0e0;margin:0 14mm 2px;"></div>
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:1px 14mm 0;">
        <tr>
            <td style="font-size:7pt;color:#999;">
                {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
                @if(!empty($settings['company_phone']))<span style="color:#ccc">&nbsp;|&nbsp;</span>{{ $settings['company_phone'] }}@endif
            </td>
            <td style="font-size:7pt;color:#999;text-align:right;">
                Page <span class="pagenum"></span>
            </td>
        </tr>
    </table>
    <div style="height:5mm;background:#cc1010;margin-top:2px;"></div>
</div>

{{-- ════════ PAGE CONTENT (flows between header and footer) ════════ --}}
<div style="padding:10px 14mm 8px;">

{{-- DOCUMENT TITLE + CLIENT INFO --}}
<table width="100%" cellpadding="0" cellspacing="0"
       style="margin-bottom:14px;border-bottom:1px solid #e8e8e8;padding-bottom:12px;table-layout:fixed;">
    <tr>
        <td width="54%" style="vertical-align:top;padding-right:12px;">
            <div style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:4px;">Prepared For</div>
            <div style="font-size:13pt;font-weight:bold;color:#1a1a1a;line-height:1.2;">
                {{ $invoice->customer_name }}
            </div>
            @if($invoice->customer_address)
            <div style="font-size:8.5pt;color:#555;margin-top:3px;">{{ $invoice->customer_address }}</div>
            @endif
            @if($invoice->customer_contact)
            <div style="font-size:8.5pt;color:#555;">{{ $invoice->customer_contact }}</div>
            @endif
        </td>
        <td width="46%" style="text-align:right;vertical-align:top;">
            <div style="font-size:15pt;font-weight:bold;color:#1a1a1a;line-height:1.1;margin-bottom:8px;">
                SALES INVOICE
            </div>
            <table cellpadding="3" cellspacing="0" style="margin-left:auto;border-collapse:collapse;">
                <tr>
                    <td style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;
                               letter-spacing:1px;text-align:right;padding-right:8px;">Invoice No</td>
                    <td style="font-size:10pt;font-weight:bold;color:#cc1010;text-align:right;
                               white-space:nowrap;">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td style="font-size:7pt;font-weight:bold;color:#888;text-transform:uppercase;
                               letter-spacing:1px;text-align:right;padding-right:8px;">Date</td>
                    <td style="font-size:10pt;font-weight:bold;text-align:right;
                               white-space:nowrap;">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ITEMS TABLE --}}
<table class="items-table" style="margin-bottom:14px;">
    <thead>
        <tr>
            <th width="30">Item</th>
            <th>Description</th>
            <th class="c" width="48">Qty</th>
            <th class="r" width="85">Price (LKR)</th>
            <th class="r" width="85">Total (LKR)</th>
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
            <td colspan="4" style="text-align:right;padding:5px 8px;font-weight:bold;
                                   border:1px solid #e0e0e0;">Subtotal</td>
            <td class="r" style="border:1px solid #e0e0e0;">{{ number_format($invoice->subtotal) }}</td>
        </tr>
        <tr style="background:#f5f5f5;">
            <td colspan="4" style="text-align:right;padding:5px 8px;font-weight:bold;
                                   border:1px solid #e0e0e0;">Tax</td>
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
    </tfoot>
</table>

{{-- PAYMENT DETAILS (hidden when paid) --}}
@if($invoice->payment_status !== 'paid')
<div style="border:1px solid #e0e0e0;border-left:3px solid #cc1010;padding:10px 12px;margin-bottom:14px;">
    <div style="font-weight:bold;font-size:9pt;margin-bottom:6px;padding-bottom:5px;
                border-bottom:1px solid #eee;text-transform:uppercase;letter-spacing:0.5px;">
        Payment Details
    </div>
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

{{-- TERMS & CONDITIONS --}}
@if($invoice->terms_conditions)
<div style="border-top:1px solid #e8e8e8;padding-top:10px;margin-bottom:14px;">
    <div style="font-size:8pt;font-weight:bold;text-transform:uppercase;letter-spacing:1px;
                color:#1a1a1a;margin-bottom:6px;">Terms &amp; Conditions</div>
    @php
        $termsLines = array_map('rtrim', explode("\n", $invoice->terms_conditions));
        $prevEmpty  = true;
        $counter    = 0;
    @endphp
    @foreach($termsLines as $tLine)
        @if(trim($tLine) === '')
            <div style="height:3pt;"></div>
            @php $prevEmpty = true; @endphp
        @elseif(mb_substr(ltrim($tLine), 0, 1) === '●' || mb_substr(ltrim($tLine), 0, 1) === '•')
            <div class="t-bullet"><span style="color:#cc1010;font-weight:bold;">&#9679;</span>&nbsp;{{ preg_replace('/^[\s●•]+/', '', $tLine) }}</div>
            @php $prevEmpty = false; @endphp
        @elseif($prevEmpty)
            @php $counter++; @endphp
            <div class="t-head">{{ $counter }}. {{ $tLine }}</div>
            @php $prevEmpty = false; @endphp
        @elseif(mb_strlen(trim($tLine)) < 60 && mb_substr(trim($tLine), -1) === ':')
            <div class="t-sub">{{ $tLine }}</div>
            @php $prevEmpty = false; @endphp
        @else
            <div class="t-body">{{ $tLine }}</div>
            @php $prevEmpty = false; @endphp
        @endif
    @endforeach
</div>
@endif

{{-- PAID STAMP — shown above closing when invoice is paid --}}
@php $paidStampPath = public_path('images/paid-stamp.png'); @endphp
@if($invoice->payment_status === 'paid' && file_exists($paidStampPath))
<div style="text-align:center;margin-bottom:10px;">
    <img src="{{ $paidStampPath }}" alt="PAID" style="width:130px;height:130px;">
</div>
@endif

{{-- CLOSING --}}
<div style="text-align:center;padding-top:10px;border-top:1px solid #e8e8e8;">
    <div style="font-size:9.5pt;font-weight:bold;color:#cc1010;">
        Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
    </div>
    <div style="font-size:7.5pt;color:#888;margin-top:3px;">
        For any inquiries or clarifications, please don&#8217;t hesitate to contact us.
    </div>
</div>

</div>{{-- end content --}}
</body>
</html>
