<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #1a1a1a; }
.header-stripe { background: #e31c23; height: 6px; width: 100%; }
.header-black  { background: #1a1a1a; height: 3px; width: 100%; margin-top: 2px; }
.page-header { padding: 14px 20px 10px; display: flex; justify-content: space-between; align-items: flex-start; }
.logo-block { display: flex; align-items: center; gap: 8px; }
.logo-box { width: 36px; height: 36px; background: #e31c23; display: flex; align-items: center; justify-content: center; }
.logo-box span { color: #fff; font-weight: 900; font-size: 11pt; }
.company-name-big { font-size: 16pt; font-weight: 900; color: #e31c23; letter-spacing: 2px; }
.company-info { font-size: 8pt; color: #555; margin-top: 4px; }
.inv-num-box { background: #1a1a1a; color: #fff; padding: 4px 12px; font-size: 9pt; font-weight: bold; }
.title { font-size: 22pt; font-weight: 900; color: #1a1a1a; margin-top: 6px; }
.meta-table { width: 100%; padding: 10px 20px; }
.meta-table td { padding: 2px 0; font-size: 9pt; }
.section { padding: 8px 20px; }
.section-title { font-size: 10pt; font-weight: bold; color: #e31c23; text-transform: uppercase; border-bottom: 2px solid #e31c23; padding-bottom: 2px; margin-bottom: 6px; }
.items-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
.items-table thead tr { background: #e31c23; color: #fff; }
.items-table th { padding: 6px 8px; text-align: left; font-weight: bold; }
.items-table th.right, .items-table td.right { text-align: right; }
.items-table tbody tr:nth-child(even) { background: #f8f8f8; }
.items-table td { padding: 5px 8px; border-bottom: 1px solid #eee; }
.total-row td { background: #f3f4f6; font-weight: bold; font-size: 11pt; padding: 8px; }
.grand-total { color: #e31c23; font-size: 13pt; font-weight: 900; }
.features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; font-size: 9pt; }
.feature-item { display: flex; gap: 6px; align-items: flex-start; }
.check { color: #e31c23; font-weight: bold; }
.payment-box { border: 2px solid #1a1a1a; padding: 10px 14px; font-size: 9pt; margin-top: 8px; }
.payment-box strong { font-size: 10pt; }
.terms pre { font-size: 8.5pt; white-space: pre-wrap; font-family: Arial; color: #444; line-height: 1.5; }
.footer { margin-top: 16px; background: #1a1a1a; color: #fff; text-align: center; padding: 8px; font-size: 8pt; }
.footer-red { background: #e31c23; height: 5px; }
</style>
</head>
<body>

<div class="header-stripe"></div>
<div class="header-black"></div>

<div class="page-header">
    <div>
        <div class="logo-block">
            <div class="logo-box"><span>JN</span></div>
            <div>
                <div class="company-name-big">JAAN<br><span style="font-size:13pt">Network</span></div>
            </div>
        </div>
        <div class="company-info">
            {{ $settings['company_address'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_website'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_phone'] ?? '' }}
        </div>
    </div>
    <div style="text-align:right">
        <div class="inv-num-box">Quotation No: {{ $quotation->quotation_number }}</div>
        <div class="title">QUOTATION</div>
    </div>
</div>

<table class="meta-table">
    <tr>
        <td width="50%">
            <strong>Prepared For</strong><br>
            <strong style="font-size:11pt">{{ $quotation->customer_name }}</strong><br>
            {{ $quotation->customer_address }}<br>
            {{ $quotation->customer_contact }}
        </td>
        <td width="50%" style="text-align:right">
            <strong>Prepared Date</strong><br>
            {{ $quotation->quotation_date->format('d/m/Y') }}<br>
            @if($quotation->subject)<em>{{ $quotation->subject }}</em>@endif
        </td>
    </tr>
</table>

@if($quotation->items->count())
<div class="section">
    <div class="section-title">Hardware Package</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="40">Item</th>
                <th>Description</th>
                <th width="50" class="right">Qty</th>
                <th width="80" class="right">Price</th>
                <th width="90" class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td>{{ $item->item_number }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price) }}</td>
                <td class="right">{{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($quotation->tax_amount > 0)
            <tr><td colspan="4" style="text-align:right;padding:4px 8px">Subtotal</td><td class="right">{{ number_format($quotation->subtotal) }}</td></tr>
            <tr><td colspan="4" style="text-align:right;padding:4px 8px">Tax</td><td class="right">{{ number_format($quotation->tax_amount) }}</td></tr>
            @endif
            <tr class="total-row">
                <td colspan="4" style="text-align:right">LKR TOTAL</td>
                <td class="right grand-total">{{ number_format($quotation->total_amount) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

@if(!empty($quotation->software_features))
<div class="section">
    <div class="section-title">Software Features</div>
    <div class="features-grid">
        @foreach($quotation->software_features as $f)
        <div class="feature-item"><span class="check">&#10003;</span> {{ $f }}</div>
        @endforeach
    </div>
</div>
@endif

@if(!empty($quotation->additional_benefits))
<div class="section">
    <div class="section-title">Additional Benefits</div>
    @foreach($quotation->additional_benefits as $b)
    <div style="font-size:9pt;margin-bottom:2px">&#8226; {{ $b }}</div>
    @endforeach
</div>
@endif

@if($quotation->terms_conditions)
<div class="section">
    <div class="section-title">Terms & Conditions</div>
    <div class="terms"><pre>{{ $quotation->terms_conditions }}</pre></div>
</div>
@endif

<div class="section">
    <div class="section-title">Payment Details</div>
    <div class="payment-box">
        <strong>PAYMENT DETAILS:</strong><br>
        Bank Name: <strong>{{ $settings['bank_name'] ?? 'DFCC Bank' }}</strong><br>
        Branch: <strong>{{ $settings['bank_branch'] ?? 'Gampaha' }}</strong><br>
        Account Name: <strong>{{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</strong><br>
        Account Number: <strong>{{ $settings['bank_account_number'] ?? '102003031923' }}</strong>
    </div>
</div>

<div class="footer-red"></div>
<div class="footer">Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }} &mdash; Your Trusted Partner in IT Solutions</div>

</body>
</html>
