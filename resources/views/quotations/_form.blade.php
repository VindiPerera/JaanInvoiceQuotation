<div class="space-y-6 max-w-5xl">

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Quotation Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Quotation Number *</label>
                <input type="text" name="quotation_number" value="{{ old('quotation_number', $quotation?->quotation_number ?? $nextNumber) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date *</label>
                <input type="date" name="quotation_date" value="{{ old('quotation_date', $quotation ? $quotation->quotation_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    @foreach(['draft','finalized','sent','accepted','rejected'] as $s)
                        <option value="{{ $s }}" {{ old('status', $quotation?->status ?? 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Customer card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Customer Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Select Existing Customer</label>
                <select name="customer_id" x-model="selectedCustomer" @change="fillCustomer($event)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-address="{{ $c->address }}" data-contact="{{ $c->contact }}"
                            {{ old('customer_id', $quotation?->customer_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Customer Name *</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $quotation?->customer_name ?? '') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                <textarea name="customer_address" id="customer_address" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">{{ old('customer_address', $quotation?->customer_address ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Contact</label>
                <input type="text" name="customer_contact" id="customer_contact" value="{{ old('customer_contact', $quotation?->customer_contact ?? '') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Subject</label>
                <input type="text" name="subject" value="{{ old('subject', $quotation?->subject ?? '') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                    placeholder="e.g. Quotation for Maria POS System - Complete Package">
            </div>
        </div>
    </div>

    {{-- Items / Hardware Package --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Hardware Package Items</h2>
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
                        <th class="pb-2 text-left text-xs font-medium text-gray-500 w-32">Unit Price</th>
                        <th class="pb-2 text-right text-xs font-medium text-gray-500 w-32">Total</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="border-b border-gray-50">
                            <td class="py-2 pr-2 text-gray-400 text-xs" x-text="index + 1"></td>
                            <td class="py-2 pr-2">
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                    class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Item description">
                                <input type="hidden" :name="`items[${index}][item_type]`" value="hardware">
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
                                value="{{ old('tax_amount', $quotation?->tax_amount ?? 0) }}"
                                class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                min="0" step="0.01">
                        </td>
                        <td class="pt-2 text-right font-semibold text-gray-700" x-text="'LKR ' + formatNum(taxAmount)"></td>
                        <td></td>
                    </tr>
                    <tr class="bg-red-50">
                        <td colspan="4" class="pt-3 pb-2 px-3 text-right text-base font-bold text-gray-800">TOTAL</td>
                        <td class="pt-3 pb-2 text-right text-base font-bold text-red-600 pr-1" x-text="'LKR ' + formatNum(total)"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Software Features --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Software Features</h2>
            <button type="button" @click="addFeature" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
                <i class="fa-solid fa-plus"></i> Add Feature
            </button>
        </div>
        <div class="space-y-2">
            <template x-for="(feature, idx) in features" :key="idx">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-check text-red-500 text-xs shrink-0"></i>
                    <input type="text" :name="`software_features[${idx}]`" x-model="features[idx]"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                        placeholder="Feature description">
                    <button type="button" @click="features.splice(idx, 1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </template>
        </div>
    </div>

    {{-- Additional Benefits --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Additional Benefits</h2>
            <button type="button" @click="addBenefit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
                <i class="fa-solid fa-plus"></i> Add Benefit
            </button>
        </div>
        <div class="space-y-2">
            <template x-for="(benefit, idx) in benefits" :key="idx">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle text-red-400 text-xs shrink-0"></i>
                    <input type="text" :name="`additional_benefits[${idx}]`" x-model="benefits[idx]"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                        placeholder="Benefit description">
                    <button type="button" @click="benefits.splice(idx, 1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </template>
        </div>
    </div>

    {{-- Terms --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-3">Terms & Conditions</h2>
        <textarea name="terms_conditions" rows="8"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 font-mono">{{ old('terms_conditions', $quotation?->terms_conditions ?? $defaultTerms) }}</textarea>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            {{ $quotation ? 'Update Quotation' : 'Save Quotation' }}
        </button>
        <a href="{{ route('quotations.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            Cancel
        </a>
    </div>
</div>
