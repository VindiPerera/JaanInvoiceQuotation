<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$template = App\Models\QuoteTemplate::where('key', 'maria_full_set')->first();
if (!$template) {
    echo 'Template not found';
    exit;
}

$quotation = App\Models\Quotation::create([
    'quotation_number' => 'QT-TEST-01',
    'quotation_date' => now()->toDateString(),
    'customer_id' => null,
    'customer_name' => 'Test Customer',
    'customer_address' => 'Test Address',
    'customer_contact' => 'Test Contact',
    'subject' => 'Test Subject',
    'project_overview' => 'Test Overview',
    'quote_type' => 'full_set',
    'software_features' => [],
    'additional_benefits' => [],
    'tax_amount' => 0,
    'status' => 'draft',
]);

$subtotal = 0;
$items = $template->hardware_items ?? [];
foreach ($items as $i => $item) {
    if (empty($item['description'])) continue;
    $total = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
    $description = $item['description'];
    $lines = array_filter(array_map('trim', explode(PHP_EOL, $description)));
    $itemName = count($lines) > 0 ? reset($lines) : 'Item';

    App\Models\QuotationItem::create([
        'quotation_id' => $quotation->id,
        'item_number' => $i + 1,
        'item_name' => $itemName,
        'description' => $description,
        'quantity' => $item['quantity'] ?? 1,
        'unit_price' => $item['unit_price'] ?? 0,
        'warranty' => $item['warranty'] ?? null,
        'total' => $total,
        'item_type' => 'hardware',
    ]);
    $subtotal += $total;
}

$quotation->update([
    'subtotal' => $subtotal,
    'total_amount' => $subtotal,
]);

echo 'Quotation created: ' . $quotation->quotation_number . ' with ' . $quotation->items()->count() . ' items' . PHP_EOL;
