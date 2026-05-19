<div class="space-y-6 max-w-5xl">

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(isset($quotation) && $quotation)
        <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
        <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-2.5 rounded-lg">
            <i class="fa-solid fa-circle-info mr-1"></i> Converting from quotation <strong>{{ $quotation->quotation_number }}</strong>
        </div>
    @endif

    {{-- Details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Invoice Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Invoice Number *</label>
                <input type="text" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number ?? $nextNumber) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date *</label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice ? $invoice->invoice_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Payment Status</label>
                <select name="payment_status" x-model="paymentStatus"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    <option value="pending" {{ old('payment_status', $invoice->payment_status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ old('payment_status', $invoice->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Customer --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Customer Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Select Existing Customer</label>
                <select name="customer_id" @change="fillCustomer($event)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-address="{{ $c->address }}" data-contact="{{ $c->contact }}"
                            {{ old('customer_id', $invoice->customer_id ?? ($quotation->customer_id ?? '')) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Customer Name *</label>
                <input type="text" name="customer_name" id="inv_customer_name"
                    value="{{ old('customer_name', $invoice->customer_name ?? ($quotation->customer_name ?? '')) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                <textarea name="customer_address" id="inv_customer_address" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">{{ old('customer_address', $invoice->customer_address ?? ($quotation->customer_address ?? '')) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Contact</label>
                <input type="text" name="customer_contact" id="inv_customer_contact"
                    value="{{ old('customer_contact', $invoice->customer_contact ?? ($quotation->customer_contact ?? '')) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
        </div>
    </div>

    {{-- Line items --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Line Items</h2>
            <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
                <i class="fa-solid fa-plus"></i> Add Row
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="pb-2 text-left text-xs font-medium text-gray-500 w-10">#</th>
                        <th class="pb-2 text-left text-xs font-medium text-gray-500">Description</th>
                        <th class="pb-2 text-left text-xs font-medium text-gray-500 w-24">Qty</th>
                        <th class="pb-2 text-left text-xs font-medium text-gray-500 w-36">Unit Price (LKR)</th>
                        <th class="pb-2 text-right text-xs font-medium text-gray-500 w-36">Total (LKR)</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="border-b border-gray-50">
                            <td class="py-2 pr-2 text-gray-400 text-xs" x-text="index + 1"></td>
                            <td class="py-2 pr-2">
                                <select @change="pickHardware(index, $event)"
                                    class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300 mb-1 text-gray-500">
                                    <option value="">— Select from hardware catalog —</option>
                                    @foreach($hardware as $hw)
                                        <option value="{{ $hw->id }}"
                                            data-name="{{ $hw->name }}"
                                            data-desc="{{ $hw->description }}"
                                            data-price="{{ $hw->unit_price }}">
                                            {{ $hw->name }} — LKR {{ number_format($hw->unit_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                    class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Item / service description">
                            </td>
                            <td class="py-2 pr-2">
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calcRow(index)"
                                    class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    min="0" step="0.01">
                            </td>
                            <td class="py-2 pr-2">
                                <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcRow(index)"
                                    class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    min="0" step="0.01">
                            </td>
                            <td class="py-2 text-right font-medium text-gray-700" x-text="formatNum(item.total)"></td>
                            <td class="py-2 pl-2">
                                <button type="button" @click="removeItem(index)" class="text-gray-300 hover:text-red-500 transition">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td colspan="4" class="pt-3 text-right text-sm font-medium text-gray-600">Subtotal</td>
                        <td class="pt-3 text-right font-semibold text-gray-800" x-text="'LKR ' + formatNum(subtotal)"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="pt-2 text-right text-sm font-medium text-gray-600">Tax / Other</td>
                        <td class="pt-2 pr-2">
                            <input type="number" name="tax_amount" x-model.number="taxAmount" @input="calcTotal"
                                value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}"
                                class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                min="0" step="0.01">
                        </td>
                        <td class="pt-2 text-right font-semibold text-gray-700" x-text="'LKR ' + formatNum(taxAmount)"></td>
                        <td></td>
                    </tr>
                    <tr class="bg-red-50 rounded">
                        <td colspan="4" class="pt-3 pb-2 px-3 text-right text-base font-bold text-gray-900">GRAND TOTAL</td>
                        <td class="pt-3 pb-2 text-right text-lg font-black text-red-600 pr-1" x-text="'LKR ' + formatNum(total)"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payment details info (hidden when status is paid) --}}
    <div x-show="paymentStatus !== 'paid'" class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-3">Payment Details (shown on invoice)</h2>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 space-y-1">
            <p><span class="font-medium">Bank Name:</span> {{ $settings['bank_name'] ?? 'DFCC Bank' }}</p>
            <p><span class="font-medium">Branch:</span> {{ $settings['bank_branch'] ?? 'Gampaha' }}</p>
            <p><span class="font-medium">Account Name:</span> {{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</p>
            <p><span class="font-medium">Account Number:</span> {{ $settings['bank_account_number'] ?? '102003031923' }}</p>
        </div>
        <p class="text-xs text-gray-400 mt-2">Change in <a href="{{ route('settings.index') }}" class="underline">Settings</a></p>
    </div>

    {{-- Terms & Notes --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-3">Terms & Conditions</h2>
        <textarea name="terms_conditions" rows="6"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 font-mono">{{ old('terms_conditions', $invoice->terms_conditions ?? '') }}</textarea>
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">Notes (internal)</label>
            <textarea name="notes" rows="2"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">{{ old('notes', $invoice->notes ?? '') }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            {{ isset($invoice) && $invoice ? 'Update Invoice' : 'Save Invoice' }}
        </button>
        <a href="{{ route('invoices.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            Cancel
        </a>
    </div>
</div>
