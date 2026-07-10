@extends('layouts.app')
@section('title', 'View Invoice ' . $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number)

@section('header-actions')
    <x-button href="{{ route('invoices.pdf', $invoice) }}" variant="danger" icon="fa-file-pdf">
        Invoice PDF
    </x-button>
    @if($invoice->payments->count())
        <x-button href="{{ route('invoices.payment.receipt', $invoice) }}" variant="warning" icon="fa-receipt">
            Payment Receipt
        </x-button>
    @endif
    <x-button href="{{ route('invoices.edit', $invoice) }}" variant="primary" icon="fa-pencil">
        Edit Invoice
    </x-button>
    @if(!$invoice->isFullyPaid())
        <x-button @click="showPaymentModal = true" variant="success" icon="fa-money-bill">
            Record Payment
        </x-button>
    @endif
@endsection

@section('content')
<div x-data="paymentManager()" class="space-y-6">
    {{-- Success/Error Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-l-red-600 border border-red-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-circle-exclamation text-red-600 mt-0.5 flex-shrink-0 text-lg"></i>
                <div>
                    <h3 class="font-bold text-red-900 mb-2">Payment Error</h3>
                    <ul class="space-y-1 text-red-700 text-sm">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <span class="w-1 h-1 bg-red-600 rounded-full"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Invoice Total -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-slate-600">Invoice Total</p>
                <i class="fas fa-money-bill text-blue-600 text-lg opacity-30"></i>
            </div>
            <p class="text-2xl font-bold text-slate-900">LKR {{ number_format($invoice->total_amount) }}</p>
            <p class="text-xs text-slate-500 mt-2">Amount to be paid</p>
        </div>

        <!-- Amount Paid -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-slate-600">Amount Paid</p>
                <i class="fas fa-check-circle text-green-600 text-lg opacity-30"></i>
            </div>
            <p class="text-2xl font-bold text-green-600">LKR {{ number_format($invoice->paid_amount) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $invoice->payments()->count() }} payment(s)</p>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-slate-600">Outstanding</p>
                <i class="fas fa-hourglass-half text-amber-600 text-lg opacity-30"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600">LKR {{ number_format($invoice->balance) }}</p>
            <p class="text-xs text-slate-500 mt-2">Remaining balance</p>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-slate-600">Status</p>
                <i class="fas fa-circle-info text-indigo-600 text-lg opacity-30"></i>
            </div>
            <div class="mt-1">
                @if($invoice->payment_status === 'paid')
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-bold border border-green-300">
                        <i class="fas fa-check-circle"></i>Paid
                    </span>
                @elseif($invoice->payment_status === 'partial')
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-sm font-bold border border-amber-300">
                        <i class="fas fa-hourglass-half"></i>Partially Paid
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-full text-sm font-bold border border-red-300">
                        <i class="fas fa-clock"></i>Pending
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ (float)$invoice->paid_amount / (float)$invoice->total_amount * 100 | number_format(1) }}% collected</p>
        </div>
    </div>

    {{-- Payment Progress Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-900">Payment Progress</h3>
            <span class="text-xs font-semibold text-slate-500">{{ (float)$invoice->paid_amount / (float)$invoice->total_amount * 100 | number_format(0) }}%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full transition-all duration-300 rounded-full"
                 style="width: {{ min((float)$invoice->paid_amount / (float)$invoice->total_amount * 100, 100) }}%"></div>
        </div>
        <div class="flex justify-between mt-3 text-xs text-slate-600">
            <span>LKR {{ number_format($invoice->paid_amount) }}</span>
            <span>LKR {{ number_format($invoice->total_amount) }}</span>
        </div>
    </div>

    {{-- Invoice & Bill To (2 Column) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bill To -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-200">
                <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                <h3 class="text-lg font-bold text-slate-900">Bill To</h3>
            </div>
            <div class="space-y-2">
                <p class="text-sm text-slate-600"><span class="font-bold text-slate-900">{{ $invoice->customer_name }}</span></p>
                @if($invoice->customer_address)
                    <p class="text-sm text-slate-600 whitespace-pre-line">{{ $invoice->customer_address }}</p>
                @endif
                @if($invoice->customer_contact)
                    <p class="text-sm text-slate-600"><i class="fas fa-phone text-slate-400 mr-2"></i>{{ $invoice->customer_contact }}</p>
                @endif
            </div>
        </div>

        <!-- Invoice Info -->
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
                    <span class="font-bold">
                        @if($invoice->payment_status === 'paid')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-bold">Paid</span>
                        @elseif($invoice->payment_status === 'partial')
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-bold">Partial</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-bold">Pending</span>
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
                        <span class="text-slate-600 font-medium">Tax / Other</span>
                        <span class="font-semibold text-slate-900">LKR {{ number_format($invoice->tax_amount) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg pt-2 pb-3 border-b-2 border-blue-600">
                        <span class="font-bold text-slate-900">GRAND TOTAL (LKR)</span>
                        <span class="font-bold text-blue-600">{{ number_format($invoice->total_amount) }}</span>
                    </div>
                    @if($invoice->paid_amount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span class="font-medium">Less: Advance Payment</span>
                        <span class="font-semibold">({{ number_format($invoice->paid_amount) }})</span>
                    </div>
                    @endif
                    @if($invoice->balance > 0)
                    <div class="flex justify-between text-lg pt-2">
                        <span class="font-bold text-slate-900">Balance Due (LKR)</span>
                        <span class="font-bold text-amber-600">{{ number_format($invoice->balance) }}</span>
                    </div>
                    @elseif($invoice->payment_status === 'paid')
                    <div class="flex justify-between text-lg pt-2">
                        <span class="font-bold text-slate-900">Status</span>
                        <span class="font-bold text-green-600">FULLY PAID</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    @if($invoice->payments->count())
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Payment History</h3>
                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                    {{ $invoice->payments->count() }} payment(s)
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700">Recorded By</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $pmt)
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition">
                        <td class="px-4 py-3 text-slate-600 font-medium">
                            <div>{{ $pmt->payment_date->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $pmt->payment_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-2">
                                @if($pmt->payment_method === 'cash')
                                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                @elseif($pmt->payment_method === 'bank_transfer')
                                    <i class="fas fa-university text-blue-600"></i>
                                @elseif($pmt->payment_method === 'card')
                                    <i class="fas fa-credit-card text-purple-600"></i>
                                @elseif($pmt->payment_method === 'cheque')
                                    <i class="fas fa-receipt text-orange-600"></i>
                                @elseif($pmt->payment_method === 'online')
                                    <i class="fas fa-globe text-indigo-600"></i>
                                @endif
                                <span class="capitalize text-slate-700 font-medium">{{ str_replace('_', ' ', $pmt->payment_method) }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                            {{ $pmt->reference_number ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-xs">
                            @if($pmt->createdBy)
                                {{ $pmt->createdBy->name }}
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">
                            LKR {{ number_format($pmt->amount) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                @if($pmt->notes)
                                    <button type="button" @click="showNote('{{ addslashes($pmt->notes) }}')" class="text-slate-400 hover:text-blue-600 transition" title="View notes">
                                        <i class="fas fa-sticky-note text-sm"></i>
                                    </button>
                                @endif
                                <button type="button" @click="editPayment({{ $pmt->id }}, '{{ $pmt->payment_date->format('Y-m-d') }}', {{ $pmt->amount }}, '{{ $pmt->payment_method }}', '{{ $pmt->reference_number }}', '{{ addslashes($pmt->notes) }}')"
                                    class="text-slate-400 hover:text-blue-600 transition" title="Edit payment">
                                    <i class="fas fa-pencil text-sm"></i>
                                </button>
                                <form method="POST" action="{{ route('invoices.payment.delete', [$invoice, $pmt]) }}" class="inline" onsubmit="return confirm('Remove this payment? This will update invoice totals.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Delete payment">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($invoice->payments->count() > 1)
        <div class="mt-6 pt-6 border-t border-slate-200">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Total Transactions</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $invoice->payments->count() }}</p>
                </div>
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Last Payment</p>
                    <p class="text-sm font-bold text-slate-900">{{ $invoice->payments->first()->payment_date->format('d M Y') }}</p>
                </div>
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Average Payment</p>
                    <p class="text-sm font-bold text-slate-900">LKR {{ number_format($invoice->paid_amount / $invoice->payments->count()) }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300 p-12 text-center">
        <i class="fas fa-inbox text-slate-400 text-3xl mb-4 block"></i>
        <p class="text-slate-600 font-medium">No payments recorded yet</p>
        <p class="text-slate-500 text-sm mt-1">Invoice total: <strong>LKR {{ number_format($invoice->total_amount) }}</strong></p>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-3 flex-wrap">
        <x-button href="{{ route('invoices.index') }}" variant="outline" icon="fa-arrow-left">
            Back to Invoices
        </x-button>
        <x-button href="{{ route('invoices.edit', $invoice) }}" variant="primary" icon="fa-pencil">
            Edit Invoice
        </x-button>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete this invoice and all associated payments?');">
            @csrf @method('DELETE')
            <x-button type="submit" variant="danger" icon="fa-trash">
                Delete Invoice
            </x-button>
        </form>
    </div>

    {{-- Payment Modal --}}
    <div x-show="showPaymentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-900">Record Payment</h3>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Payment Summary in Modal --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-4 mb-6">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Invoice Total:</span>
                        <span class="font-bold text-slate-900">LKR {{ number_format($invoice->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Already Paid:</span>
                        <span class="font-bold text-green-600">LKR {{ number_format($invoice->paid_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-blue-200">
                        <span class="font-semibold text-slate-700">Remaining Balance:</span>
                        <span class="font-bold text-amber-600 text-lg">LKR {{ number_format($invoice->balance) }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="space-y-4" id="paymentForm">
                @csrf

                <div class="space-y-2">
                    <label for="payment_date" class="block text-sm font-semibold text-slate-700">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ now()->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                        required>
                </div>

                <div class="space-y-2">
                    <label for="amount" class="block text-sm font-semibold text-slate-700">Amount (LKR) *</label>
                    <input type="number" name="amount" id="amount"
                        value="{{ $invoice->balance }}"
                        step="0.01"
                        min="0.01"
                        max="{{ $invoice->balance }}"
                        x-model="paymentAmount"
                        @input="validateAmount()"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                        required>
                    <div x-show="paymentError" class="text-xs text-red-600 font-medium" x-text="paymentError"></div>
                    <div x-show="!paymentError && paymentAmount" class="text-xs text-amber-600 font-medium">
                        Remaining after payment: <strong>LKR <span x-text="formatNumber({{ $invoice->balance }} - parseFloat(paymentAmount) || 0)"></span></strong>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="payment_method" class="block text-sm font-semibold text-slate-700">Payment Method *</label>
                    <select name="payment_method" id="payment_method" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none" required>
                        <option value="">— Select payment method —</option>
                        <option value="cash">💵 Cash</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                        <option value="card">💳 Card</option>
                        <option value="cheque">📋 Cheque</option>
                        <option value="online">🌐 Online</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="reference_number" class="block text-sm font-semibold text-slate-700">Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number"
                        placeholder="e.g., Check #1234, Transfer ID, etc."
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                        maxlength="100">
                </div>

                <div class="space-y-2">
                    <label for="pmt_notes" class="block text-sm font-semibold text-slate-700">Notes</label>
                    <textarea name="notes" id="pmt_notes" rows="2"
                        placeholder="Add any notes about this payment..."
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none resize-none"
                        maxlength="500"></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <x-button type="submit" variant="success" class="flex-1">
                        <i class="fas fa-check mr-2"></i>Record Payment
                    </x-button>
                    <x-button type="button" variant="outline" class="flex-1" @click="showPaymentModal = false">
                        Cancel
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Notes Modal --}}
    <div x-show="showNotesModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Payment Notes</h3>
                <button @click="showNotesModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                <p class="text-slate-700 whitespace-pre-line text-sm" x-text="notesContent"></p>
            </div>
            <div class="flex gap-3 mt-6">
                <x-button type="button" variant="outline" class="flex-1" @click="showNotesModal = false">
                    Close
                </x-button>
            </div>
        </div>
    </div>

    {{-- Edit Payment Modal --}}
    <div x-show="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-900">Edit Payment</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Edit Payment Summary in Modal --}}
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200 p-4 mb-6">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Invoice Total:</span>
                        <span class="font-bold text-slate-900">LKR {{ number_format($invoice->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Currently Paid:</span>
                        <span class="font-bold text-green-600">LKR {{ number_format($invoice->paid_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-orange-200">
                        <span class="font-semibold text-slate-700">Remaining Balance:</span>
                        <span class="font-bold text-amber-600 text-lg">LKR {{ number_format($invoice->balance) }}</span>
                    </div>
                    <p class="text-xs text-orange-700 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Modifying this payment will recalculate the total
                    </p>
                </div>
            </div>

            <form method="POST" :action="`/invoices/{{ $invoice->id }}/payment/${editingPaymentId}`" class="space-y-4" id="editPaymentForm" x-ref="editForm">
                @csrf
                @method('PATCH')

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Payment Date *</label>
                    <input type="date" name="payment_date" x-model="editPaymentDate"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition focus:outline-none"
                        required>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Amount (LKR) *</label>
                    <input type="number" name="amount" x-model.number="editPaymentAmount"
                        step="0.01" min="0.01" @input="validateEditAmount()"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition focus:outline-none"
                        required>
                    <div x-show="editPaymentError" class="text-xs text-red-600 font-medium" x-text="editPaymentError"></div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Payment Method *</label>
                    <select name="payment_method" x-model="editPaymentMethod"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition focus:outline-none" required>
                        <option value="cash">💵 Cash</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                        <option value="card">💳 Card</option>
                        <option value="cheque">📋 Cheque</option>
                        <option value="online">🌐 Online</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Reference Number</label>
                    <input type="text" name="reference_number" x-model="editPaymentReference"
                        placeholder="Optional"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition focus:outline-none"
                        maxlength="100">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Notes</label>
                    <textarea name="notes" x-model="editPaymentNotes" rows="2"
                        placeholder="Payment notes..."
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition focus:outline-none resize-none"
                        maxlength="500"></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <x-button type="submit" variant="warning" class="flex-1">
                        <i class="fas fa-save mr-2"></i>Update Payment
                    </x-button>
                    <x-button type="button" variant="outline" class="flex-1" @click="showEditModal = false">
                        Cancel
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function paymentManager() {
        return {
            showPaymentModal: false,
            showNotesModal: false,
            showEditModal: false,
            paymentAmount: {{ $invoice->balance }},
            paymentError: '',
            notesContent: '',

            // Edit payment fields
            editingPaymentId: null,
            editPaymentDate: '',
            editPaymentAmount: 0,
            editPaymentMethod: '',
            editPaymentReference: '',
            editPaymentNotes: '',
            editPaymentError: '',

            validateAmount() {
                const maxAmount = {{ $invoice->balance }};
                const amount = parseFloat(this.paymentAmount) || 0;

                if (amount <= 0) {
                    this.paymentError = 'Payment amount must be greater than zero.';
                } else if (amount > maxAmount) {
                    this.paymentError = `Payment cannot exceed remaining balance of LKR ${this.formatNumber(maxAmount)}`;
                } else {
                    this.paymentError = '';
                }
            },

            validateEditAmount() {
                const maxAmount = {{ $invoice->balance }};
                const amount = parseFloat(this.editPaymentAmount) || 0;

                if (amount <= 0) {
                    this.editPaymentError = 'Payment amount must be greater than zero.';
                } else if (amount > maxAmount + 100) {
                    // Allow small variance for edit
                    this.editPaymentError = 'Payment amount seems too high.';
                } else {
                    this.editPaymentError = '';
                }
            },

            editPayment(id, date, amount, method, reference, notes) {
                this.editingPaymentId = id;
                this.editPaymentDate = date;
                this.editPaymentAmount = amount;
                this.editPaymentMethod = method;
                this.editPaymentReference = reference || '';
                this.editPaymentNotes = notes || '';
                this.editPaymentError = '';
                this.showEditModal = true;
            },

            formatNumber(num) {
                return new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(num || 0);
            },

            showNote(notes) {
                this.notesContent = notes;
                this.showNotesModal = true;
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('paymentManager', paymentManager);
    });
</script>
@endpush
