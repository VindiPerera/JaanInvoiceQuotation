<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
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
    Q U O T A T I O N
</div>

<hr style="border:none;border-top:1px solid #111111;margin-bottom:14px;">

{{-- BILLING + DOC DETAILS --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td width="58%" style="vertical-align:top;padding:10px 14px 10px 12px;background:#fef2f2;border:1px solid #fecaca;border-top:2px solid #b91c1c;">
            <div style="font-size:7.5pt;text-transform:uppercase;letter-spacing:1.5px;color:#7f1d1d;margin-bottom:6px;">Bill To</div>
            <div style="font-weight:bold;font-size:11pt;line-height:1.3;margin-bottom:4px;">{{ $quotation->customer_name }}</div>
            @if($quotation->customer_address)
            <div style="font-size:9pt;color:#111111;margin-bottom:2px;">{{ $quotation->customer_address }}</div>
            @endif
            @if($quotation->customer_contact)
            <div style="font-size:9pt;color:#111111;">{{ $quotation->customer_contact }}</div>
            @endif
        </td>
        <td width="42%" style="vertical-align:top;padding:10px 12px;background:#fee2e2;border:1px solid #fecaca;border-top:2px solid #b91c1c;">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:9pt;line-height:1.8;color:#111111;">
                <tr>
                    <td style="font-weight:bold;width:50%;">Quotation No</td>
                    <td style="text-align:right;">:&nbsp;{{ $quotation->quotation_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Date</td>
                    <td style="text-align:right;">:&nbsp;{{ $quotation->quotation_date->format('d M Y') }}</td>
                </tr>
                @if($quotation->subject)
                <tr>
                    <td style="font-weight:bold;">Subject</td>
                    <td style="text-align:right;">:&nbsp;{{ $quotation->subject }}</td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<hr style="border:none;border-top:1px solid #111111;margin-bottom:16px;">

{{-- INTRODUCTION / PROJECT OVERVIEW --}}
@if($quotation->project_overview)
<div style="font-size:9pt;line-height:1.75;margin-bottom:6px;text-align:justify;color:#111111;">
    {!! nl2br(htmlspecialchars($quotation->project_overview)) !!}
</div>
@else
<div style="font-size:9pt;line-height:1.75;margin-bottom:4px;color:#111111;">Dear Valued Customer,</div>
<div style="font-size:9pt;line-height:1.75;margin-bottom:6px;text-align:justify;color:#111111;">
    Thank you for your interest in our products and services. We are pleased to present the
    following quotation, carefully tailored to meet your business requirements. Please review
    the details below and feel free to contact us for any clarifications or further assistance.
</div>
@endif

@php
$quoteType = $quotation->quote_type ?? 'full_set';
$visibleItems = $quotation->items->where('is_hidden', false);
$hasQtyPrice = $visibleItems->some(fn($item) => (float)$item->quantity > 0 || (float)$item->unit_price > 0);
@endphp

{{-- HARDWARE PACKAGE --}}
@if($visibleItems->count() > 0)
<div class="sec">Software/Hardware/Services</div>
<hr class="rule">
<table class="tbl" style="margin-bottom:8px;">
    <thead>
        <tr>
            <th class="c" width="38">No.</th>
            <th>Description</th>
            @if($hasQtyPrice)
            <th class="c" width="52">Qty</th>
            <th class="r" width="60">Price</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($visibleItems as $item)
        <tr>
            <td class="c" style="font-weight:bold;vertical-align:top;">{{ $item->item_number }}</td>
            <td style="vertical-align:top;">
                @if($item->item_name)
                <div style="font-weight:bold;margin-bottom:10px;font-size:9.5pt;">{{ $item->item_name }}@if($item->warranty) <span style="font-weight:normal;color:#7f1d1d;font-size:8.5pt;">[{{ $item->warranty }}]</span>@endif</div>
                @endif
                @if($item->description)
                <div style="font-size:9pt;color:#333333;line-height:1.8;white-space:pre-wrap;word-wrap:break-word;">@php
                    $lines = array_filter(array_map('trim', explode("\n", $item->description)));
                    echo implode("</div><div style=\"font-size:9pt;color:#333333;line-height:1.8;margin:4px 0;\">", $lines);
                @endphp</div>
                @endif
            </td>
            @if($hasQtyPrice)
            <td class="c" style="font-weight:bold;vertical-align:top;">{{ $item->quantity }}</td>
            <td class="r" style="font-weight:bold;vertical-align:top;">LKR {{ number_format($item->unit_price, 2) }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- PAYMENT BREAKDOWN --}}
<div class="sec">Payment Breakdown</div>
<hr class="rule">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="font-size:9pt;padding:6px 0;">
            @if($quoteType === 'software_only') Software Package Price
            @elseif($quoteType === 'hardware_only') Hardware Package Price
            @else Total
            @endif
        </td>
        <td style="text-align:right;font-size:13pt;font-weight:bold;white-space:nowrap;padding:6px 0;">
            <span style="display:inline-block;background:#b91c1c;border:1px solid #b91c1c;padding:6px 10px;color:#fff;">LKR {{ number_format($quotation->total_amount) }}.00</span>
        </td>
    </tr>
</table>
<hr class="drule">

{{-- CONTACT INFORMATION --}}
<div class="sec">Contact Information</div>
<hr class="rule">
<div style="font-size:9pt;line-height:1.9;margin-bottom:8px;color:#111111;background:#fef2f2;border:1px solid #fecaca;padding:8px 10px;">
    {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}<br>
    {{ $settings['company_address'] ?? 'No 46, Hudson Rd, Colombo 03' }}<br>
    {{ $settings['company_phone'] ?? '+94 76 59 33 255' }}<br>
    Monday &ndash; Saturday &nbsp; 9:00 AM &ndash; 6:00 PM
</div>

<hr style="border:none;border-top:1px solid #b91c1c;margin-top:18px;margin-bottom:8px;">
<div style="text-align:center;font-size:9pt;font-weight:bold;margin-bottom:3px;color:#111111;">
    Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
</div>
<div style="text-align:center;font-size:8pt;color:#7f1d1d;">
    This quotation is valid for 30 days from the date of issue.
</div>

</div>{{-- end #content --}}
</body>
</html>
