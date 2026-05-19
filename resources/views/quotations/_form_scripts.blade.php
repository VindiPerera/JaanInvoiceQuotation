@php
    $defaultItems = [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'total' => 0]];
    $formItems = old('items', $quotation
        ? $quotation->items->map(fn($i) => [
            'description' => $i->description,
            'quantity'    => $i->quantity,
            'unit_price'  => $i->unit_price,
            'total'       => $i->total,
          ])->toArray()
        : $defaultItems);

    // Normalize stored entries to [{kind, text}] — handles both old string[] and new object[] formats
    $normalizeEntries = fn(array $arr) => array_values(array_map(
        fn($e) => is_array($e) && isset($e['kind'])
            ? ['kind' => $e['kind'], 'text' => (string)($e['text'] ?? '')]
            : ['kind' => 'item', 'text' => (string)$e],
        $arr
    ));

    $rawFeatures = old('software_features', $quotation?->software_features ?? ($defaultFeatures ?? []));
    $formFeatures = $normalizeEntries(is_array($rawFeatures) ? $rawFeatures : []);

    $rawBenefits = old('additional_benefits', $quotation?->additional_benefits ?? ($defaultBenefits ?? []));
    $formBenefits = $normalizeEntries(is_array($rawBenefits) ? $rawBenefits : []);

    $formTerms = old('terms_conditions', $quotation?->terms_conditions ?? $defaultTerms ?? '');
    $isNewQuotation = !$quotation && !old('quote_type');

    $normalizeItems = fn(array $arr) => array_values(array_map(
        fn($i) => [
            'description' => $i['description'] ?? '',
            'quantity'    => (float)($i['quantity']  ?? 1),
            'unit_price'  => (float)($i['unit_price'] ?? 0),
            'total'       => (float)($i['quantity'] ?? 1) * (float)($i['unit_price'] ?? 0),
        ],
        array_filter($arr, fn($i) => !empty(trim($i['description'] ?? '')))
    ));

    $tplData = ($templates ?? collect())->map(fn($t) => [
        'key'      => $t->key,
        'items'    => $normalizeItems($t->hardware_items    ?? []),
        'features' => $t->software_features   ?? [],
        'benefits' => $t->additional_benefits ?? [],
        'terms'    => $t->terms_conditions    ?? '',
    ])->values()->all();
@endphp
<script>
const _tplData = @json($tplData);
const quotationTypeDefaults = Object.fromEntries(
    _tplData.map(t => [t.key, { items: t.items, features: t.features, benefits: t.benefits, terms: t.terms }])
);

function quotationForm() {
    const items = @json($formItems);

    return {
        items: items,
        features: @json($formFeatures),
        benefits: @json($formBenefits),
        termsText: @json($formTerms),
        taxAmount: {{ old('tax_amount', $quotation?->tax_amount ?? 0) }},
        quoteType: '{{ old('quote_type', $quotation?->quote_type ?? 'full_set') }}',
        isNewQuotation: {{ $isNewQuotation ? 'true' : 'false' }},
        _skipTypeWatch: false,
        subtotal: 0,
        total: 0,

        init() {
            if (!this.items.length) { this.items = [{ description: '', quantity: 1, unit_price: 0, total: 0 }]; }
            this.calcTotal();

            if (this.isNewQuotation) {
                this.applyTypeDefaults(this.quoteType);
            }

            this.$watch('quoteType', (newType, oldType) => {
                if (this._skipTypeWatch) { this._skipTypeWatch = false; return; }
                if (!this.isNewQuotation) {
                    const ok = confirm('Changing the template will reset Hardware Items, Software Features, Additional Benefits and Terms & Conditions to the template defaults. Continue?');
                    if (!ok) {
                        this._skipTypeWatch = true;
                        this.quoteType = oldType;
                        return;
                    }
                }
                this.applyTypeDefaults(newType);
            });
        },

        applyTypeDefaults(type) {
            const keys = Object.keys(quotationTypeDefaults);
            const d = quotationTypeDefaults[type] || quotationTypeDefaults[keys[0]] || { items: [], features: [], benefits: [], terms: '' };
            if (d.items && d.items.length) {
                this.items = d.items.map(i => ({ ...i }));
            } else {
                this.items = [{ description: '', quantity: 1, unit_price: 0, total: 0 }];
            }
            this.features  = (d.features || []).map(f => ({ ...f }));
            this.benefits  = (d.benefits || []).map(b => ({ ...b }));
            this.termsText = d.terms || '';
            this.calcTotal();
        },

        addItem()         { this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 }); },
        removeItem(i)     { this.items.splice(i, 1); this.calcTotal(); },
        addFeature(kind)  { this.features.push({ kind: kind || 'item', text: '' }); },
        addBenefit(kind)  { this.benefits.push({ kind: kind || 'item', text: '' }); },

        calcRow(i) {
            this.items[i].total = (this.items[i].quantity || 0) * (this.items[i].unit_price || 0);
            this.calcTotal();
        },
        calcTotal() {
            this.subtotal = this.items.reduce((s, r) => s + (r.total || 0), 0);
            this.total = this.subtotal + (this.taxAmount || 0);
        },
        formatNum(v) {
            return Number(v || 0).toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        fillCustomer(e) {
            const opt = e.target.selectedOptions[0];
            document.getElementById('customer_name').value = opt.text === '— Select customer —' ? '' : opt.text;
            document.getElementById('customer_address').value = opt.dataset.address || '';
            document.getElementById('customer_contact').value = opt.dataset.contact || '';
        },
    };
}
</script>
