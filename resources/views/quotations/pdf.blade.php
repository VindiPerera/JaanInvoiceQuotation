<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #1a1a1a; line-height: 1.55; }

/* Zero top margin — the inline header's red bar sits flush at the physical top of page 1.
   12mm bottom reserves space for the fixed footer on every page.
   Left/right 0 so fixed footer can span the full physical page width.   */
@page { size: A4 portrait; margin: 0 0 12mm 0; }

/* ── FIXED FOOTER — bottom 12mm margin, repeats on every page ────────── */
#page-footer {
    position: fixed;
    bottom: -12mm; left: 0; right: 0;
    height: 12mm;
    background: #fff;
}
.pagenum:before { content: counter(page); }

/* ── SECTION HEADER BARS ─────────────────────────────────────────────── */
.sec-head {
    background: #cc1010; color: #fff;
    font-size: 7.5pt; font-weight: bold;
    text-transform: uppercase; letter-spacing: 1px;
    padding: 3px 10px; margin-bottom: 8px;
    page-break-after: avoid;
}

/* ── HARDWARE TABLE ──────────────────────────────────────────────────── */
.hw-table { width: 100%; border-collapse: collapse; }
.hw-table thead tr { background: #cc1010; color: #fff; }
.hw-table th { padding: 6px 10px; font-size: 8.5pt; font-weight: bold; text-align: left; letter-spacing: 0.5px; }
.hw-table th.c { text-align: center; }
.hw-table td { padding: 9px 10px; vertical-align: top; border: 1px solid #e0e0e0; font-size: 9.5pt; }
.hw-table td.c { text-align: center; vertical-align: middle; }
.hw-table tbody tr:nth-child(odd)  { background: #fff; }
.hw-table tbody tr:nth-child(even) { background: #f6f6f6; }
.hw-table tbody tr { page-break-inside: avoid; }

/* ── TERMS ───────────────────────────────────────────────────────────── */
.t-head   { font-size: 10pt; font-weight: bold; color: #1a1a1a; margin: 10px 0 3px; page-break-after: avoid; }
.t-sub    { font-size: 9pt;  font-weight: bold; color: #333;    margin:  6px 0 2px; page-break-after: avoid; }
.t-body   { font-size: 9pt;  color: #444;  margin-bottom: 4px; }
.t-bullet { font-size: 9pt;  color: #333;  padding-left: 10px; margin-bottom: 4px; }
</style>
</head>
<body>

{{-- ════════ FIXED FOOTER — repeats on every page ════════ --}}
<div id="page-footer">
    <div style="height:1px;background:#e0e0e0;margin:0 15mm 2px;"></div>
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:1px 15mm 0;">
        <tr>
            <td style="font-size:7pt;color:#999;">
                {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
                @if(!empty($settings['company_phone']))<span style="color:#ccc">&nbsp;&#124;&nbsp;</span>{{ $settings['company_phone'] }}@endif
            </td>
            <td style="font-size:7pt;color:#999;text-align:right;">
                Page&nbsp;<span class="pagenum"></span>
            </td>
        </tr>
    </table>
    <div style="height:5mm;background:#cc1010;margin-top:2px;"></div>
</div>

{{-- ════════ FIRST-PAGE HEADER — inline flow, appears on page 1 only ════════ --}}
@php $logoPath = !empty($settings['company_logo']) ? public_path($settings['company_logo']) : null; @endphp
<div style="margin:0;padding:0;">
    {{-- Full physical-width red top bar --}}
    <div style="height:9mm;background:#cc1010;"></div>
    {{-- Logo | Company name | Contact --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="padding:6px 15mm 5px;table-layout:fixed;">
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
                    {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}
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

{{-- ════════ PAGE CONTENT — flows naturally across all pages ════════ --}}
<div style="padding:12px 15mm 8px;">

{{-- QUOTATION TITLE --}}
<div style="text-align:center;margin-bottom:12px;">
    <div style="font-size:18pt;font-weight:bold;color:#1a1a1a;letter-spacing:5px;">QUOTATION</div>
</div>

{{-- Date / Quotation No --}}
<table width="100%" cellpadding="0" cellspacing="0"
       style="border-collapse:collapse;margin-bottom:14px;">
    <tr>
        <td style="border:1px solid #ddd;padding:6px 12px;width:50%;font-size:9.5pt;">
            <strong>Date:</strong> {{ $quotation->quotation_date->format('F d, Y') }}
        </td>
        <td style="border:1px solid #ddd;border-left:none;padding:6px 12px;width:50%;font-size:9.5pt;">
            <strong>Quotation No:</strong> {{ $quotation->quotation_number }}
        </td>
    </tr>
</table>

{{-- CUSTOMER DETAILS --}}
<div class="sec-head">Customer Details</div>
<div style="margin-bottom:12px;font-size:10pt;line-height:1.8;padding:0 2px;">
    <strong>To: {{ $quotation->customer_name }}</strong>
    @if($quotation->customer_address)
    <br><span style="font-size:9.5pt;color:#333;">{{ $quotation->customer_address }}</span>
    @endif
    @if($quotation->customer_contact)
    <br><strong>Contact: {{ $quotation->customer_contact }}</strong>
    @endif
</div>

{{-- Subject --}}
@if($quotation->subject)
<div style="font-size:10pt;font-weight:bold;margin-bottom:12px;padding:2px;">
    SUBJECT: {{ $quotation->subject }}
</div>
@endif

{{-- Introduction --}}
<div style="font-size:9.5pt;color:#333;line-height:1.85;margin-bottom:18px;
            text-align:justify;padding:0 2px;">
    Dear Valued Customer,<br><br>
    Thank you for your interest in our products and services. We are pleased to present the
    following quotation, carefully tailored to meet your business requirements. Our team has
    dedicated thorough attention to this proposal to ensure it delivers the most effective and
    reliable solution for your needs. Please review the details below and feel free to contact
    us for any clarifications or further assistance.
</div>

@php $quoteType = $quotation->quote_type ?? 'full_set'; @endphp

{{-- HARDWARE PACKAGE — shown for full_set and hardware_only --}}
@if($quoteType !== 'software_only' && $quotation->items->count())
<div class="sec-head">Hardware Package</div>
<table class="hw-table" style="margin-bottom:20px;">
    <thead>
        <tr>
            <th class="c" width="44">Item</th>
            <th>Description</th>
            <th class="c" width="50">Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotation->items as $item)
        @php
            $lines = array_values(array_filter(array_map('rtrim', explode("\n", $item->description))));
            $specs = array_slice($lines, 1);
        @endphp
        <tr>
            <td class="c" style="font-weight:bold;font-size:11pt;color:#cc1010;">
                {{ $item->item_number }}
            </td>
            <td>
                <div style="font-weight:bold;font-size:9.5pt;color:#1a1a1a;margin-bottom:4px;">
                    {{ $lines[0] ?? $item->description }}
                </div>
                @if(count($specs) > 0)
                <div style="font-size:8.5pt;color:#555;line-height:1.72;">
                    @foreach($specs as $spec)
                    <span style="color:#cc1010;">&#9679;</span>&nbsp;{{ $spec }}<br>
                    @endforeach
                </div>
                @endif
            </td>
            <td class="c" style="font-size:11pt;font-weight:bold;color:#1a1a1a;">
                {{ $item->quantity }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SOFTWARE FEATURES — shown for full_set and software_only --}}
@if($quoteType !== 'hardware_only' && !empty($quotation->software_features))
<div class="sec-head">Maria POS &#8212; Software Features</div>
<div style="margin-bottom:20px;padding:0 2px;">
    @foreach($quotation->software_features as $f)
        @php
            $kind = is_array($f) ? ($f['kind'] ?? 'item') : 'item';
            $text = is_array($f) ? ($f['text'] ?? '') : $f;
        @endphp
        @if($kind === 'space')
            <div style="height:5pt;"></div>
        @elseif($kind === 'heading')
            <div style="font-size:9.5pt;font-weight:bold;color:#1a1a1a;
                        border-bottom:1px dashed #ccc;padding-bottom:3px;margin:7px 0 6px;">
                {{ $text }}
            </div>
        @else
            @php $parts = explode(' - ', $text, 2); @endphp
            <div style="font-size:9.5pt;margin-bottom:4px;">
                <span style="color:#cc1010;font-weight:bold;font-size:10pt;">&#10003;</span>&nbsp;
                <strong>{{ $parts[0] }}</strong>@if(isset($parts[1]))<span style="color:#555;font-size:9pt;"> - {{ $parts[1] }}</span>@endif
            </div>
        @endif
    @endforeach
</div>
@endif

{{-- ADDITIONAL BENEFITS — shown for full_set and software_only --}}
@if($quoteType !== 'hardware_only' && !empty($quotation->additional_benefits))
<div class="sec-head">Additional Benefits</div>
<div style="margin-bottom:20px;padding:4px 2px;">
    @foreach($quotation->additional_benefits as $b)
        @php
            $kind = is_array($b) ? ($b['kind'] ?? 'item') : 'item';
            $text = is_array($b) ? ($b['text'] ?? '') : $b;
        @endphp
        @if($kind === 'space')
            <div style="height:5pt;"></div>
        @elseif($kind === 'heading')
            <div style="font-size:9.5pt;font-weight:bold;color:#1a1a1a;margin:6px 0 3px;">{{ $text }}</div>
        @else
            <div style="font-size:9.5pt;margin-bottom:4px;">
                <span style="color:#cc1010;font-weight:bold;font-size:10pt;">&#9679;</span>&nbsp;{{ $text }}
            </div>
        @endif
    @endforeach
</div>
@endif

{{-- PAYMENT BREAKDOWN --}}
<div class="sec-head">Payment Breakdown</div>
<table width="100%" cellpadding="0" cellspacing="0"
       style="border:1px solid #e0e0e0;margin-bottom:20px;">
    <tr style="background:#fafafa;">
        <td style="padding:12px 14px;font-size:9.5pt;font-weight:bold;color:#333;
                   border-left:3px solid #cc1010;vertical-align:middle;">
            @if($quoteType === 'software_only') SOFTWARE PACKAGE PRICE
            @elseif($quoteType === 'hardware_only') HARDWARE PACKAGE PRICE
            @else COMPLETE PACKAGE PRICE
            @endif
        </td>
        <td style="padding:12px 14px;font-size:15pt;font-weight:bold;color:#cc1010;
                   text-align:right;white-space:nowrap;vertical-align:middle;">
            LKR {{ number_format($quotation->total_amount) }}.00
        </td>
    </tr>
</table>

{{-- TERMS & CONDITIONS --}}
@if($quotation->terms_conditions)
<div class="sec-head">Terms &amp; Conditions</div>
<div style="margin-bottom:20px;">
    @php
        $termsLines = array_map('rtrim', explode("\n", $quotation->terms_conditions));
        $prevEmpty  = true;
    @endphp
    @foreach($termsLines as $tLine)
        @if(trim($tLine) === '')
            <div style="height:5pt;"></div>
            @php $prevEmpty = true; @endphp
        @elseif(mb_substr(ltrim($tLine), 0, 1) === '●' || mb_substr(ltrim($tLine), 0, 1) === '•')
            <div class="t-bullet">
                <span style="color:#cc1010;font-weight:bold;">&#9679;</span>&nbsp;{{ preg_replace('/^[\s●•]+/', '', $tLine) }}
            </div>
            @php $prevEmpty = false; @endphp
        @elseif($prevEmpty)
            <div class="t-head">{{ $tLine }}</div>
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

{{-- CONTACT INFORMATION --}}
<div class="sec-head">Contact Information</div>
<div style="font-size:9.5pt;line-height:1.9;margin-bottom:16px;padding:0 2px;">
    <strong>{{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}</strong><br>
    <strong>Address:</strong> {{ $settings['company_address'] ?? 'No 46, Hudson Rd, Colombo 03' }}<br>
    <strong>Phone:</strong> {{ $settings['company_phone'] ?? '+94 76 59 33 255' }}<br>
    <strong>Business Hours:</strong> Monday &#8211; Saturday: 9:00 AM &#8211; 6:00 PM
</div>

{{-- Closing --}}
<div style="text-align:center;margin-top:18px;padding-top:12px;border-top:1px solid #e0e0e0;">
    <div style="font-size:10pt;font-weight:bold;color:#cc1010;letter-spacing:0.5px;">
        Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
    </div>
    <div style="font-size:8pt;color:#888;margin-top:4px;">
        For any inquiries or clarifications, please don&#8217;t hesitate to contact us.
    </div>
</div>

</div>{{-- end content --}}
</body>
</html>
