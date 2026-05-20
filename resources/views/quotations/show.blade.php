@extends('layouts.app')
@section('title', $quotation->quotation_number)
@section('breadcrumb', 'Quotations / ' . $quotation->quotation_number)

@section('header-actions')
    <a href="{{ route('quotations.pdf', $quotation) }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
    <a href="{{ route('quotations.edit', $quotation) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-pencil"></i> Edit
    </a>
    <a href="{{ route('quotations.convert', $quotation) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-right-arrow-left"></i> Convert to Invoice
    </a>
@endsection

@section('content')
<div style="max-width:860px;">

{{-- Paper preview --}}
<div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;
            box-shadow:0 2px 8px rgba(0,0,0,0.07);margin-bottom:20px;
            font-family:Arial,sans-serif;font-size:10pt;color:#1a1a1a;line-height:1.55;">
<style>
.qpv-sec  { background:#cc1010;color:#fff;font-size:7.5pt;font-weight:bold;
             text-transform:uppercase;letter-spacing:1px;padding:3px 10px;margin-bottom:8px; }
.qpv-hw   { width:100%;border-collapse:collapse; }
.qpv-hw thead tr { background:#cc1010;color:#fff; }
.qpv-hw th   { padding:6px 10px;font-size:8.5pt;font-weight:bold;text-align:left;letter-spacing:.5px; }
.qpv-hw th.c { text-align:center; }
.qpv-hw td   { padding:9px 10px;vertical-align:top;border:1px solid #e0e0e0;font-size:9.5pt; }
.qpv-hw td.c { text-align:center;vertical-align:middle; }
.qpv-hw tbody tr:nth-child(odd)  { background:#fff; }
.qpv-hw tbody tr:nth-child(even) { background:#f6f6f6; }
.qpv-th { font-size:10pt;font-weight:bold;color:#1a1a1a;margin:10px 0 3px; }
.qpv-ts { font-size:9pt;font-weight:bold;color:#333;margin:6px 0 2px; }
.qpv-tb { font-size:9pt;color:#444;margin-bottom:4px; }
.qpv-tbu{ font-size:9pt;color:#333;padding-left:10px;margin-bottom:4px; }
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
                {{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}
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

    {{-- Title --}}
    <div style="text-align:center;margin-bottom:12px;">
        <div style="font-size:18pt;font-weight:bold;color:#1a1a1a;letter-spacing:5px;">QUOTATION</div>
    </div>

    {{-- Date / Number --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:14px;">
        <tr>
            <td style="border:1px solid #ddd;padding:6px 12px;width:50%;font-size:9.5pt;">
                <strong>Date:</strong> {{ $quotation->quotation_date->format('F d, Y') }}
            </td>
            <td style="border:1px solid #ddd;border-left:none;padding:6px 12px;width:50%;font-size:9.5pt;">
                <strong>Quotation No:</strong> {{ $quotation->quotation_number }}
            </td>
        </tr>
    </table>

    {{-- Customer --}}
    <div class="qpv-sec">Customer Details</div>
    <div style="margin-bottom:12px;font-size:10pt;line-height:1.8;padding:0 2px;">
        <strong>To: {{ $quotation->customer_name }}</strong>
        @if($quotation->customer_address)<br><span style="font-size:9.5pt;color:#333;">{{ $quotation->customer_address }}</span>@endif
        @if($quotation->customer_contact)<br><strong>Contact: {{ $quotation->customer_contact }}</strong>@endif
    </div>

    @if($quotation->subject)
    <div style="font-size:10pt;font-weight:bold;margin-bottom:12px;padding:2px;">SUBJECT: {{ $quotation->subject }}</div>
    @endif

    {{-- Intro --}}
    <div style="font-size:9.5pt;color:#333;line-height:1.85;margin-bottom:18px;text-align:justify;padding:0 2px;">
        Dear Valued Customer,<br><br>
        Thank you for your interest in our products and services. We are pleased to present the
        following quotation, carefully tailored to meet your business requirements. Our team has
        dedicated thorough attention to this proposal to ensure it delivers the most effective and
        reliable solution for your needs. Please review the details below and feel free to contact
        us for any clarifications or further assistance.
    </div>

    @php $quoteType = $quotation->quote_type ?? 'full_set'; @endphp

    {{-- Hardware Package --}}
    @if($quoteType !== 'software_only' && $quotation->items->count())
    <div class="qpv-sec">Hardware/Services</div>
    <table class="qpv-hw" style="margin-bottom:20px;">
        <thead>
            <tr style="background:#cc1010;color:#fff;">
                <th class="c" width="44" style="padding:8px 10px;font-size:8.5pt;font-weight:bold;text-align:center;letter-spacing:.5px;">Item</th>
                <th style="padding:8px 10px;font-size:8.5pt;font-weight:bold;text-align:left;letter-spacing:.5px;">Description</th>
                <th class="c" width="50" style="padding:8px 10px;font-size:8.5pt;font-weight:bold;text-align:center;letter-spacing:.5px;">Qty</th>
                <th class="c" width="70" style="padding:8px 10px;font-size:8.5pt;font-weight:bold;text-align:right;letter-spacing:.5px;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            @php
                $lines = array_values(array_filter(array_map('rtrim', explode("\n", $item->description))));
                $specs = array_slice($lines, 1);
            @endphp
            <tr style="border:1px solid #e0e0e0;">
                <td class="c" style="padding:10px;font-weight:bold;font-size:11pt;color:#cc1010;text-align:center;vertical-align:top;">{{ $item->item_number }}</td>
                <td style="padding:10px;vertical-align:top;">
                    <div style="font-weight:bold;font-size:9.5pt;color:#1a1a1a;margin-bottom:4px;">{{ $lines[0] ?? $item->description }}</div>
                    @if(count($specs))
                    <div style="font-size:8.5pt;color:#555;line-height:1.6;">
                        @foreach($specs as $spec)<span style="color:#cc1010;">&#9679;</span>&nbsp;{{ $spec }}<br>@endforeach
                    </div>
                    @endif
                </td>
                <td class="c" style="padding:10px;font-size:11pt;font-weight:bold;color:#1a1a1a;text-align:center;vertical-align:middle;">{{ $item->quantity }}</td>
                <td class="c" style="padding:10px;font-weight:bold;font-size:9.5pt;color:#1a1a1a;text-align:right;vertical-align:middle;">LKR<br>{{ number_format($item->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Software Features --}}
    @if($quoteType !== 'hardware_only' && !empty($quotation->software_features))
    <div class="qpv-sec">Software Features</div>
    <div style="margin-bottom:20px;padding:0 2px;">
        @foreach($quotation->software_features as $f)
            @php $kind = is_array($f) ? ($f['kind'] ?? 'item') : 'item'; $text = is_array($f) ? ($f['text'] ?? '') : $f; @endphp
            @if($kind === 'space')<div style="height:5pt;"></div>
            @elseif($kind === 'heading')
                <div style="font-size:9.5pt;font-weight:bold;color:#1a1a1a;border-bottom:1px dashed #ccc;padding-bottom:3px;margin:7px 0 6px;">{{ $text }}</div>
            @else
                @php $parts = explode(' - ', $text, 2); @endphp
                <div style="font-size:9.5pt;margin-bottom:4px;">
                    <span style="color:#cc1010;font-weight:bold;font-size:10pt;">&#10003;</span>&nbsp;<strong>{{ $parts[0] }}</strong>@if(isset($parts[1]))<span style="color:#555;font-size:9pt;"> - {{ $parts[1] }}</span>@endif
                </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Payment Breakdown --}}
    <div class="qpv-sec">Payment Breakdown</div>
    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e0e0e0;margin-bottom:20px;">
        <tr style="background:#fafafa;">
            <td style="padding:12px 14px;font-size:9.5pt;font-weight:bold;color:#333;border-left:3px solid #cc1010;vertical-align:middle;">
                @if($quoteType === 'software_only') SOFTWARE PACKAGE PRICE
                @elseif($quoteType === 'hardware_only') HARDWARE PACKAGE PRICE
                @else Total
                @endif
            </td>
            <td style="padding:12px 14px;font-size:15pt;font-weight:bold;color:#cc1010;text-align:right;white-space:nowrap;vertical-align:middle;">
                LKR {{ number_format($quotation->total_amount) }}.00
            </td>
        </tr>
    </table>

    {{-- Terms & Conditions --}}
    @if($quotation->terms_conditions)
    <div class="qpv-sec">Terms &amp; Conditions</div>
    <div style="margin-bottom:20px;">
        @php $termsLines = array_map('rtrim', explode("\n", $quotation->terms_conditions)); $prevEmpty = true; @endphp
        @foreach($termsLines as $tLine)
            @if(trim($tLine) === '')<div style="height:5pt;"></div>@php $prevEmpty = true; @endphp
            @elseif(mb_substr(ltrim($tLine), 0, 1) === '●' || mb_substr(ltrim($tLine), 0, 1) === '•')
                <div class="qpv-tbu"><span style="color:#cc1010;font-weight:bold;">&#9679;</span>&nbsp;{{ preg_replace('/^[\s●•]+/', '', $tLine) }}</div>@php $prevEmpty = false; @endphp
            @elseif($prevEmpty)<div class="qpv-th">{{ $tLine }}</div>@php $prevEmpty = false; @endphp
            @elseif(mb_strlen(trim($tLine)) < 60 && mb_substr(trim($tLine), -1) === ':')
                <div class="qpv-ts">{{ $tLine }}</div>@php $prevEmpty = false; @endphp
            @else<div class="qpv-tb">{{ $tLine }}</div>@php $prevEmpty = false; @endphp
            @endif
        @endforeach
    </div>
    @endif

    {{-- Contact Information --}}
    <div class="qpv-sec">Contact Information</div>
    <div style="font-size:9.5pt;line-height:1.9;margin-bottom:16px;padding:0 2px;">
        <strong>{{ $settings['company_name'] ?? 'JAAN NETWORK PVT. LTD.' }}</strong><br>
        <strong>Address:</strong> {{ $settings['company_address'] ?? 'No 46, Hudson Rd, Colombo 03' }}<br>
        <strong>Phone:</strong> {{ $settings['company_phone'] ?? '+94 76 59 33 255' }}<br>
        <strong>Business Hours:</strong> Monday &#8211; Saturday: 9:00 AM &#8211; 6:00 PM
    </div>

    {{-- Closing --}}
    <div style="text-align:center;margin-top:18px;padding-top:12px;border-top:1px solid #e0e0e0;">
        <div style="font-size:10pt;font-weight:bold;color:#cc1010;letter-spacing:.5px;">
            Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
        </div>
        <div style="font-size:8pt;color:#888;margin-top:4px;">
            For any inquiries or clarifications, please don&#8217;t hesitate to contact us.
        </div>
    </div>

</div>{{-- end body --}}
<div style="height:5mm;background:#cc1010;margin-top:10px;"></div>
</div>{{-- end paper --}}

<div class="flex items-center gap-3 mt-2">
    <a href="{{ route('quotations.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-arrow-left mr-1"></i> Back to list
    </a>
    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirm('Delete this quotation?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-500 hover:text-red-700">
            <i class="fa-solid fa-trash mr-1"></i> Delete
        </button>
    </form>
</div>
</div>
@endsection
