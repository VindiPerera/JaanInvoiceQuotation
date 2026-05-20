<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Courier', 'DejaVu Sans Mono', monospace;
    font-size: 9.5pt;
    color: #000;
    line-height: 1.65;
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
#content { padding: 18mm 20mm 10mm 20mm; }

.sec {
    font-weight: bold;
    font-size: 8.5pt;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin: 18px 0 4px;
}
.rule  { border: none; border-top: 1px solid #000; margin: 0 0 10px; }
.drule { border: none; border-top: 3px double #000; margin: 4px 0 0; }

.tbl { width: 100%; border-collapse: collapse; }
.tbl th {
    font-size: 8pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
    padding: 6px 8px;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
}
.tbl th.r { text-align: right; }
.tbl th.c { text-align: center; }
.tbl td {
    font-size: 9pt;
    padding: 7px 8px;
    vertical-align: top;
    border-bottom: 1px dashed #aaa;
}
.tbl td.r { text-align: right; }
.tbl td.c { text-align: center; }
.tbl tbody tr:last-child td { border-bottom: 1px solid #000; }
</style>
</head>
<body>

{{-- ── FOOTER ── --}}
<div id="footer">
    <hr style="border:none;border-top:1px solid #000;margin-bottom:5px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="font-size:7.5pt;color:#444;">{{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}</td>
            <td style="font-size:7.5pt;color:#444;text-align:right;">Page <span class="pagenum"></span></td>
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
                <img src="{{ $logoPath }}" alt="Logo" style="max-height:44px;max-width:130px;display:block;margin-bottom:5px;">
            @endif
            <div style="font-size:13pt;font-weight:bold;line-height:1.2;">
                {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}
            </div>
            <div style="font-size:8.5pt;color:#444;margin-top:3px;">
                Professional IT Solutions &amp; Services
            </div>
        </td>
        <td width="40%" style="vertical-align:bottom;text-align:right;font-size:8.5pt;line-height:1.85;color:#333;">
            @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
            @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
            @if(!empty($settings['company_address'])){{ $settings['company_address'] }}@endif
        </td>
    </tr>
</table>

<hr style="border:none;border-top:2px solid #000;margin-bottom:3px;">
<hr style="border:none;border-top:1px solid #000;margin-bottom:16px;">

{{-- DOCUMENT TITLE --}}
<div style="text-align:center;font-size:15pt;font-weight:bold;letter-spacing:6px;margin-bottom:16px;">
    Q U O T A T I O N
</div>

<hr style="border:none;border-top:1px solid #000;margin-bottom:16px;">

{{-- BILLING + DOC DETAILS --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td width="58%" style="vertical-align:top;padding-right:24px;">
            <div style="font-size:7.5pt;text-transform:uppercase;letter-spacing:1.5px;color:#555;margin-bottom:6px;">Bill To</div>
            <div style="font-weight:bold;font-size:11pt;line-height:1.3;margin-bottom:4px;">{{ $quotation->customer_name }}</div>
            @if($quotation->customer_address)
            <div style="font-size:9pt;color:#333;margin-bottom:2px;">{{ $quotation->customer_address }}</div>
            @endif
            @if($quotation->customer_contact)
            <div style="font-size:9pt;color:#333;">{{ $quotation->customer_contact }}</div>
            @endif
        </td>
        <td width="42%" style="vertical-align:top;">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:9pt;line-height:2;">
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

<hr style="border:none;border-top:1px solid #000;margin-bottom:16px;">

{{-- INTRODUCTION --}}
<div style="font-size:9pt;line-height:1.85;margin-bottom:4px;">Dear Valued Customer,</div>
<div style="font-size:9pt;line-height:1.85;margin-bottom:6px;text-align:justify;">
    Thank you for your interest in our products and services. We are pleased to present the
    following quotation, carefully tailored to meet your business requirements. Please review
    the details below and feel free to contact us for any clarifications or further assistance.
</div>

@php $quoteType = $quotation->quote_type ?? 'full_set'; @endphp

{{-- HARDWARE PACKAGE --}}
@if($quoteType !== 'software_only' && $quotation->items->count())
<div class="sec">Hardware Package</div>
<hr class="rule">
<table class="tbl" style="margin-bottom:8px;">
    <thead>
        <tr>
            <th class="c" width="38">No.</th>
            <th>Description</th>
            <th class="c" width="52">Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotation->items as $item)
        @php
            $lines = array_values(array_filter(array_map('rtrim', explode("\n", $item->description))));
            $specs = array_slice($lines, 1);
        @endphp
        <tr>
            <td class="c" style="font-weight:bold;">{{ $item->item_number }}</td>
            <td>
                <div style="font-weight:bold;margin-bottom:3px;">{{ $lines[0] ?? $item->description }}@if($item->warranty) <span style="font-weight:normal;color:#555;">[{{ $item->warranty }}]</span>@endif</div>
                @foreach($specs as $spec)
                <div style="font-size:8.5pt;color:#333;padding-left:4px;">- {{ preg_replace('/^[\s•●\-\*]+/', '', $spec) }}</div>
                @endforeach
            </td>
            <td class="c" style="font-weight:bold;">{{ $item->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SOFTWARE FEATURES --}}
@if($quoteType !== 'hardware_only' && !empty($quotation->software_features))
<div class="sec">Software Features</div>
<hr class="rule">
<div style="margin-bottom:8px;">
    @foreach($quotation->software_features as $f)
        @php
            $kind = is_array($f) ? ($f['kind'] ?? 'item') : 'item';
            $text = is_array($f) ? ($f['text'] ?? '') : $f;
        @endphp
        @if($kind === 'space')
            <div style="height:6pt;"></div>
        @elseif($kind === 'heading')
            <div style="font-weight:bold;font-size:9pt;margin:8px 0 4px;">{{ $text }}</div>
        @else
            @php $parts = explode(' - ', $text, 2); @endphp
            <div style="font-size:9pt;padding-left:6px;margin-bottom:3px;">
                [+] <strong>{{ $parts[0] }}</strong>@if(isset($parts[1]))<span style="color:#333;"> &mdash; {{ $parts[1] }}</span>@endif
            </div>
        @endif
    @endforeach
</div>
@endif

{{-- PAYMENT BREAKDOWN --}}
<div class="sec">Payment Breakdown</div>
<hr class="rule">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="font-size:9pt;padding:6px 0;">
            @if($quoteType === 'software_only') Software Package Price
            @elseif($quoteType === 'hardware_only') Hardware Package Price
            @else Complete Package Price
            @endif
        </td>
        <td style="text-align:right;font-size:13pt;font-weight:bold;white-space:nowrap;padding:6px 0;">
            LKR {{ number_format($quotation->total_amount) }}.00
        </td>
    </tr>
</table>
<hr class="drule">

{{-- TERMS & CONDITIONS --}}
@if($quotation->terms_conditions)
<div class="sec">Terms &amp; Conditions</div>
<hr class="rule">
<div style="margin-bottom:8px;font-size:8.5pt;line-height:1.8;">
    @php
        $termsLines = array_map('rtrim', explode("\n", $quotation->terms_conditions));
        $prevEmpty  = true;
    @endphp
    @foreach($termsLines as $tLine)
        @if(trim($tLine) === '')
            <div style="height:5pt;"></div>
            @php $prevEmpty = true; @endphp
        @elseif(mb_substr(ltrim($tLine), 0, 1) === '●' || mb_substr(ltrim($tLine), 0, 1) === '•')
            <div style="padding-left:14px;margin-bottom:2px;">- {{ preg_replace('/^[\s●•]+/', '', $tLine) }}</div>
            @php $prevEmpty = false; @endphp
        @elseif($prevEmpty)
            <div style="font-weight:bold;margin-top:6px;">{{ $tLine }}</div>
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

{{-- CONTACT INFORMATION --}}
<div class="sec">Contact Information</div>
<hr class="rule">
<div style="font-size:9pt;line-height:1.9;margin-bottom:8px;color:#333;">
    {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}<br>
    {{ $settings['company_address'] ?? 'No 46, Hudson Rd, Colombo 03' }}<br>
    {{ $settings['company_phone'] ?? '+94 76 59 33 255' }}<br>
    Monday &ndash; Saturday &nbsp; 9:00 AM &ndash; 6:00 PM
</div>

<hr style="border:none;border-top:1px solid #000;margin-top:20px;margin-bottom:8px;">
<div style="text-align:center;font-size:9pt;font-weight:bold;margin-bottom:3px;">
    Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
</div>
<div style="text-align:center;font-size:8pt;color:#444;">
    This quotation is valid for 30 days from the date of issue.
</div>

</div>{{-- end #content --}}
</body>
</html>
