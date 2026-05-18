<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #1a1a1a; }
.header-stripe { background: #e31c23; height: 6px; }
.header-black  { background: #1a1a1a; height: 3px; margin-top: 2px; }
.page-header { padding: 14px 20px 10px; }
.top-row { display: flex; justify-content: space-between; align-items: flex-start; }
.logo-box { width: 36px; height: 36px; background: #e31c23; display: inline-flex; align-items: center; justify-content: center; }
.logo-box span { color: #fff; font-weight: 900; font-size: 11pt; }
.company-name { font-size: 15pt; font-weight: 900; color: #e31c23; }
.company-info { font-size: 8pt; color: #666; margin-top: 3px; }
.inv-box { background: #1a1a1a; color: #fff; padding: 3px 12px; font-size: 9pt; font-weight: bold; margin-bottom: 4px; display: inline-block; }
.doc-title { font-size: 20pt; font-weight: 900; }
.meta { width: 100%; padding: 8px 20px; font-size: 9pt; }
.section { padding: 6px 20px; }
.items-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
.items-table thead tr { background: #e31c23; color: #fff; }
.items-table th { padding: 6px 8px; text-align: left; }
.items-table th.r, .items-table td.r { text-align: right; }
.items-table tbody tr:nth-child(even) { background: #f8f8f8; }
.items-table td { padding: 5px 8px; border-bottom: 1px solid #eee; }
.total-row { background: #f3f4f6; font-weight: bold; }
.grand-total { color: #e31c23; font-size: 12pt; font-weight: 900; }
.payment-box { border: 2px solid #1a1a1a; padding: 10px 14px; font-size: 9pt; margin: 10px 20px; display: inline-block; }
.terms-text { font-size: 8pt; white-space: pre-wrap; color: #555; line-height: 1.5; }
.footer-black { background: #1a1a1a; color: #fff; text-align: center; padding: 6px; font-size: 8pt; }
.footer-red { background: #e31c23; height: 5px; }
</style>
</head>
<body>

<div class="header-stripe"></div>
<div class="header-black"></div>

<div class="page-header">
    <div class="top-row">
        <div>
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo-box"><span>JN</span></div>
                <div class="company-name">JAAN<br><span style="font-size:12pt">Network</span></div>
            </div>
            <div class="company-info">
                {{ $settings['company_address'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_website'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_phone'] ?? '' }}
            </div>
        </div>
        <div style="text-align:right">
            <div class="inv-box">Invoice No: {{ $invoice->invoice_number }}</div>
            <div class="doc-title">SALES INVOICE</div>
        </div>
    </div>
</div>

<table class="meta">
    <tr>
        <td width="55%">
            <strong>Prepared For</strong><br>
            <strong style="font-size:11pt">{{ $invoice->customer_name }}</strong><br>
            {{ $invoice->customer_address }}
        </td>
        <td width="45%" style="text-align:right;vertical-align:top">
            <strong>Prepared Date</strong><br>
            {{ $invoice->invoice_date->format('d/m/Y') }}
        </td>
    </tr>
</table>

<div class="section">
    <table class="items-table">
        <thead>
            <tr>
                <th width="40">Item</th>
                <th>Description</th>
                <th width="50" class="r">Qty</th>
                <th width="90" class="r">Price (LKR)</th>
                <th width="90" class="r">Total (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->item_number }}</td>
                <td>{{ $item->description }}</td>
                <td class="r">{{ $item->quantity }}</td>
                <td class="r">{{ number_format($item->unit_price) }}</td>
                <td class="r">{{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($invoice->tax_amount > 0)
            <tr><td colspan="4" style="text-align:right;padding:4px 8px">Subtotal</td><td class="r">{{ number_format($invoice->subtotal) }}</td></tr>
            <tr><td colspan="4" style="text-align:right;padding:4px 8px">Tax</td><td class="r">{{ number_format($invoice->tax_amount) }}</td></tr>
            @endif
            <tr class="total-row">
                <td colspan="4" style="text-align:right;padding:8px">LKR TOTAL</td>
                <td class="r grand-total" style="padding:8px">{{ number_format($invoice->total_amount) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="payment-box">
    <strong>PAYMENT DETAILS:</strong><br>
    Bank Name: <strong>{{ $settings['bank_name'] ?? 'DFCC Bank' }}</strong><br>
    Branch: <strong>{{ $settings['bank_branch'] ?? 'Gampaha' }}</strong><br>
    Account Name: <strong>{{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</strong><br>
    Account Number: <strong>{{ $settings['bank_account_number'] ?? '102003031923' }}</strong>
</div>

@if($invoice->terms_conditions)
<div class="section">
    <strong style="font-size:9pt">Terms & Conditions:</strong><br>
    <div class="terms-text">{{ $invoice->terms_conditions }}</div>
</div>
@endif

<div style="margin-top:20px">
    <div class="footer-red"></div>
    <div class="footer-black">Thank you for choosing {{ $settings['company_name'] ?? 'JAAN Network (Pvt) Ltd' }}</div>
</div>

</body>
</html>
