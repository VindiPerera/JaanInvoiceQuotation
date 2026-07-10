<div class="space-y-6 max-w-6xl">
    {{-- Alerts --}}
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-l-red-600 border border-red-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-circle-exclamation text-red-600 mt-0.5 flex-shrink-0 text-lg"></i>
                <div>
                    <h3 class="font-bold text-red-900 mb-2">Validation Errors</h3>
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

    @if(isset($quotation) && $quotation)
        <div class="bg-blue-50 border-l-4 border-l-blue-600 border border-blue-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-info-circle text-blue-600 text-lg flex-shrink-0"></i>
                <p class="text-blue-900 font-medium">Converting from quotation <strong>{{ $quotation->quotation_number }}</strong></p>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Invoice Details Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                <div class="w-1 h-8 bg-blue-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-slate-900">Invoice Information</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-form-input
                    label="Invoice Number"
                    name="invoice_number"
                    value="{{ $invoice->invoice_number ?? $nextNumber }}"
                    required
                />

                <x-form-input
                    label="Date"
                    name="invoice_date"
                    type="date"
                    value="{{ $invoice ? $invoice->invoice_date->format('Y-m-d') : now()->format('Y-m-d') }}"
                    required
                />

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Payment Status</label>
                    <select name="payment_status" x-model="paymentStatus" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none">
                        <option value="pending" {{ old('payment_status', $invoice->payment_status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('payment_status', $invoice->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Customer Details Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                <div class="w-1 h-8 bg-indigo-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-slate-900">Customer Information</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">Select Existing Customer</label>
                    <select name="customer_id" @change="fillCustomer($event)" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none">
                        <option value="">— Select customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-address="{{ $c->address }}" data-contact="{{ $c->contact }}"
                                {{ old('customer_id', $invoice->customer_id ?? ($quotation->customer_id ?? '')) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-form-input
                    label="Customer Name"
                    name="customer_name"
                    id="inv_customer_name"
                    value="{{ $invoice->customer_name ?? ($quotation->customer_name ?? '') }}"
                    required
                />

                <div class="space-y-2">
                    <label for="inv_customer_address" class="block text-sm font-semibold text-slate-700">Address</label>
                    <textarea name="customer_address" id="inv_customer_address" rows="3"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none resize-none">{{ old('customer_address', $invoice->customer_address ?? ($quotation->customer_address ?? '')) }}</textarea>
                </div>

                <x-form-input
                    label="Contact"
                    name="customer_contact"
                    value="{{ $invoice->customer_contact ?? ($quotation->customer_contact ?? '') }}"
                />
            </div>
        </div>

        {{-- Line Items --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-green-600 rounded-full"></div>
                    <h2 class="text-xl font-bold text-slate-900">Line Items</h2>
                </div>
                <x-button type="button" @click="addItem" variant="success" size="sm" icon="fa-plus">
                    Add Item
                </x-button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-200 bg-slate-50">
                            <th class="px-4 py-3 text-left font-bold text-slate-700">#</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-700">Item Name</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-700">Description</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-700">Warranty</th>
                            <th class="px-4 py-3 text-center font-bold text-slate-700">Qty</th>
                            <th class="px-4 py-3 text-right font-bold text-slate-700">Unit Price</th>
                            <th class="px-4 py-3 text-right font-bold text-slate-700">Total</th>
                            <th class="px-4 py-3 text-center font-bold text-slate-700">Hide</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-slate-200 hover:bg-blue-50 transition">
                                <td class="px-4 py-3 text-slate-500 font-semibold" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <select @change="pickHardware(index, $event)"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 mb-2 transition focus:outline-none">
                                        <option value="">— Select from catalog —</option>
                                        @foreach($hardware as $hw)
                                            <option value="{{ $hw->id }}"
                                                data-name="{{ $hw->name }}"
                                                data-desc="{{ $hw->description }}"
                                                data-price="{{ $hw->unit_price }}"
                                                data-warranty="{{ $hw->warranty ?? '' }}">
                                                {{ $hw->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                                        placeholder="Item name">
                                </td>
                                <td class="px-4 py-3">
                                    <textarea :name="`items[${index}][description]`" x-model="item.description" rows="2"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none resize-none"
                                        placeholder="Description..."></textarea>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" :name="`items[${index}][warranty]`" x-model="item.warranty"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                                        placeholder="1 Year">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calcRow(index)"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-center focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                                        min="0" step="0.01">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcRow(index)"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-right focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                                        min="0" step="0.01">
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">
                                    <input type="hidden" :name="`items[${index}][total]`" :value="item.total">
                                    <span x-text="formatNum(item.total)"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="hidden" :name="`items[${index}][is_hidden]`" :value="item.is_hidden ? '1' : '0'">
                                    <input type="checkbox" @change="item.is_hidden = $event.target.checked"
                                        :checked="item.is_hidden"
                                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-200">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-200">
                            <td colspan="5" class="px-4 py-3 text-right font-bold text-slate-700">Subtotal</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900" x-text="'LKR ' + formatNum(subtotal)"></td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-slate-700">Tax / Other</td>
                            <td class="px-4 py-3">
                                <input type="number" name="tax_amount" x-model.number="taxAmount" @input="calcTotal"
                                    value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-right focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                                    min="0" step="0.01">
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900" x-text="'LKR ' + formatNum(taxAmount)"></td>
                        </tr>
                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                            <td colspan="4" class="px-4 py-4 text-right text-base font-bold">GRAND TOTAL (LKR)</td>
                            <td class="px-4 py-4">
                                <input type="number" name="manual_total" x-model.number="manualTotal"
                                    :value="total"
                                    class="w-full px-3 py-2 bg-blue-700 border-2 border-blue-400 rounded-lg text-sm font-bold text-white text-right focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    min="0" step="0.01">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Payment Details --}}
        <div x-show="paymentStatus !== 'paid'" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-200">
                <div class="w-1 h-8 bg-green-600 rounded-full"></div>
                <h3 class="text-lg font-bold text-slate-900">Payment Details</h3>
                <span class="text-xs font-medium text-slate-500">(shown on invoice)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-green-50 p-4 rounded-lg border border-green-200">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Bank Name</p>
                    <p class="text-slate-900 font-medium">{{ $settings['bank_name'] ?? 'DFCC Bank' }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Branch</p>
                    <p class="text-slate-900 font-medium">{{ $settings['bank_branch'] ?? 'Gampaha' }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Account Name</p>
                    <p class="text-slate-900 font-medium">{{ $settings['bank_account_name'] ?? 'JAAN Network (Pvt) Ltd' }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Account Number</p>
                    <p class="text-slate-900 font-medium">{{ $settings['bank_account_number'] ?? '102003031923' }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                Edit these in <a href="{{ route('settings.index') }}" class="text-blue-600 hover:underline font-medium">Settings</a>
            </p>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <label class="block text-sm font-bold text-slate-700 mb-2">Internal Notes <span class="font-normal text-slate-500">(not shown on invoice)</span></label>
            <textarea name="notes" rows="4"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                placeholder="Add any internal notes here...">{{ old('notes', $invoice->notes ?? '') }}</textarea>
        </div>

        {{-- Form Actions --}}
        <div class="flex gap-3 pt-6">
            <x-button type="submit" variant="primary" size="lg" icon="fa-save">
                {{ isset($invoice) && $invoice ? 'Update Invoice' : 'Create Invoice' }}
            </x-button>
            <x-button href="{{ route('invoices.index') }}" variant="outline" size="lg" icon="fa-arrow-left">
                Cancel
            </x-button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('invoiceForm', () => ({
            items: @json(isset($invoice) && $invoice ? $invoice->items->toArray() : []),
            paymentStatus: '{{ old('payment_status', $invoice->payment_status ?? 'pending') }}',
            taxAmount: {{ old('tax_amount', $invoice->tax_amount ?? 0) }},
            manualTotal: 0,

            get subtotal() {
                return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            },

            get total() {
                return this.subtotal + this.taxAmount;
            },

            addItem() {
                this.items.push({
                    item_name: '',
                    description: '',
                    warranty: '',
                    quantity: 1,
                    unit_price: 0,
                    total: 0,
                    is_hidden: false
                });
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            calcRow(index) {
                const item = this.items[index];
                item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
            },

            calcTotal() {
                this.manualTotal = this.total;
            },

            pickHardware(index, event) {
                const option = event.target.selectedOptions[0];
                if (option.value) {
                    const item = this.items[index];
                    item.item_name = option.dataset.name;
                    item.description = option.dataset.desc;
                    item.unit_price = parseFloat(option.dataset.price);
                    item.warranty = option.dataset.warranty;
                    this.calcRow(index);
                }
            },

            fillCustomer(event) {
                const option = event.target.selectedOptions[0];
                if (option.value) {
                    document.querySelector('#inv_customer_name').value = option.text;
                    document.querySelector('#inv_customer_address').value = option.dataset.address;
                    document.querySelector('#inv_customer_contact').value = option.dataset.contact;
                }
            },

            toggleAllHide(event) {
                this.items.forEach(item => item.is_hidden = event.target.checked);
            },

            formatNum(num) {
                return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(num || 0);
            }
        }))
    });
</script>
@endpush
