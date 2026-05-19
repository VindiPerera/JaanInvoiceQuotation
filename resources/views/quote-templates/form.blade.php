@extends('layouts.app')
@section('title', $template ? 'Edit Template' : 'New Template')
@section('breadcrumb', $template ? 'Edit: ' . $template->name : 'Create a new quote template')

@section('content')

@php
    $normalizeEntries = fn(array $arr) => array_values(array_map(
        fn($e) => is_array($e) && isset($e['kind'])
            ? ['kind' => $e['kind'], 'text' => (string)($e['text'] ?? '')]
            : ['kind' => 'item', 'text' => (string)$e],
        $arr
    ));

    $rawFeatures  = old('software_features',   $template?->software_features   ?? []);
    $rawBenefits  = old('additional_benefits', $template?->additional_benefits ?? []);
    $formFeatures = $normalizeEntries(is_array($rawFeatures) ? $rawFeatures : []);
    $formBenefits = $normalizeEntries(is_array($rawBenefits) ? $rawBenefits : []);

    $rawItems   = old('hardware_items', $template?->hardware_items ?? []);
    $formItems  = is_array($rawItems) ? array_map(fn($i) => [
        'description' => $i['description'] ?? '',
        'quantity'    => (float)($i['quantity']   ?? 1),
        'unit_price'  => (float)($i['unit_price']  ?? 0),
        'total'       => (float)($i['quantity'] ?? 1) * (float)($i['unit_price'] ?? 0),
    ], $rawItems) : [];
    if (empty($formItems)) {
        $formItems = [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'total' => 0]];
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

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Template Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Template Name *</label>
                    <input type="text" name="name" value="{{ old('name', $template?->name) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="e.g. MariaPOS PC Full Set" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Key * <span class="text-gray-400 font-normal">(unique slug, no spaces)</span>
                    </label>
                    <input type="text" name="key" value="{{ old('key', $template?->key) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="e.g. full_set" {{ $template ? 'readonly' : '' }} required>
                    @if($template)
                    <p class="text-xs text-gray-400 mt-1">Key cannot be changed after creation.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Icon <span class="text-gray-400 font-normal">(Font Awesome class)</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="icon" x-model="iconClass"
                            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                            placeholder="fa-desktop">
                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-gray-200">
                            <i class="fa-solid text-red-600" :class="iconClass || 'fa-file-alt'"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">e.g. fa-desktop, fa-code, fa-microchip</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $template?->subtitle) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="e.g. Hardware + Software">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $template?->sort_order ?? 0) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        min="0">
                </div>
            </div>
        </div>

        {{-- Hardware Package Items --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Hardware Package Items</h2>
                <div class="flex items-center gap-2">
                    {{-- Catalog picker --}}
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
                                        @click="addItemFromCatalog({ description: {{ Js::from($ci->description ?: $ci->name) }}, unit_price: {{ (float)$ci->unit_price }} }); open = false;"
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
                    <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
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
                                        placeholder="ITEM NAME (first line bold in PDF)&#10;• Sub-detail one&#10;• Sub-detail two"></textarea>
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
                <h2 class="text-base font-semibold text-gray-800">Software Features</h2>
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
                                <button type="button" @click="features.splice(idx,1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                        <template x-if="feat.kind === 'heading'">
                            <div class="flex items-center gap-2 py-0.5">
                                <i class="fa-solid fa-minus text-gray-400 text-xs shrink-0 w-4 text-center"></i>
                                <input type="text" :name="`software_features[${idx}][text]`" x-model="feat.text"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Section heading (e.g. 1. Software Warranty:)">
                                <button type="button" @click="features.splice(idx,1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                        <template x-if="feat.kind === 'item'">
                            <div class="flex items-center gap-2 py-0.5">
                                <i class="fa-solid fa-check text-red-500 text-xs shrink-0 w-4 text-center"></i>
                                <input type="text" :name="`software_features[${idx}][text]`" x-model="feat.text"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Feature description">
                                <button type="button" @click="features.splice(idx,1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                    </div>
                </template>
                <p x-show="features.length === 0" class="text-sm text-gray-400 text-center py-4">No features added yet.</p>
            </div>
        </div>

        {{-- Additional Benefits --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Additional Benefits</h2>
                <div class="flex items-center gap-2">
                    <button type="button" @click="addBenefit('heading')"
                        class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 border border-gray-200 rounded-md px-2 py-1 transition">
                        <i class="fa-solid fa-heading text-xs"></i> Heading
                    </button>
                    <button type="button" @click="addBenefit('space')"
                        class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 border border-gray-200 rounded-md px-2 py-1 transition">
                        <i class="fa-solid fa-grip-lines text-xs"></i> Space
                    </button>
                    <button type="button" @click="addBenefit('item')"
                        class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-medium">
                        <i class="fa-solid fa-plus"></i> Add Benefit
                    </button>
                </div>
            </div>
            <div class="space-y-1">
                <template x-for="(ben, idx) in benefits" :key="idx">
                    <div>
                        <input type="hidden" :name="`additional_benefits[${idx}][kind]`" :value="ben.kind">
                        <template x-if="ben.kind === 'space'">
                            <div class="flex items-center gap-2 py-2">
                                <i class="fa-solid fa-grip-lines text-gray-300 text-xs shrink-0"></i>
                                <div class="flex-1 border-t border-dashed border-gray-200"></div>
                                <input type="hidden" :name="`additional_benefits[${idx}][text]`" value="">
                                <button type="button" @click="benefits.splice(idx,1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                        <template x-if="ben.kind === 'heading'">
                            <div class="flex items-center gap-2 py-0.5">
                                <i class="fa-solid fa-minus text-gray-400 text-xs shrink-0 w-4 text-center"></i>
                                <input type="text" :name="`additional_benefits[${idx}][text]`" x-model="ben.text"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Section heading (e.g. 2. Service Terms:)">
                                <button type="button" @click="benefits.splice(idx,1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                        <template x-if="ben.kind === 'item'">
                            <div class="flex items-center gap-2 py-0.5">
                                <i class="fa-solid fa-circle text-red-400 text-xs shrink-0 w-4 text-center"></i>
                                <input type="text" :name="`additional_benefits[${idx}][text]`" x-model="ben.text"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
                                    placeholder="Benefit description">
                                <button type="button" @click="benefits.splice(idx,1)" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-xmark text-xs"></i></button>
                            </div>
                        </template>
                    </div>
                </template>
                <p x-show="benefits.length === 0" class="text-sm text-gray-400 text-center py-4">No benefits added yet.</p>
            </div>
        </div>

        {{-- Terms --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-3">Terms & Conditions</h2>
            <textarea name="terms_conditions" rows="12"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 font-mono">{{ old('terms_conditions', $template?->terms_conditions) }}</textarea>
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
        iconClass: '{{ old('icon', $template?->icon ?? 'fa-file-alt') }}',
        items:    @json($formItems),
        features: @json($formFeatures),
        benefits: @json($formBenefits),
        subtotal: 0,

        init() {
            this.calcSubtotal();
        },

        addItem()        { this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 }); },
        addItemFromCatalog(catalog) {
            this.items.push({ description: catalog.description, quantity: 1, unit_price: catalog.unit_price, total: catalog.unit_price });
            this.calcSubtotal();
        },
        removeItem(i)    { this.items.splice(i, 1); this.calcSubtotal(); },
        addFeature(kind) { this.features.push({ kind: kind || 'item', text: '' }); },
        addBenefit(kind) { this.benefits.push({ kind: kind || 'item', text: '' }); },

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
