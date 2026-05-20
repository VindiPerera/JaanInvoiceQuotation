<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: "DejaVu Sans", "Helvetica", sans-serif;
    font-size: 9.5pt;
    color: #111111;
    line-height: 1.6;
    background: #fff;
}

/* DomPDF ignores @page side/top margins — use padding wrapper instead */
@page { size: A4 portrait; margin: 0 0 18mm 0; }

#footer {
    position: fixed;
    bottom: -18mm; left: 0; right: 0;
    height: 18mm;
    padding: 0 20mm;
}
.pagenum:before { content: counter(page); }

/* All page content sits inside this padded wrapper */
#content { padding: 18mm 20mm 12mm 20mm; }

.sec {
    font-weight: bold;
    font-size: 8.2pt;
    text-transform: uppercase;
    letter-spacing: 1.6px;
    margin: 16px 0 4px;
    color: #b91c1c;
    display: inline-block;
    padding: 4px 10px 4px 8px;
    border-left: 3px solid #b91c1c;
    background: #fef2f2;
    border-radius: 2px;
}
.rule  { border: none; border-top: 1px solid #b91c1c; margin: 0 0 8px; }
.drule { border: none; border-top: 3px double #111111; margin: 6px 0 0; }

.tbl { width: 100%; border-collapse: collapse; }
.tbl th {
    font-size: 8pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
    padding: 6px 8px;
    border-top: 1px solid #111111;
    border-bottom: 1px solid #111111;
    background: #fee2e2;
}
.tbl th.r { text-align: right; }
.tbl th.c { text-align: center; }
.tbl td {
    font-size: 9pt;
    padding: 7px 8px;
    vertical-align: top;
    border-bottom: 1px dashed #fecaca;
}
.tbl td.r { text-align: right; }
.tbl td.c { text-align: center; }
.tbl tbody tr:nth-child(even) td { background: #fef2f2; }
.tbl tbody tr:last-child td { border-bottom: 1px solid #111111; }
</style>
</head>
<body>

{{-- ── FOOTER ── --}}
<div id="footer">
    <hr style="border:none;border-top:1px solid #b91c1c;margin-bottom:5px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="font-size:7.5pt;color:#7f1d1d;">{{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}</td>
            <td style="font-size:7.5pt;color:#7f1d1d;text-align:right;">Page <span class="pagenum"></span></td>
        </tr>
    </table>
</div>

{{-- ── ALL PAGE CONTENT ── --}}
<div id="content">

{{-- LETTERHEAD --}}
@php $logoPath = !empty($settings['company_logo']) ? public_path($settings['company_logo']) : null; @endphp
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
    <tr>
        <td width="60%" style="vertical-align:bottom;padding-right:10px;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" style="max-height:48px;max-width:140px;display:block;margin-bottom:6px;">
            @endif
            <div style="font-size:14pt;font-weight:bold;line-height:1.15;letter-spacing:0.3px;color:#111111;">
                {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}
            </div>
            <div style="font-size:8.5pt;color:#7f1d1d;margin-top:2px;">
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

<hr style="border:none;border-top:2px solid #b91c1c;margin-bottom:3px;">
<hr style="border:none;border-top:1px solid #111111;margin-bottom:14px;">

{{-- DOCUMENT TITLE --}}
<div style="text-align:center;font-size:16pt;font-weight:bold;letter-spacing:5px;margin:8px 0 12px;padding:8px 0;border-top:2px solid #b91c1c;border-bottom:1px solid #b91c1c;border-left:1px solid #fecaca;border-right:1px solid #fecaca;background:#fef2f2;color:#b91c1c;">
    S A L E S &nbsp; I N V O I C E
</div>

<hr style="border:none;border-top:1px solid #111111;margin-bottom:14px;">

{{-- BILLING + DOC DETAILS --}}
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

<hr style="border:none;border-top:1px solid #111111;margin-bottom:16px;">

{{-- ITEMS --}}
<div class="sec">Items</div>
<hr class="rule">
<table class="tbl">
    <thead>
        <tr>
            <th class="c" width="36">No.</th>
            <th>Description</th>
            <th class="c" width="52">Qty</th>
            <th class="r" width="96">Unit Price</th>
            <th class="r" width="96">Amount (LKR)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td class="c" style="font-weight:bold;">{{ $item->item_number }}</td>
            <td>{{ $item->item_name }}@if($item->warranty) <span style="color:#7f1d1d;">[{{ $item->warranty }}]</span>@endif</td>
            <td class="c">{{ number_format((float)$item->quantity, 0) }}</td>
            <td class="r">{{ number_format((float)$item->unit_price) }}</td>
            <td class="r" style="font-weight:bold;">{{ number_format((float)$item->total) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- TOTALS --}}
<table width="100%" cellpadding="0" cellspacing="0">
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
<div class="sec">Payment Details</div>
<hr class="rule">
<table width="100%" cellpadding="0" cellspacing="0" style="font-size:9pt;line-height:1.9;margin-bottom:8px;color:#111111;background:#fef2f2;border:1px solid #fecaca;padding:8px 10px;">
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

{{-- TERMS & CONDITIONS --}}
@if($invoice->terms_conditions)
<div class="sec">Terms &amp; Conditions</div>
<hr class="rule">
<div style="margin-bottom:8px;font-size:8.5pt;line-height:1.8;color:#111111;">
    @php
        $termsLines = array_map('rtrim', explode("\n", $invoice->terms_conditions));
        $prevEmpty  = true;
        $counter    = 0;
    @endphp
    @foreach($termsLines as $tLine)
        @if(trim($tLine) === '')
            <div style="height:5pt;"></div>
            @php $prevEmpty = true; @endphp
        @elseif(mb_substr(ltrim($tLine), 0, 1) === '●' || mb_substr(ltrim($tLine), 0, 1) === '•')
            <div style="padding-left:14px;margin-bottom:2px;">- {{ preg_replace('/^[\s●•]+/', '', $tLine) }}</div>
            @php $prevEmpty = false; @endphp
        @elseif($prevEmpty)
            @php $counter++; @endphp
            <div style="font-weight:bold;margin-top:6px;">{{ $counter }}. {{ $tLine }}</div>
            @php $prevEmpty = false; @endphp
        @elseif(mb_strlen(trim($tLine)) < 60 && mb_substr(trim($tLine), -1) === ':')
            <div style="font-weight:bold;margin-top:2px;">{{ $tLine }}</div>
            @php $prevEmpty = false; @endphp
        @else
            <div style="margin-bottom:2px;">{{ $tLine }}</div>
            @php $prevEmpty = false; @endphp
        @endif
    @endforeach
</div>
@endif

{{-- PAID STAMP --}}
@php $paidStampPath = public_path('images/paid-stamp.png'); @endphp
@if($invoice->payment_status === 'paid' && file_exists($paidStampPath))
<div style="text-align:center;margin-top:16px;margin-bottom:8px;">
    <img src="{{ $paidStampPath }}" alt="PAID" style="width:110px;height:110px;opacity:0.7;">
</div>
@endif

<hr style="border:none;border-top:1px solid #b91c1c;margin-top:18px;margin-bottom:8px;">
<div style="text-align:center;font-size:9pt;font-weight:bold;margin-bottom:3px;color:#111111;">
    Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
</div>
<div style="text-align:center;font-size:8pt;color:#7f1d1d;">
    For inquiries: {{ $settings['company_phone'] ?? '' }}@if(!empty($settings['company_email'])) &nbsp;/&nbsp; {{ $settings['company_email'] }}@endif
</div>

</div>{{-- end #content --}}
</body>
</html>
