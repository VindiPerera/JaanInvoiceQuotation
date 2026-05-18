@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number)

@section('header-actions')
    <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
    <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-pencil"></i> Edit
    </a>
    @if($invoice->payment_status !== 'paid')
    <button onclick="document.getElementById('paymentModal').classList.remove('hidden')"
        class="inline-flex items-center gap-2 bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="fa-solid fa-money-bill"></i> Record Payment
    </button>
    @endif
@endsection

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Invoice preview --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="bg-red-600 h-2"></div>
        <div class="p-8">
            <div class="flex items-start justify-between mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 bg-red-600 rounded flex items-center justify-center">
                            <span class="text-white font-black text-sm">JN</span>
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-lg leading-none">JAAN</p>
                            <p class="font-bold text-gray-900 text-sm leading-none">Network</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $settings['company_address'] ?? '' }}</p>
                    <p class="text-xs text-gray-500">{{ $settings['company_website'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_phone'] ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-gray-900 text-white text-xs px-3 py-1 rounded mb-2 inline-block">
                        Invoice No: {{ $invoice->invoice_number }}
                    </div>
                    <p class="text-4xl font-black text-gray-900">SALES INVOICE</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Prepared For</p>
                    <p class="font-bold text-gray-900">{{ $invoice->customer_name }}</p>
                    @if($invoice->customer_address)<p class="text-sm text-gray-600">{{ $invoice->customer_address }}</p>@endif
                    @if($invoice->customer_contact)<p class="text-sm text-gray-600">{{ $invoice->customer_contact }}</p>@endif
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                    <div class="mt-2">
                        <span @class([
                            'text-xs px-3 py-1 rounded-full font-medium',
                            'bg-green-100 text-green-700'  => $invoice->payment_status === 'paid',
                            'bg-orange-100 text-orange-700'=> $invoice->payment_status === 'partial',
                            'bg-red-100 text-red-700'      => $invoice->payment_status === 'pending',
                        ])>{{ ucfirst($invoice->payment_status) }}</span>
                    </div>
                </div>
            </div>

            {{-- Items table --}}
            <table class="w-full text-sm border-collapse mb-6">
                <thead>
                    <tr class="bg-red-600 text-white">
                        <th class="px-4 py-2 text-left font-semibold">Item</th>
                        <th class="px-4 py-2 text-left font-semibold">Description</th>
                        <th class="px-4 py-2 text-center font-semibold w-16">Qty</th>
                        <th class="px-4 py-2 text-right font-semibold w-28">Price</th>
                        <th class="px-4 py-2 text-right font-semibold w-28">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-2 text-gray-600">{{ $item->item_number }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $item->description }}</td>
                        <td class="px-4 py-2 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right text-gray-600">{{ number_format($item->unit_price) }}</td>
                        <td class="px-4 py-2 text-right font-medium">{{ number_format($item->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if($invoice->tax_amount > 0)
                    <tr class="border-t border-gray-200">
                        <td colspan="4" class="px-4 py-2 text-right text-gray-600 font-medium">Subtotal</td>
                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($invoice->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right text-gray-600 font-medium">Tax</td>
                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($invoice->tax_amount) }}</td>
                    </tr>
                    @endif
                    <tr class="bg-gray-100">
                        <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-900">LKR TOTAL</td>
                        <td class="px-4 py-3 text-right font-black text-red-600 text-base">{{ number_format($invoice->total_amount) }}</td>
                    </tr>
                    @if($invoice->paid_amount > 0)
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right text-green-600 font-medium">Paid</td>
                        <td class="px-4 py-2 text-right text-green-600 font-semibold">{{ number_format($invoice->paid_amount) }}</td>
                    </tr>
                    <tr class="{{ $invoice->balance > 0 ? 'bg-red-50' : 'bg-green-50' }}">
                        <td colspan="4" class="px-4 py-2 text-right font-bold {{ $invoice->balance > 0 ? 'text-red-700' : 'text-green-700' }}">Balance Due</td>
                        <td class="px-4 py-2 text-right font-black {{ $invoice->balance > 0 ? 'text-red-700' : 'text-green-700' }}">{{ number_format($invoice->balance) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>

            {{-- Payment Details Box --}}
            <div class="border-2 border-gray-800 p-4 inline-block">
                <p class="font-bold text-sm mb-1">PAYMENT DETAILS:</p>
                <p class="text-sm">Bank Name: <strong>{{ $settings['bank_name'] ?? 'DFCC Bank' }}</strong></p>
                <p class="text-sm">Branch: <strong>{{ $settings['bank_branch'] ?? 'Gampaha' }}</strong></p>
                <p class="text-sm">Account Name: <strong>{{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</strong></p>
                <p class="text-sm">Account Number: <strong>{{ $settings['bank_account_number'] ?? '102003031923' }}</strong></p>
            </div>

            @if($invoice->terms_conditions)
            <div class="mt-6 border-t border-gray-200 pt-4">
                <p class="font-bold text-xs text-gray-700 mb-1 uppercase tracking-wide">Terms & Conditions</p>
                <pre class="text-xs text-gray-600 whitespace-pre-wrap font-sans">{{ $invoice->terms_conditions }}</pre>
            </div>
            @endif
        </div>
        <div class="bg-red-600 h-2"></div>
    </div>

    {{-- Payment history --}}
    @if($invoice->payments->count())
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Payment History</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="pb-2 text-left text-xs font-medium text-gray-500">Date</th>
                    <th class="pb-2 text-left text-xs font-medium text-gray-500">Method</th>
                    <th class="pb-2 text-left text-xs font-medium text-gray-500">Reference</th>
                    <th class="pb-2 text-right text-xs font-medium text-gray-500">Amount</th>
                    <th class="pb-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($invoice->payments as $pmt)
                <tr>
                    <td class="py-2 text-gray-600">{{ $pmt->payment_date->format('d M Y') }}</td>
                    <td class="py-2 capitalize text-gray-600">{{ str_replace('_', ' ', $pmt->payment_method) }}</td>
                    <td class="py-2 text-gray-500">{{ $pmt->reference_number ?: '—' }}</td>
                    <td class="py-2 text-right font-semibold text-green-600">LKR {{ number_format($pmt->amount) }}</td>
                    <td class="py-2 pl-2">
                        <form method="POST" action="{{ route('invoices.payment.delete', [$invoice, $pmt]) }}" onsubmit="return confirm('Remove this payment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-red-500 text-xs"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="flex items-center gap-3">
        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to list
        </a>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                <i class="fa-solid fa-trash mr-1"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Record Payment Modal --}}
<div id="paymentModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50" x-data>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900">Record Payment</h3>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date *</label>
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Amount (LKR) *</label>
                    <input type="number" name="amount" value="{{ $invoice->balance }}" step="0.01" min="0.01"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Payment Method *</label>
                <select name="payment_method" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
                    @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'card' => 'Card', 'cheque' => 'Cheque'] as $v => $l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Reference Number</label>
                <input type="text" name="reference_number" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Optional">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-green-700 transition">
                    Save Payment
                </button>
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 bg-white border border-gray-200 text-gray-600 text-sm py-2.5 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
