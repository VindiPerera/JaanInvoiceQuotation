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
@endphp
<script>
const quotationTypeDefaults = {
    full_set: {
        benefits: [
            { kind: 'heading', text: '1. Software Warranty (Lifetime):' },
            { kind: 'item', text: 'Lifetime free software updates & bug fixes' },
            { kind: 'item', text: 'Lifetime remote or on-site technical support' },
            { kind: 'space', text: '' },
            { kind: 'heading', text: '2. Hardware Warranty (1 Year):' },
            { kind: 'item', text: '1 Year warranty on all hardware components' },
            { kind: 'item', text: 'Free repair or replacement for defective parts within warranty' },
            { kind: 'space', text: '' },
            { kind: 'heading', text: '3. Installation & Training:' },
            { kind: 'item', text: 'Free on-site installation and system configuration' },
            { kind: 'item', text: 'Staff training on system usage included' },
        ],
        terms: `SOFTWARE WARRANTY (Lifetime Warranty for POS System)\nThe software provided with the POS system includes a lifetime warranty.\n\nCoverage:\n• Covers any bugs, defects, or malfunctions in the software\n• Includes lifetime updates and technical support\n\nExclusions:\n• Issues caused by unauthorized modifications\n• Problems arising from third-party software integrations\n• Misuse or improper handling of the system\n\nHARDWARE WARRANTY (1 Year)\nAll hardware components of the POS system are covered under a 1-year warranty from the date of purchase.\n\nThis includes:\n• PC-Full Set\n• Cash Drawer\n• Xprinter – XP – 237B\n• Desktop Barcode Scanner\n\nLIMITATIONS OF HARDWARE WARRANTY\nThe hardware warranty does not cover:\n• Physical damage caused by accidents, misuse, or neglect.\n• Damage due to unauthorized repairs, modifications, or tampering.\n• Consumable items such as batteries, printer ribbons, and thermal paper.\n• Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).\n\nWARRANTY CLAIMS\n• Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.\n• Defective hardware must be returned to an authorized service center for inspection.\n• Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.\n\nSERVICE TERMS\n• Lifetime software support will be provided either remotely or on-site, depending on the situation.\n• Hardware repair or replacement is free within the 1-year warranty period.\n\nAfter the 1-year warranty period:\n• Repair services will be chargeable\n• Replacement parts will be provided at current market prices\n\nEXCLUSIONS AND CONDITIONS\n• Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty.\n• Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.`,
    },
    software_only: {
        benefits: [
            { kind: 'heading', text: '1. Software Warranty (Lifetime):' },
            { kind: 'item', text: 'Lifetime free software updates & bug fixes' },
            { kind: 'item', text: 'Lifetime remote or on-site technical support' },
            { kind: 'item', text: 'Cloud backup support (optional add-on)' },
            { kind: 'space', text: '' },
            { kind: 'heading', text: '2. Training & Onboarding:' },
            { kind: 'item', text: 'Online/remote training session included' },
            { kind: 'item', text: 'User manual and documentation provided' },
        ],
        terms: `SOFTWARE WARRANTY (Lifetime Warranty for POS System)\nThe software provided with the POS system includes a lifetime warranty.\n\nCoverage:\n• Covers any bugs, defects, or malfunctions in the software\n• Includes lifetime updates and technical support\n\nExclusions:\n• Issues caused by unauthorized modifications\n• Problems arising from third-party software integrations\n• Misuse or improper handling of the system\n\nSERVICE TERMS\n• Lifetime software support will be provided either remotely or on-site, depending on the situation.\n\nEXCLUSIONS AND CONDITIONS\n• Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty.\n• Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.`,
    },
    hardware_only: {
        benefits: [
            { kind: 'heading', text: '1. Hardware Warranty (1 Year):' },
            { kind: 'item', text: '1 Year warranty on all hardware components' },
            { kind: 'item', text: 'Free repair or replacement for defective parts within warranty' },
            { kind: 'space', text: '' },
            { kind: 'heading', text: '2. Delivery & Setup:' },
            { kind: 'item', text: 'Free delivery within city limits' },
            { kind: 'item', text: 'Hardware setup and basic configuration included' },
        ],
        terms: `HARDWARE WARRANTY (1 Year)\nAll hardware components are covered under a 1-year warranty from the date of purchase.\n\nThis includes:\n• PC-Full Set\n• Cash Drawer\n• Xprinter – XP – 237B\n• Desktop Barcode Scanner\n\nLIMITATIONS OF HARDWARE WARRANTY\nThe hardware warranty does not cover:\n• Physical damage caused by accidents, misuse, or neglect.\n• Damage due to unauthorized repairs, modifications, or tampering.\n• Consumable items such as batteries, printer ribbons, and thermal paper.\n• Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).\n\nWARRANTY CLAIMS\n• Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.\n• Defective hardware must be returned to an authorized service center for inspection.\n• Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.\n\nSERVICE TERMS\n• Hardware repair or replacement is free within the 1-year warranty period.\n\nAfter the 1-year warranty period:\n• Repair services will be chargeable\n• Replacement parts will be provided at current market prices\n\nEXCLUSIONS AND CONDITIONS\n• Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty.\n• Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.`,
    },
};

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
                    const ok = confirm('Changing the quote type will reset Additional Benefits and Terms & Conditions to the template defaults. Continue?');
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
            const d = quotationTypeDefaults[type] || quotationTypeDefaults.full_set;
            this.benefits = d.benefits.map(b => ({ ...b }));
            this.termsText = d.terms;
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
