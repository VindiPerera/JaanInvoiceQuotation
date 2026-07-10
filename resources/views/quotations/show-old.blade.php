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
            font-family:Arial,sans-serif;font-size:10pt;color:#1a1a1a;line-height:1.5;">
<style>
.qpv-tbl  { width:100%;border-collapse:collapse; }
.qpv-tbl thead tr { background:#b91c1c;color:#fff; }
.qpv-tbl th  { padding:8px;font-size:8pt;font-weight:bold;text-align:left;text-transform:uppercase;letter-spacing:0.5px;border:none; }
.qpv-tbl th.r { text-align:right; }
.qpv-tbl th.c { text-align:center; }
.qpv-tbl td  { padding:7px 8px;border-bottom:1px dashed #fecaca;font-size:9pt;vertical-align:top; }
.qpv-tbl td.r { text-align:right; }
.qpv-tbl td.c { text-align:center; }
.qpv-tbl tbody tr:nth-child(odd)  { background:#fff; }
.qpv-tbl tbody tr:nth-child(even) { background:#fef2f2; }
.qpv-tbl tbody tr:last-child td { border-bottom:1px solid #111; }
.qpv-th  { font-size:9pt;font-weight:bold;color:#1a1a1a;margin:7px 0 2px; }
.qpv-ts  { font-size:8.5pt;font-weight:bold;color:#333;margin:5px 0 2px; }
.qpv-tb  { font-size:8.5pt;color:#444;margin-bottom:3px; }
.qpv-tbu { font-size:8.5pt;color:#333;padding-left:10px;margin-bottom:3px; }
.qpv-doc-title { text-align:center;font-size:16pt;font-weight:bold;letter-spacing:5px;margin:8px 0 12px;padding:8px 0;border-top:2px solid #b91c1c;border-bottom:1px solid #b91c1c;border-left:1px solid #fecaca;border-right:1px solid #fecaca;background:#fef2f2;color:#b91c1c; }
.qpv-section { font-weight:bold;font-size:8.2pt;text-transform:uppercase;letter-spacing:1.6px;margin:16px 0 4px;color:#b91c1c;display:inline-block;padding:4px 10px 4px 8px;border-left:3px solid #b91c1c;background:#fef2f2;border-radius:2px; }
.qpv-rule { border:none;border-top:1px solid #b91c1c;margin:0 0 8px; }
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
<div class="qpv-doc-title">Q U O T A T I O N</div>

{{-- BILLING + DOC DETAILS --}}
<div style="padding:0 20px 20px;">
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
                    <td style="font-weight:bold;width:48%;">Quotation No</td>
                    <td style="text-align:right;">:&nbsp;{{ $quotation->quotation_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Date</td>
                    <td style="text-align:right;">:&nbsp;{{ $quotation->quotation_date->format('d M Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ITEMS --}}
@php $visibleItems = $quotation->items->where('is_hidden', false); @endphp
@if($visibleItems->count() > 0)
<div class="qpv-section">SOFTWARE/HARDWARE/SERVICES</div>
<hr class="qpv-rule">
<table class="qpv-tbl" style="margin-bottom:16px;">
    <thead>
        <tr>
            <th style="width:36px;">No.</th>
            <th>Description</th>
            <th class="c" style="width:52px;">Qty</th>
            <th class="r" style="width:96px;">Unit Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($visibleItems as $item)
        <tr>
            <td class="c" style="font-weight:bold;">{{ $item->item_number }}</td>
            <td>{{ $item->item_name ?? $item->description }}</td>
            <td class="c">{{ number_format((float)$item->quantity, 0) }}</td>
            <td class="r">{{ number_format((float)$item->unit_price) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SOFTWARE FEATURES --}}
@if(!empty($quotation->software_features))
<div class="qpv-section">Software Features</div>
<hr class="qpv-rule">
<div style="margin-bottom:16px;padding:0 8px;font-size:9pt;line-height:1.8;">
    @foreach($quotation->software_features as $f)
        @php $kind = is_array($f) ? ($f['kind'] ?? 'item') : 'item'; $text = is_array($f) ? ($f['text'] ?? '') : $f; @endphp
        @if($kind === 'space')<div style="height:6pt;"></div>
        @elseif($kind === 'heading')
            <div style="font-size:9pt;font-weight:bold;color:#1a1a1a;border-bottom:1px dashed #fecaca;padding-bottom:3px;margin:7px 0 6px;">{{ $text }}</div>
        @else
            @php $parts = explode(' - ', $text, 2); @endphp
            <div style="margin-bottom:4px;">
                <span style="color:#b91c1c;font-weight:bold;">✓</span>&nbsp;<strong>{{ $parts[0] }}</strong>@if(isset($parts[1]))<span style="color:#555;"> - {{ $parts[1] }}</span>@endif
            </div>
        @endif
    @endforeach
</div>
@endif

{{-- TOTALS --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td></td>
        <td style="padding:8px 8px;font-size:9.5pt;font-weight:bold;
                   border-top:1px solid #111111;border-bottom:3px double #111111;">TOTAL (LKR)</td>
        <td style="padding:8px 8px;text-align:right;font-size:13pt;font-weight:bold;
                   white-space:nowrap;border-top:1px solid #111111;border-bottom:3px double #111111;background:#b91c1c;color:#fff;">
            {{ number_format((float)$quotation->total_amount) }}
        </td>
    </tr>
</table>

{{-- CLOSING --}}
<hr style="border:none;border-top:1px solid #b91c1c;margin-top:18px;margin-bottom:8px;">
<div style="text-align:center;font-size:9pt;font-weight:bold;margin-bottom:3px;color:#111111;">
    Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}
</div>
<div style="text-align:center;font-size:8pt;color:#b91c1c;">
    For inquiries: {{ $settings['company_phone'] ?? '' }}@if(!empty($settings['company_email'])) &nbsp;/&nbsp; {{ $settings['company_email'] }}@endif
</div>

</div>{{-- end body --}}
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
