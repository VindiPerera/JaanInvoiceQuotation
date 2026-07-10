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

    {{-- Template Selector --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
            <div class="w-1 h-8 bg-blue-600 rounded-full"></div>
            <h2 class="text-xl font-bold text-slate-900">Select Template</h2>
        </div>

        @if(isset($templates) && $templates->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="quote_type" x-model="quoteType">
            @foreach($templates as $tpl)
            <button type="button" @click="quoteType = '{{ $tpl->key }}'"
                :class="quoteType === '{{ $tpl->key }}'
                    ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-md'
                    : 'border-slate-300 text-slate-700 hover:border-blue-400 hover:bg-blue-50'"
                class="flex flex-col items-center justify-center gap-3 p-6 border-2 rounded-xl transition-all cursor-pointer">
                <i class="fas {{ $tpl->icon ?: 'fa-file' }} text-3xl"></i>
                <div>
                    <div class="font-bold text-sm">{{ $tpl->name }}</div>
                    @if($tpl->subtitle)
                    <div class="text-xs text-slate-500">{{ $tpl->subtitle }}</div>
                    @endif
                </div>
            </button>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-slate-600 mb-4">No templates configured yet.</p>
            <x-button href="{{ route('quote-templates.create') }}" variant="secondary" icon="fa-plus">
                Create Your First Template
            </x-button>
        </div>
        @endif
    </div>

    {{-- Quotation Details --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
            <div class="w-1 h-8 bg-indigo-600 rounded-full"></div>
            <h2 class="text-xl font-bold text-slate-900">Quotation Information</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-input
                label="Quotation Number"
                name="quotation_number"
                value="{{ $quotation?->quotation_number ?? $nextNumber }}"
                required
            />

            <x-form-input
                label="Date"
                name="quotation_date"
                type="date"
                value="{{ $quotation ? $quotation->quotation_date->format('Y-m-d') : now()->format('Y-m-d') }}"
                required
            />
        </div>
    </div>

    {{-- Customer Details --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
            <div class="w-1 h-8 bg-green-600 rounded-full"></div>
            <h2 class="text-xl font-bold text-slate-900">Customer Information</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Select Existing Customer</label>
                <select name="customer_id" x-model="selectedCustomer" @change="fillCustomer($event)"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-address="{{ $c->address }}" data-contact="{{ $c->contact }}"
                            {{ old('customer_id', $quotation?->customer_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <x-form-input
                label="Customer Name"
                name="customer_name"
                id="customer_name"
                value="{{ $quotation?->customer_name ?? '' }}"
                required
            />

            <div class="space-y-2">
                <label for="customer_address" class="block text-sm font-semibold text-slate-700">Address</label>
                <textarea name="customer_address" id="customer_address" rows="3"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none">{{ old('customer_address', $quotation?->customer_address ?? '') }}</textarea>
            </div>

            <x-form-input
                label="Contact"
                name="customer_contact"
                id="customer_contact"
                value="{{ $quotation?->customer_contact ?? '' }}"
            />

            <div class="md:col-span-2">
                <x-form-input
                    label="Subject"
                    name="subject"
                    value="{{ $quotation?->subject ?? '' }}"
                    placeholder="e.g. Maria POS System - Complete Package"
                />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Project Overview <span class="font-normal text-slate-500">(optional)</span></label>
                <textarea name="project_overview" x-model="projectOverview" rows="4"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none"
                    placeholder="Describe the project scope, requirements, and deliverables...">{{ old('project_overview', $quotation?->project_overview ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-purple-600 rounded-full"></div>
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
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 mb-2 transition">
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
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    placeholder="Item name">
                            </td>
                            <td class="px-4 py-3">
                                <textarea :name="`items[${index}][description]`" x-model="item.description" rows="2"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none"
                                    placeholder="Description..."></textarea>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calcRow(index)"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-center focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    min="0" step="0.01">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcRow(index)"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-right focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
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
                        <td colspan="4" class="px-4 py-3 text-right font-bold text-slate-700">Total Amount (LKR)</td>
                        <td class="px-4 py-3 text-right font-bold text-slate-900" x-text="formatNum(total)"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Notes & Terms --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <label class="block text-sm font-bold text-slate-700 mb-3">Terms & Conditions</label>
        <textarea name="terms_and_conditions" rows="4"
            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none"
            placeholder="Enter your terms and conditions...">{{ old('terms_and_conditions', $quotation->terms_and_conditions ?? '') }}</textarea>
    </div>

    {{-- Form Actions --}}
    <div class="flex gap-3 pt-6">
        <x-button type="submit" variant="primary" size="lg" icon="fa-save">
            {{ isset($quotation) && $quotation ? 'Update Quotation' : 'Create Quotation' }}
        </x-button>
        <x-button href="{{ route('quotations.index') }}" variant="outline" size="lg" icon="fa-arrow-left">
            Cancel
        </x-button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quotationForm', () => ({
            items: @json(isset($quotation) && $quotation ? $quotation->items->toArray() : []),
            quoteType: '{{ old('quote_type', $quotation?->quote_type ?? '') }}',
            selectedCustomer: '{{ old('customer_id', $quotation?->customer_id ?? '') }}',
            projectOverview: '{{ old('project_overview', $quotation?->project_overview ?? '') }}',

            get total() {
                return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            },

            addItem() {
                this.items.push({
                    item_name: '',
                    description: '',
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

            pickHardware(index, event) {
                const option = event.target.selectedOptions[0];
                if (option.value) {
                    const item = this.items[index];
                    item.item_name = option.dataset.name;
                    item.description = option.dataset.desc;
                    item.unit_price = parseFloat(option.dataset.price);
                    this.calcRow(index);
                }
            },

            fillCustomer(event) {
                const option = event.target.selectedOptions[0];
                if (option.value) {
                    document.querySelector('#customer_name').value = option.text;
                    document.querySelector('#customer_address').value = option.dataset.address;
                    document.querySelector('#customer_contact').value = option.dataset.contact;
                }
            },

            formatNum(num) {
                return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(num || 0);
            }
        }))
    });
</script>
@endpush
