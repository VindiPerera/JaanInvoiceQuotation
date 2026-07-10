<div class="space-y-6">
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

        {{-- Advance Payment Section --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-indigo-600 rounded-full"></div>
                    <h3 class="text-lg font-bold text-slate-900">Advance Payment (Optional)</h3>
                </div>
                <span class="text-xs font-medium text-slate-500 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                    {{ isset($invoice) && $invoice->payments->count() > 0 ? 'Editable' : 'Initial Payment' }}
                </span>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-indigo-900">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    {{ isset($invoice) && $invoice->payments->count() > 0 ?
                        'Edit the advance payment or update it. Changing the amount will recalculate invoice totals.' :
                        'Receive an advance or partial payment at the time of invoice creation. The remaining balance will be due later.' }}
                </p>
            </div>

            <div x-data="advancePayment()" class="space-y-4">
                {{-- Enable Advance Payment Toggle --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="has_advance" name="has_advance" x-model="hasAdvance"
                        class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <label for="has_advance" class="text-sm font-semibold text-slate-700 cursor-pointer">
                        <i class="fas fa-check mr-2 text-indigo-600"></i>
                        {{ isset($invoice) && $invoice->payments->count() > 0 ? 'Edit advance payment' : 'This invoice has an advance/initial payment' }}
                    </label>
                </div>

                {{-- Advance Payment Details --}}
                <div x-show="hasAdvance" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-200">
                    <x-form-input
                        label="Advance Amount (LKR)"
                        name="advance_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :value="isset($invoice) && $invoice->payments->count() > 0 ? $invoice->payments->first()->amount : old('advance_amount')"
                        x-model.number="advanceAmount"
                        @input="updateAdvanceInfo()"
                        placeholder="0.00"
                        :required="false"
                    />

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Payment Method</label>
                        <select name="advance_payment_method" x-model="advanceMethod"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition focus:outline-none">
                            <option value="">— Select Method —</option>
                            <option value="cash" {{ isset($invoice) && $invoice->payments->count() > 0 && $invoice->payments->first()->payment_method === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                            <option value="bank_transfer" {{ isset($invoice) && $invoice->payments->count() > 0 && $invoice->payments->first()->payment_method === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                            <option value="card" {{ isset($invoice) && $invoice->payments->count() > 0 && $invoice->payments->first()->payment_method === 'card' ? 'selected' : '' }}>💳 Card</option>
                            <option value="cheque" {{ isset($invoice) && $invoice->payments->count() > 0 && $invoice->payments->first()->payment_method === 'cheque' ? 'selected' : '' }}>📋 Cheque</option>
                            <option value="online" {{ isset($invoice) && $invoice->payments->count() > 0 && $invoice->payments->first()->payment_method === 'online' ? 'selected' : '' }}>🌐 Online</option>
                        </select>
                    </div>

                    <x-form-input
                        label="Reference Number"
                        name="advance_reference"
                        :value="isset($invoice) && $invoice->payments->count() > 0 ? $invoice->payments->first()->reference_number : old('advance_reference')"
                        placeholder="Optional"
                        :required="false"
                    />
                </div>

                {{-- Advance Payment Summary --}}
                <div x-show="hasAdvance && advanceAmount > 0" x-transition class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm pb-2 border-b border-indigo-200">
                        <span class="text-slate-700 font-semibold">GRAND TOTAL:</span>
                        <span class="font-bold text-slate-900">LKR <span x-text="formatNumber(getInvoiceTotal())"></span></span>
                    </div>
                    <div class="flex justify-between text-sm text-green-600">
                        <span class="font-medium">Less: Advance Payment</span>
                        <span class="font-semibold">(LKR <span x-text="formatNumber(advanceAmount)"></span>)</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-indigo-300 font-bold">
                        <span class="text-slate-900">Balance Due:</span>
                        <span class="text-amber-600 text-base">LKR <span x-text="formatNumber(getInvoiceTotal() - advanceAmount)"></span></span>
                    </div>
                    <div class="text-xs text-indigo-700 mt-3 pt-2 border-t border-indigo-200">
                        <i class="fas fa-check-circle mr-1"></i>
                        Payment Status: <strong x-text="getAdvanceStatus()"></strong>
                    </div>
                </div>

                {{-- Warning if advance exceeds total --}}
                <div x-show="hasAdvance && advanceAmount > getInvoiceTotal()" class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-sm text-amber-900">
                        <i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i>
                        <strong>Note:</strong> Advance amount exceeds invoice total. This will create an overpayment.
                    </p>
                </div>

                {{-- Info for editing existing advance --}}
                @if(isset($invoice) && $invoice->payments->count() > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-900">
                        <i class="fas fa-pencil text-blue-600 mr-2"></i>
                        <strong>Editing existing advance payment</strong> recorded on {{ $invoice->payments->first()->payment_date->format('d M Y') }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment Schedule & History --}}
        @if(isset($invoice) && $invoice->payments->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                <div class="w-1 h-8 bg-amber-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-slate-900">Payment History</h2>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-amber-700 font-semibold">Invoice Total</p>
                        <p class="text-xl font-bold text-slate-900">LKR {{ number_format($invoice->total_amount) }}</p>
                    </div>
                    <div>
                        <p class="text-green-700 font-semibold">Amount Paid</p>
                        <p class="text-xl font-bold text-green-600">LKR {{ number_format($invoice->paid_amount) }}</p>
                    </div>
                    <div>
                        <p class="text-red-700 font-semibold">Balance Remaining</p>
                        <p class="text-xl font-bold text-red-600">LKR {{ number_format($invoice->balance) }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-200 bg-slate-50">
                            <th class="px-4 py-3 text-left font-bold text-slate-700">Date</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-700">Method</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-700">Reference</th>
                            <th class="px-4 py-3 text-right font-bold text-slate-700">Amount</th>
                            <th class="px-4 py-3 text-right font-bold text-slate-700">Balance</th>
                            <th class="px-4 py-3 text-center font-bold text-slate-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $runningBalance = (float)$invoice->total_amount;
                            $payments = $invoice->payments->sortBy('payment_date');
                        @endphp
                        @foreach($payments as $pmt)
                        @php $runningBalance -= (float)$pmt->amount; @endphp
                        <tr class="border-b border-slate-200 hover:bg-amber-50 transition">
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $pmt->payment_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 capitalize text-slate-600">
                                @php
                                    $methods = ['cash' => '💵 Cash', 'bank_transfer' => '🏦 Bank Transfer', 'card' => '💳 Card', 'cheque' => '📋 Cheque', 'online' => '🌐 Online'];
                                @endphp
                                {{ $methods[$pmt->payment_method] ?? ucfirst(str_replace('_', ' ', $pmt->payment_method)) }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $pmt->reference_number ?: '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">LKR {{ number_format($pmt->amount) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-amber-700">LKR {{ number_format($runningBalance) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    ✓ Paid
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($invoice->balance > 0)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <strong>Remaining Balance:</strong> LKR {{ number_format($invoice->balance) }} is due
                </p>
            </div>
            @endif
        </div>
        @endif

        {{-- Payment Plan / Scheduled Payments --}}
        @if(isset($invoice) && $invoice)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs" x-data="paymentPlan()">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                <div class="w-1 h-8 bg-blue-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-slate-900">Payment Schedule / Plan</h2>
            </div>

            {{-- Existing Payment Schedule --}}
            @if(isset($invoice) && $invoice->paymentSchedules->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4">📋 Payment Steps</h3>
                <div class="space-y-3">
                    @php
                        $totalAmount = $invoice->total_amount;
                        $paidSoFar = 0;
                    @endphp
                    @foreach($invoice->paymentSchedules->sortBy('step_number') as $step)
                    @php
                        $paidSoFar += (float)$step->amount;
                        $balanceRemaining = $totalAmount - $paidSoFar;
                    @endphp
                    <div class="flex items-center gap-4 p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $step->isPaid() ? 'bg-green-100' : ($step->isOverdue() ? 'bg-red-100' : 'bg-blue-100') }} flex items-center justify-center font-bold {{ $step->isPaid() ? 'text-green-700' : ($step->isOverdue() ? 'text-red-700' : 'text-blue-700') }}">
                                {{ $step->step_number }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-slate-900">Step {{ $step->step_number }}</p>
                                    <p class="text-sm text-slate-600">
                                        Due: <strong>{{ $step->due_date->format('d M Y') }}</strong>
                                        @if($step->isOverdue() && !$step->isPaid())
                                        <span class="ml-2 text-red-600 font-semibold">⚠ OVERDUE</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-slate-900">LKR {{ number_format($step->amount) }}</p>
                                    <p class="text-xs text-amber-600 font-semibold mb-1">Balance: LKR {{ number_format($balanceRemaining) }}</p>
                                    <p class="text-xs {{ $step->isPaid() ? 'text-green-600' : 'text-blue-600' }} font-semibold">
                                        {{ $step->status === 'paid' ? '✓ Paid on ' . $step->payment_date->format('d M Y') : ucfirst($step->status) }}
                                    </p>
                                </div>
                            </div>
                            @if($step->notes)
                            <p class="text-xs text-slate-500 mt-2">{{ $step->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Add New Step --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-900">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                    <strong>Add Payment Step:</strong> Create payment schedule steps for installments
                </p>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Step Number</label>
                        <input type="number" x-model.number="stepNumber" min="1"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                            placeholder="1, 2, 3...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Due Date</label>
                        <input type="date" x-model="dueDate"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                            :value="nextDueDate()">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Amount (LKR)</label>
                        <input type="number" x-model.number="stepAmount" step="0.01" min="0"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Notes (Optional)</label>
                        <input type="text" x-model="stepNotes"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition focus:outline-none"
                            placeholder="e.g., 50% payment">
                    </div>
                </div>
                <button type="button" @click="addStep()"
                    class="w-full px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add Step
                </button>
            </div>

            {{-- Hidden inputs for steps --}}
            <template x-for="(step, index) in scheduleSteps" :key="index">
                <input type="hidden" :name="`schedule_steps[${index}][step_number]`" :value="step.step_number">
                <input type="hidden" :name="`schedule_steps[${index}][due_date]`" :value="step.due_date">
                <input type="hidden" :name="`schedule_steps[${index}][amount]`" :value="step.amount">
                <input type="hidden" :name="`schedule_steps[${index}][notes]`" :value="step.notes">
            </template>
        </div>
        @endif

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
    // Invoice form Alpine component
    function invoiceForm() {
        return {
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
        }
    }

    // Advance Payment Alpine component
    function advancePayment() {
        return {
            hasAdvance: false,
            advanceAmount: 0,
            advanceMethod: '',

            getInvoiceTotal() {
                const form = document.querySelector('form');
                const manualTotal = parseFloat(form?.querySelector('[name="manual_total"]')?.value) || 0;
                const taxAmount = parseFloat(form?.querySelector('[name="tax_amount"]')?.value) || 0;

                // Calculate from items if manual total is not set
                if (manualTotal > 0) return manualTotal;

                let total = taxAmount;
                const itemElements = form?.querySelectorAll('[name*="[total]"]') || [];
                itemElements.forEach(el => {
                    total += parseFloat(el.value) || 0;
                });

                return total;
            },

            getAdvanceStatus() {
                const total = this.getInvoiceTotal();
                if (this.advanceAmount <= 0) return 'No payment';
                if (this.advanceAmount >= total) return 'Fully Paid';
                return 'Partially Paid';
            },

            updateAdvanceInfo() {
                // Validation updates in real-time
                this.$watch('advanceAmount', (val) => {
                    // Amount is validated dynamically
                });
            },

            formatNumber(num) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(num || 0);
            }
        }
    }

    // Payment Plan Alpine component
    function paymentPlan() {
        return {
            scheduleSteps: [],
            stepNumber: 2,
            stepAmount: 0,
            dueDate: '',
            stepNotes: '',

            nextDueDate() {
                // Default to 14 days from now
                const date = new Date();
                date.setDate(date.getDate() + 14);
                return date.toISOString().split('T')[0];
            },

            addStep() {
                if (!this.stepNumber || !this.dueDate || !this.stepAmount) {
                    alert('Please fill in all required fields');
                    return;
                }

                this.scheduleSteps.push({
                    step_number: this.stepNumber,
                    due_date: this.dueDate,
                    amount: this.stepAmount,
                    notes: this.stepNotes || ''
                });

                // Reset form
                this.stepNumber = this.scheduleSteps.length + 2;
                this.stepAmount = 0;
                this.dueDate = '';
                this.stepNotes = '';
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('invoiceForm', invoiceForm);
        Alpine.data('advancePayment', advancePayment);
        Alpine.data('paymentPlan', paymentPlan);
    });
</script>
@endpush
