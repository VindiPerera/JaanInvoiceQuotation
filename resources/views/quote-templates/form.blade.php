@extends('layouts.app')
@section('title', $template ? 'Edit Template' : 'New Template')
@section('breadcrumb', $template ? 'Edit: ' . $template->name : 'Create a new quote template')

@section('content')

@php
    $defaultWarrantyTerms = "Software Warranty (Lifetime Warranty for POS System)\nThe software provided with the POS system includes a lifetime warranty.\nCoverage:\n● Covers any bugs, defects, or malfunctions in the software\n● Includes lifetime updates and technical support\nExclusions:\n● Issues caused by unauthorized modifications\n● Problems arising from third-party software integrations\n● Misuse or improper handling of the system\n\nHardware Warranty (1 Year)\nAll hardware components of the POS system are covered under a 1-year warranty.\nThis includes:\n● PC-Full Set\n● Cash Drawer\n● Thermal Receipt Printer\n● Desktop Barcode Scanner\n\nLimitations of Hardware Warranty\nThe hardware warranty does not cover:\n● Physical damage caused by accidents, misuse, or neglect.\n● Damage due to unauthorized repairs, modifications, or tampering.\n● Consumable items such as batteries, printer ribbons, and thermal paper.\n● Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).\n\nWarranty Claims\n● Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.\n● Defective hardware must be returned to an authorized service center for inspection.\n● Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.\n\nService Terms\n● Lifetime software support will be provided either remotely or on-site, depending on the situation.\n● Hardware repair or replacement is free within the 1-year warranty period.\nAfter the 1-year warranty period:\n● Repair services will be chargeable\n● Replacement parts will be provided at current market prices\n\nExclusions and Conditions\n● Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty\n● Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.";

    $normalizeEntries = fn(array $arr) => array_values(array_map(
        fn($e) => is_array($e) && isset($e['kind'])
            ? ['kind' => $e['kind'], 'text' => (string)($e['text'] ?? '')]
            : ['kind' => 'item', 'text' => (string)$e],
        $arr
    ));

    $rawFeatures  = old('software_features', $template?->software_features ?? []);
    $rawFeatures  = is_string($rawFeatures) ? json_decode($rawFeatures, true) ?? [] : $rawFeatures;
    $formFeatures = $normalizeEntries(is_array($rawFeatures) ? $rawFeatures : []);

    $rawItems  = old('hardware_items', $template?->hardware_items ?? []);
    $rawItems  = is_string($rawItems) ? json_decode($rawItems, true) ?? [] : $rawItems;
    $formItems = is_array($rawItems) ? array_map(fn($i) => [
        'description' => $i['description'] ?? '',
        'quantity'    => (float)($i['quantity']  ?? 1),
        'unit_price'  => (float)($i['unit_price'] ?? 0),
        'warranty'    => $i['warranty'] ?? '',
        'total'       => (float)($i['quantity'] ?? 1) * (float)($i['unit_price'] ?? 0),
    ], $rawItems) : [];
    if (empty($formItems)) {
        $formItems = [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'warranty' => '', 'total' => 0]];
    }
@endphp

<form method="POST"
      action="{{ $template ? route('quote-templates.update', $template) : route('quote-templates.store') }}"
      x-data="templateForm()">
    @csrf
    @if($template) @method('PUT') @endif

    <div class="space-y-6 max-w-5xl">

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Template Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Template Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tpl_name" class="block text-xs font-medium text-gray-500 mb-1">Template Name *</label>
                    <input type="text" id="tpl_name" name="name" value="{{ old('name', $template?->name) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="e.g. MariaPOS PC Full Set" required>
                </div>
                <div>
                    <label for="tpl_subtitle" class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
                    <input type="text" id="tpl_subtitle" name="subtitle" value="{{ old('subtitle', $template?->subtitle) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="e.g. Hardware + Software">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">Project Overview <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea name="project_overview" rows="4"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                    placeholder="Enter default project overview/introduction text for this template...">{{ old('project_overview', $template?->project_overview ?? '') }}</textarea>
            </div>
        </div>

        {{-- Hardware/Software Package Items --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Hardware/Software Items</h2>
                <div class="flex items-center gap-2">
                    @if($hardwareCatalog->isNotEmpty())
                    <div class="relative" x-data="{ open: false, search: '' }">
                        <button type="button" @click="open = !open; search = ''"
                            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 bg-white transition">
                            <i class="fa-solid fa-database text-xs text-gray-400"></i> From Catalog
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             class="absolute right-0 top-full mt-1 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input type="text" x-model="search" placeholder="Search catalog…"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-300"
                                    @click.stop>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @php $grouped = $hardwareCatalog->groupBy('category'); @endphp
                                @foreach($grouped as $cat => $catItems)
                                @php $catSearchStr = strtolower($catItems->map(fn($i) => $i->name . ' ' . ($i->description ?? ''))->implode(' ')); @endphp
                                <div x-show="{{ e(json_encode($catSearchStr)) }}.includes(search.toLowerCase())">
                                    <p class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $cat ?: 'General' }}</p>
                                    @foreach($catItems as $ci)
                                    @php $itemSearchStr = strtolower($ci->name . ' ' . ($ci->description ?? '')); @endphp
                                    <button type="button"
                                        x-show="{{ e(json_encode($itemSearchStr)) }}.includes(search.toLowerCase())"
                                        @click="addItemFromCatalog({ description: {{ Js::from($ci->description ?: $ci->name) }}, unit_price: {{ (float)$ci->unit_price }}, warranty: {{ Js::from($ci->warranty ?? '') }} }); open = false;"
                                        class="w-full text-left px-3 py-2 hover:bg-red-50 transition flex items-center justify-between gap-2 group">
                                        <span class="text-sm text-gray-700 group-hover:text-red-700 truncate">{{ $ci->name }}</span>
                                        <span class="text-xs text-gray-400 shrink-0">LKR {{ number_format($ci->unit_price, 2) }}</span>
                                    </button>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    <button type="button" @click="addItem"
                        class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
                        <i class="fa-solid fa-plus"></i> Add Row
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="pb-2 text-left text-xs font-medium text-gray-500 w-10">#</th>
                            <th class="pb-2 text-left text-xs font-medium text-gray-500">Description</th>
                            <th class="pb-2 text-left text-xs font-medium text-gray-500 w-28">Warranty</th>
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
                                    <textarea :name="`hardware_items[${index}][description]`" x-model="item.description" rows="3"
                                        class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300 resize-y leading-snug"
                                        placeholder="ITEM/SERVICE NAME&#10;• Spec/Detail one&#10;• Spec/Detail two"></textarea>
                                </td>
                                <td class="py-2 pr-2">
                                    <input type="text" :name="`hardware_items[${index}][warranty]`" x-model="item.warranty"
                                        class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                        placeholder="e.g. 1 Year">
                                </td>
                                <td class="py-2 pr-2">
                                    <input type="number" :name="`hardware_items[${index}][quantity]`" x-model.number="item.quantity" @input="calcRow(index)"
                                        class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                        min="0" step="0.01">
                                </td>
                                <td class="py-2 pr-2">
                                    <input type="number" :name="`hardware_items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcRow(index)"
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
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Software Features --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Software Features <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span></h2>
                <div class="flex items-center gap-2">
                    <button type="button" @click="addFeature('heading')"
                        class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 border border-gray-200 rounded-md px-2 py-1 transition">
                        <i class="fa-solid fa-heading text-xs"></i> Heading
                    </button>
                    <button type="button" @click="addFeature('space')"
                        class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 border border-gray-200 rounded-md px-2 py-1 transition">
                        <i class="fa-solid fa-grip-lines text-xs"></i> Space
                    </button>
                    <button type="button" @click="addFeature('item')"
                        class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
                        <i class="fa-solid fa-plus"></i> Add Feature
                    </button>
                </div>
            </div>
            <div class="space-y-1">
                <template x-for="(feat, idx) in features" :key="idx">
                    <div>
                        <input type="hidden" :name="`software_features[${idx}][kind]`" :value="feat.kind">
                        <template x-if="feat.kind === 'space'">
                            <div class="flex items-center gap-2 py-2">
                                <i class="fa-solid fa-grip-lines text-gray-300 text-xs shrink-0"></i>
                                <div class="flex-1 border-t border-dashed border-gray-200"></div>
                                <input type="hidden" :name="`software_features[${idx}][text]`" value="">
                                <button type="button" @click="features.splice(idx,1)" class="text-gray-300 hover:text-red-500">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>
                        <template x-if="feat.kind === 'heading'">
                            <div class="flex items-center gap-2 py-0.5">
                                <i class="fa-solid fa-minus text-gray-400 text-xs shrink-0 w-4 text-center"></i>
                                <input type="text" :name="`software_features[${idx}][text]`" x-model="feat.text"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Section heading (e.g. 1. Software Warranty:)">
                                <button type="button" @click="features.splice(idx,1)" class="text-gray-300 hover:text-red-500">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>
                        <template x-if="feat.kind === 'item'">
                            <div class="flex items-center gap-2 py-0.5">
                                <i class="fa-solid fa-check text-red-500 text-xs shrink-0 w-4 text-center"></i>
                                <input type="text" :name="`software_features[${idx}][text]`" x-model="feat.text"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Feature description">
                                <button type="button" @click="features.splice(idx,1)" class="text-gray-300 hover:text-red-500">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
                <p x-show="features.length === 0" class="text-sm text-gray-400 text-center py-4">No software features added. Leave empty if not needed.</p>
            </div>
        </div>

        {{-- Terms --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-3">Terms & Conditions</h2>
            <p class="text-xs text-gray-400 mb-3">Blank line = section heading &nbsp;|&nbsp; • bullet &nbsp;|&nbsp; Content line</p>
            <textarea name="terms_conditions" rows="16"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 font-mono">{{ old('terms_conditions', $template?->terms_conditions ?? $defaultWarrantyTerms) }}</textarea>
            <p class="text-xs text-gray-500 mt-2">💡 This template's terms will be applied to all quotations using this template.</p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                {{ $template ? 'Update Template' : 'Save Template' }}
            </button>
            <a href="{{ route('quote-templates.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </div>
</form>

<script>
function templateForm() {
    return {
        items:    @json($formItems),
        features: @json($formFeatures),
        subtotal: 0,

        init() { this.calcSubtotal(); },

        addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, warranty: '', total: 0 }); },
        addItemFromCatalog(catalog) {
            this.items.push({ description: catalog.description, quantity: 1, unit_price: catalog.unit_price, warranty: catalog.warranty || '', total: catalog.unit_price });
            this.calcSubtotal();
        },
        removeItem(i) { this.items.splice(i, 1); this.calcSubtotal(); },

        addFeature(kind) { this.features.push({ kind: kind || 'item', text: '' }); },

        calcRow(i) {
            this.items[i].total = (this.items[i].quantity || 0) * (this.items[i].unit_price || 0);
            this.calcSubtotal();
        },
        calcSubtotal() {
            this.subtotal = this.items.reduce((s, r) => s + (r.total || 0), 0);
        },
        formatNum(v) {
            return Number(v || 0).toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    };
}
</script>
@endsection
