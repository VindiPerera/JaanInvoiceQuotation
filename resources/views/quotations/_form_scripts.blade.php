@php
    $defaultItems = [['item_name' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'warranty' => '', 'total' => 0]];
    $formItems = old('items', $quotation
        ? $quotation->items->map(fn($i) => [
            'item_name'   => $i->item_name ?? '',
            'description' => $i->description,
            'quantity'    => $i->quantity,
            'unit_price'  => $i->unit_price,
            'warranty'    => $i->warranty ?? '',
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

    $isNewQuotation = !$quotation && !old('quote_type');

    $normalizeItems = fn(array $arr) => array_values(array_map(
        fn($i) => [
            'item_name'   => $i['item_name'] ?? '',
            'description' => $i['description'] ?? '',
            'quantity'    => (float)($i['quantity']  ?? 1),
            'unit_price'  => (float)($i['unit_price'] ?? 0),
            'warranty'    => $i['warranty'] ?? '',
            'total'       => (float)($i['quantity'] ?? 1) * (float)($i['unit_price'] ?? 0),
        ],
        array_filter($arr, fn($i) => !empty(trim($i['description'] ?? '')))
    ));

    $tplData = ($templates ?? collect())->map(fn($t) => [
        'key'      => $t->key,
        'items'    => $normalizeItems($t->hardware_items    ?? []),
        'features' => $t->software_features   ?? [],
        'overview' => $t->project_overview    ?? '',
    ])->values()->all();
@endphp
<script>
const _tplData = @json($tplData);
const quotationTypeDefaults = Object.fromEntries(
    _tplData.map(t => [t.key, { items: t.items, features: t.features, overview: t.overview }])
);

function quotationForm() {
    const items = @json($formItems);

    return {
        items: items,
        features: @json($formFeatures),
        projectOverview: '{{ old('project_overview', $quotation?->project_overview ?? '') }}',
        taxAmount: {{ old('tax_amount', $quotation?->tax_amount ?? 0) }},
        quoteType: '{{ old('quote_type', $quotation?->quote_type ?? '') }}',
        isNewQuotation: {{ $isNewQuotation ? 'true' : 'false' }},
        isTemplateApplied: false,
        _skipTypeWatch: false,
        catalogOpen: false,
        catalogSearch: '',
        subtotal: 0,
        total: 0,

        init() {
            if (!this.items.length) { this.items = [{ item_name: '', description: '', quantity: 1, unit_price: 0, warranty: '', total: 0, isFromTemplate: false }]; }

            // Ensure all items have item_name set
            this.items = this.items.map(i => ({
                ...i,
                item_name: i.item_name || this.extractItemName(i.description),
                isFromTemplate: i.isFromTemplate !== false ? true : false
            }));

            this.calcTotal();

            if (this.isNewQuotation && this.quoteType) {
                this.applyTypeDefaults(this.quoteType);
                this.isTemplateApplied = true;
            } else if (!this.isNewQuotation && this.quoteType) {
                this.isTemplateApplied = true;
            }

            this.$watch('quoteType', (newType, oldType) => {
                if (this._skipTypeWatch) { this._skipTypeWatch = false; return; }
                if (!this.isNewQuotation) {
                    const ok = confirm('Changing the template will reset Software/Hardware/services and Software Features to the template defaults. Continue?');
                    if (!ok) {
                        this._skipTypeWatch = true;
                        this.quoteType = oldType;
                        return;
                    }
                }
                this.applyTypeDefaults(newType);
                this.isTemplateApplied = true;
            });
        },

        applyTypeDefaults(type) {
            const keys = Object.keys(quotationTypeDefaults);
            const d = quotationTypeDefaults[type] || quotationTypeDefaults[keys[0]] || { items: [], features: [], terms: '', overview: '' };
            if (d.items && d.items.length) {
                this.items = d.items.map(i => {
                    let itemName = i.item_name;
                    if (!itemName || itemName.trim() === '') {
                        itemName = this.extractItemName(i.description);
                    }
                    return { ...i, item_name: itemName, isFromTemplate: true };
                });
            } else {
                this.items = [{ item_name: '', description: '', quantity: 1, unit_price: 0, warranty: '', total: 0, isFromTemplate: false }];
            }
            this.features  = (d.features || []).map(f => ({ ...f }));
            this.termsText = d.terms || '';
            this.projectOverview = d.overview || '';
            this.calcTotal();
        },

        addItem()         { this.items.push({ item_name: '', description: '', quantity: 1, unit_price: 0, warranty: '', total: 0, isFromTemplate: false }); },

        extractItemName(description) {
            if (!description || typeof description !== 'string') return 'Item';
            const lines = description.split('\n').map(l => l.trim()).filter(l => l && !l.startsWith('•'));
            return lines.length > 0 ? lines[0] : 'Item';
        },
        removeItem(i)     { this.items.splice(i, 1); this.calcTotal(); },

        addFromCatalog(name, desc, price, warranty) {
            const description = desc ? name + '\n• ' + desc : name;
            const unitPrice = parseFloat(price) || 0;
            this.items.push({ item_name: name, description, quantity: 1, unit_price: unitPrice, warranty: warranty || '', total: unitPrice, isFromTemplate: false });
            this.calcTotal();
            this.catalogOpen = false;
            this.catalogSearch = '';
        },
        addFeature(kind)  { this.features.push({ kind: kind || 'item', text: '' }); },

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
