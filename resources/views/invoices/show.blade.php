@extends('layouts.app')
@section('title', 'View Invoice ' . $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number)

@section('header-actions')
    <x-button href="{{ route('invoices.pdf', $invoice) }}" variant="danger" icon="fa-file-pdf">
        Download PDF
    </x-button>
    <x-button href="{{ route('invoices.edit', $invoice) }}" variant="primary" icon="fa-pencil">
        Edit Invoice
    </x-button>
    @if($invoice->payment_status !== 'paid')
        <x-button @click="showPaymentModal = true" variant="success" icon="fa-money-bill">
            Record Payment
        </x-button>
    @endif
@endsection

@section('content')
<div x-data="{ showPaymentModal: false }" class="space-y-6">
    {{-- Invoice Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Invoice Total"
            value="LKR {{ number_format($invoice->total_amount) }}"
            icon="fa-money-bill"
            color="blue"
        />
        <x-stat-card
            title="Amount Paid"
            value="LKR {{ number_format($invoice->paid_amount) }}"
            icon="fa-check-circle"
            color="green"
        />
        <x-stat-card
            title="Outstanding"
            value="LKR {{ number_format($invoice->balance) }}"
            icon="fa-clock"
            color="amber"
        />
        <x-stat-card
            title="Status"
            value="{{ ucfirst($invoice->payment_status) }}"
            icon="fa-circle-info"
            color="indigo"
        />
    </div>

    {{-- Invoice Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Bill To --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-200">
                <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                <h3 class="text-lg font-bold text-slate-900">Bill To</h3>
            </div>
            <div class="space-y-2">
                <p class="text-sm text-slate-600"><span class="font-bold text-slate-900">{{ $invoice->customer_name }}</span></p>
                @if($invoice->customer_address)
                    <p class="text-sm text-slate-600">{{ $invoice->customer_address }}</p>
                @endif
                @if($invoice->customer_contact)
                    <p class="text-sm text-slate-600">{{ $invoice->customer_contact }}</p>
                @endif
            </div>
        </div>

        {{-- Invoice Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-200">
                <div class="w-1 h-6 bg-indigo-600 rounded-full"></div>
                <h3 class="text-lg font-bold text-slate-900">Invoice Details</h3>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600 font-medium">Invoice Number</span>
                    <span class="font-bold text-slate-900">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 font-medium">Invoice Date</span>
                    <span class="font-bold text-slate-900">{{ $invoice->invoice_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 font-medium">Payment Status</span>
                    <span>
                        @if($invoice->payment_status === 'paid')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-300">
                                <i class="fas fa-check-circle mr-1"></i>Paid
                            </span>
                        @elseif($invoice->payment_status === 'partial')
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-300">
                                <i class="fas fa-hourglass-half mr-1"></i>Partial
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold border border-red-300">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-900">Line Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-700">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-700">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-700">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->items->where('is_hidden', false) as $item)
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition">
                        <td class="px-6 py-3 font-bold text-slate-900">{{ $item->item_number }}</td>
                        <td class="px-6 py-3">
                            <div class="font-semibold text-slate-900">{{ $item->item_name }}</div>
                            @if($item->warranty)
                                <div class="text-xs text-slate-500 mt-0.5">Warranty: {{ $item->warranty }}</div>
                            @endif
                            @if($item->description)
                                <div class="text-xs text-slate-500 mt-0.5">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">{{ number_format((float)$item->quantity, 0) }}</td>
                        <td class="px-6 py-3 text-right">LKR {{ number_format((float)$item->unit_price) }}</td>
                        <td class="px-6 py-3 text-right font-bold text-slate-900">LKR {{ number_format((float)$item->total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No items to display</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="bg-slate-50 border-t border-slate-200 p-6">
            <div class="flex justify-end max-w-sm">
                <div class="w-full space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 font-medium">Subtotal</span>
                        <span class="font-semibold text-slate-900">LKR {{ number_format($invoice->subtotal) }}</span>
                    </div>
                    @if($invoice->tax_amount > 0)
                    <div class="flex justify-between text-sm pb-3 border-b border-slate-200">
                        <span class="text-slate-600 font-medium">Tax/Other</span>
                        <span class="font-semibold text-slate-900">LKR {{ number_format($invoice->tax_amount) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg pt-2">
                        <span class="font-bold text-slate-900">Total (LKR)</span>
                        <span class="font-bold text-blue-600">{{ number_format($invoice->total_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    @if($invoice->payments->count())
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="mb-4 pb-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">Payment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Reference</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">Amount</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $pmt)
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition">
                        <td class="px-4 py-3 text-slate-600">{{ $pmt->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_', ' ', $pmt->payment_method) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $pmt->reference_number ?: '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">LKR {{ number_format($pmt->amount) }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('invoices.payment.delete', [$invoice, $pmt]) }}" class="inline" onsubmit="return confirm('Remove this payment?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-3">
        <x-button href="{{ route('invoices.index') }}" variant="outline" icon="fa-arrow-left">
            Back to Invoices
        </x-button>
        <x-button href="{{ route('invoices.edit', $invoice) }}" variant="primary" icon="fa-pencil">
            Edit Invoice
        </x-button>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete this invoice?');">
            @csrf @method('DELETE')
            <x-button type="submit" variant="danger" icon="fa-trash">
                Delete Invoice
            </x-button>
        </form>
    </div>

    {{-- Payment Modal --}}
    <div x-show="showPaymentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-900">Record Payment</h3>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <x-form-input
                        label="Date"
                        name="payment_date"
                        type="date"
                        value="{{ now()->format('Y-m-d') }}"
                        required
                    />

                    <x-form-input
                        label="Amount (LKR)"
                        name="amount"
                        type="number"
                        value="{{ $invoice->balance }}"
                        step="0.01"
                        min="0.01"
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Payment Method *</label>
                    <select name="payment_method" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <x-form-input
                    label="Reference Number"
                    name="reference_number"
                    placeholder="Optional"
                />

                <div class="space-y-2">
                    <label for="pmt_notes" class="block text-sm font-semibold text-slate-700">Notes</label>
                    <textarea name="notes" id="pmt_notes" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <x-button type="submit" variant="success" class="flex-1">
                        Save Payment
                    </x-button>
                    <x-button type="button" variant="outline" class="flex-1" @click="showPaymentModal = false">
                        Cancel
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
