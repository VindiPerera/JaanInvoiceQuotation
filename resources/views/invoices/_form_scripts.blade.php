@php
    $existingItems = old('items', isset($invoice) && $invoice
        ? $invoice->items->map(fn($i) => [
            'item_name'   => $i->item_name ?? '',
            'description' => $i->description,
            'quantity'    => (float) $i->quantity,
            'unit_price'  => (float) $i->unit_price,
            'warranty'    => $i->warranty ?? '',
            'total'       => (float) $i->total,
          ])->toArray()
        : (isset($quotation) && $quotation
            ? [
                [
                    'item_name'   => $quotation->subject ?? '',
                    'description' => $quotation->subject ?? '',
                    'quantity'    => 1,
                    'unit_price'  => (float) $quotation->total_amount,
                    'warranty'    => '',
                    'total'       => (float) $quotation->total_amount,
                ]
              ]
            : []));

@endphp
<script>
function invoiceForm() {
    return {
        items: @json($existingItems),
        taxAmount: {{ old('tax_amount', isset($invoice) ? $invoice->tax_amount : (isset($quotation) ? $quotation->tax_amount : 0)) }},
        paymentStatus: '{{ old('payment_status', isset($invoice) ? $invoice->payment_status : 'pending') }}',
        subtotal: 0,
        total: 0,

        init() {
            if (!this.items.length) { this.items = [{ item_name: '', description: '', quantity: 1, unit_price: 0, warranty: '', total: 0 }]; }
            this.calcTotal();
        },

        addItem()    { this.items.push({ item_name: '', description: '', quantity: 1, unit_price: 0, warranty: '', total: 0 }); },
        removeItem(i){ this.items.splice(i, 1); this.calcTotal(); },

        pickHardware(i, e) {
            const opt = e.target.selectedOptions[0];
            if (!opt.value) return;
            this.items[i].item_name  = opt.dataset.name || '';
            this.items[i].description = opt.dataset.desc || '';
            this.items[i].unit_price  = parseFloat(opt.dataset.price) || 0;
            this.items[i].warranty    = opt.dataset.warranty || '';
            this.calcRow(i);
            e.target.value = '';
        },

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
            document.getElementById('inv_customer_name').value = opt.text === '— Select customer —' ? '' : opt.text;
            document.getElementById('inv_customer_address').value = opt.dataset.address || '';
            document.getElementById('inv_customer_contact').value = opt.dataset.contact || '';
        },
    };
}
</script>
