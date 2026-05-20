@php
    $defaultItems = [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'warranty' => '', 'total' => 0]];
    $formItems = old('items', $quotation
        ? $quotation->items->map(fn($i) => [
            'description' => $i->description,
            'quantity'    => $i->quantity,
            'unit_price'  => $i->unit_price,
            'warranty'    => $i->warranty ?? '',
            'total'       => $i->total,
          ])->toArray()
        : $defaultItems);

    $defaultWarrantyTerms = "Software Warranty (Lifetime Warranty for POS System)\nThe software provided with the POS system includes a lifetime warranty.\nCoverage:\n● Covers any bugs, defects, or malfunctions in the software\n● Includes lifetime updates and technical support\nExclusions:\n● Issues caused by unauthorized modifications\n● Problems arising from third-party software integrations\n● Misuse or improper handling of the system\n\nHardware Warranty (1 Year)\nAll hardware components of the POS system are covered under a 1-year warranty.\nThis includes:\n● PC-Full Set\n● Cash Drawer\n● Thermal Receipt Printer\n● Desktop Barcode Scanner\n\nLimitations of Hardware Warranty\nThe hardware warranty does not cover:\n● Physical damage caused by accidents, misuse, or neglect.\n● Damage due to unauthorized repairs, modifications, or tampering.\n● Consumable items such as batteries, printer ribbons, and thermal paper.\n● Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).\n\nWarranty Claims\n● Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.\n● Defective hardware must be returned to an authorized service center for inspection.\n● Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.\n\nService Terms\n● Lifetime software support will be provided either remotely or on-site, depending on the situation.\n● Hardware repair or replacement is free within the 1-year warranty period.\nAfter the 1-year warranty period:\n● Repair services will be chargeable\n● Replacement parts will be provided at current market prices\n\nExclusions and Conditions\n● Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty\n● Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.";

    // Normalize stored entries to [{kind, text}] — handles both old string[] and new object[] formats
    $normalizeEntries = fn(array $arr) => array_values(array_map(
        fn($e) => is_array($e) && isset($e['kind'])
            ? ['kind' => $e['kind'], 'text' => (string)($e['text'] ?? '')]
            : ['kind' => 'item', 'text' => (string)$e],
        $arr
    ));

    $rawFeatures = old('software_features', $quotation?->software_features ?? ($defaultFeatures ?? []));
    $formFeatures = $normalizeEntries(is_array($rawFeatures) ? $rawFeatures : []);

    $formTerms = old('terms_conditions', $quotation?->terms_conditions ?? '');
    $isNewQuotation = !$quotation && !old('quote_type');

    $normalizeItems = fn(array $arr) => array_values(array_map(
        fn($i) => [
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
        'terms'    => $t->terms_conditions    ?? '',
        'overview' => $t->project_overview    ?? '',
    ])->values()->all();
@endphp
<script>
const _tplData = @json($tplData);
const quotationTypeDefaults = Object.fromEntries(
    _tplData.map(t => [t.key, { items: t.items, features: t.features, terms: t.terms, overview: t.overview }])
);

function quotationForm() {
    const items = @json($formItems);

    return {
        items: items,
        features: @json($formFeatures),
        termsText: @json($formTerms),
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
            if (!this.items.length) { this.items = [{ description: '', quantity: 1, unit_price: 0, warranty: '', total: 0 }]; }
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
                    const ok = confirm('Changing the template will reset Hardware/services, Software Features and Terms & Conditions to the template defaults. Continue?');
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
                this.items = d.items.map(i => ({ ...i }));
            } else {
                this.items = [{ description: '', quantity: 1, unit_price: 0, warranty: '', total: 0 }];
            }
            this.features  = (d.features || []).map(f => ({ ...f }));
            this.termsText = d.terms || '';
            this.projectOverview = d.overview || '';
            this.calcTotal();
        },

        addItem()         { this.items.push({ description: '', quantity: 1, unit_price: 0, warranty: '', total: 0 }); },
        removeItem(i)     { this.items.splice(i, 1); this.calcTotal(); },

        addFromCatalog(name, desc, price, warranty) {
            const description = desc ? name + '\n• ' + desc : name;
            const unitPrice = parseFloat(price) || 0;
            this.items.push({ description, quantity: 1, unit_price: unitPrice, warranty: warranty || '', total: unitPrice });
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
