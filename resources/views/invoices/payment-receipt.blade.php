<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            color: #1f2937;
            line-height: 1.6;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            padding: 40px;
            background: white;
        }

        .header {
            margin-bottom: 40px;
            border-bottom: 2px solid #0ea5e9;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #0ea5e9;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            color: #6b7280;
            font-size: 14px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .section-title {
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .info-block {
            margin-bottom: 20px;
        }

        .info-block p {
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-block strong {
            color: #1f2937;
        }

        .summary-card {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-row.total {
            border-top: 2px solid #d1d5db;
            padding-top: 12px;
            font-weight: 700;
            font-size: 16px;
            color: #0ea5e9;
        }

        .payment-table {
            width: 100%;
            margin-bottom: 40px;
            border-collapse: collapse;
        }

        .payment-table thead tr {
            background: #f3f4f6;
            border-bottom: 2px solid #d1d5db;
        }

        .payment-table th {
            padding: 12px;
            text-align: left;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            color: #4b5563;
            letter-spacing: 0.5px;
        }

        .payment-table td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .payment-table tbody tr:last-child td {
            border-bottom: 2px solid #d1d5db;
        }

        .amount {
            text-align: right;
            font-weight: 600;
            color: #059669;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d1fae5;
            color: #047857;
        }

        .status-partial {
            background: #fef3c7;
            color: #b45309;
        }

        .status-pending {
            background: #fee2e2;
            color: #dc2626;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }

        .badge-section {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        .payment-method-icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-right: 8px;
            font-size: 16px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>Payment Receipt</h1>
            <p>Official receipt for payment against invoice</p>
        </div>

        {{-- Main Info --}}
        <div class="grid-2">
            <div>
                <div class="section-title">Receipt Details</div>
                <div class="info-block">
                    <p><strong>Invoice Number:</strong></p>
                    <p style="font-size: 16px; font-weight: 600; color: #0ea5e9;">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="info-block">
                    <p><strong>Invoice Date:</strong></p>
                    <p>{{ $invoice->invoice_date->format('d F Y') }}</p>
                </div>
                <div class="info-block">
                    <p><strong>Payment Status:</strong></p>
                    <p>
                        @if($invoice->payment_status === 'paid')
                            <span class="status-badge status-paid">Fully Paid</span>
                        @elseif($invoice->payment_status === 'partial')
                            <span class="status-badge status-partial">Partially Paid</span>
                        @else
                            <span class="status-badge status-pending">Pending</span>
                        @endif
                    </p>
                </div>
            </div>

            <div>
                <div class="section-title">Bill To</div>
                <div class="info-block">
                    <p><strong>{{ $invoice->customer_name }}</strong></p>
                    @if($invoice->customer_address)
                        <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">{{ $invoice->customer_address }}</p>
                    @endif
                    @if($invoice->customer_contact)
                        <p style="font-size: 12px; color: #6b7280;">{{ $invoice->customer_contact }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="summary-card">
            <div class="summary-row">
                <span>Invoice Amount:</span>
                <strong>LKR {{ number_format($invoice->total_amount, 2) }}</strong>
            </div>
            <div class="summary-row">
                <span>Total Paid:</span>
                <strong style="color: #059669;">LKR {{ number_format($invoice->paid_amount, 2) }}</strong>
            </div>
            <div class="summary-row">
                <span>Remaining Balance:</span>
                <strong style="color: #b45309;">LKR {{ number_format($invoice->balance, 2) }}</strong>
            </div>
            <div class="summary-row total">
                <span>Collection Status:</span>
                <strong>{{ number_format((float)$invoice->paid_amount / (float)$invoice->total_amount * 100, 1) }}% Collected</strong>
            </div>
        </div>

        {{-- Payment History Table --}}
        @if($invoice->payments->count())
        <div style="margin-bottom: 30px;">
            <div class="section-title">Payment Transactions</div>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Notes</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>
                            <span>
                                @switch($payment->payment_method)
                                    @case('cash')
                                        💵 Cash
                                    @break
                                    @case('bank_transfer')
                                        🏦 Bank Transfer
                                    @break
                                    @case('card')
                                        💳 Card
                                    @break
                                    @case('cheque')
                                        📋 Cheque
                                    @break
                                    @case('online')
                                        🌐 Online
                                    @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                @endswitch
                            </span>
                        </td>
                        <td>{{ $payment->reference_number ?: '—' }}</td>
                        <td style="font-size: 12px; color: #6b7280;">{{ Str::limit($payment->notes, 30) ?: '—' }}</td>
                        <td class="amount">LKR {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Total Row --}}
        <div class="summary-card">
            <div class="summary-row total">
                <span>Total Payments Received:</span>
                <strong>LKR {{ number_format($invoice->paid_amount, 2) }}</strong>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p><strong>This is a computer-generated receipt and does not require a signature.</strong></p>
            <p>For any queries regarding this invoice, please contact us with the invoice number.</p>
            <p style="margin-top: 10px; color: #9ca3af;">Generated on {{ now()->format('d F Y \a\t H:i A') }}</p>
        </div>
    </div>
</body>
</html>
