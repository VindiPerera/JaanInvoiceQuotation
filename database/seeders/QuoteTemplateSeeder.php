<?php

namespace Database\Seeders;

use App\Models\QuoteTemplate;
use Illuminate\Database\Seeder;

class QuoteTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Standard POS System',
                'key' => 'standard_pos_system',
                'icon' => 'fa-file-alt',
                'subtitle' => 'Complete POS system with hardware and software',
                'template_type' => 'pos_system',
                'project_overview' => 'Complete Point of Sale (POS) system implementation for retail business',
                'software_features' => json_encode([
                    ['kind' => 'heading', 'text' => 'Complete POS System with Advanced Features:'],
                    ['kind' => 'item', 'text' => 'Sales Management - Complete billing and invoicing system'],
                    ['kind' => 'item', 'text' => 'Inventory Management - Real-time stock tracking with low stock alerts'],
                    ['kind' => 'item', 'text' => 'Customer Credit Management - Track credit payments and customer balances'],
                    ['kind' => 'item', 'text' => 'Return/Exchange Processing - Handle product returns with full tracking'],
                    ['kind' => 'item', 'text' => 'Split Payment Methods - Accept multiple payment types in single transaction'],
                ], JSON_UNESCAPED_UNICODE),
                'additional_benefits' => json_encode([
                    ['kind' => 'item', 'text' => 'Full Software Demo'],
                    ['kind' => 'item', 'text' => 'Unlimited Software Customizations'],
                    ['kind' => 'item', 'text' => 'Lifetime License for JAAN POS Software'],
                ], JSON_UNESCAPED_UNICODE),
                'hardware_items' => json_encode([
                    ['name' => 'PC-Full Set', 'unit_price' => 150000],
                    ['name' => 'Cash Drawer', 'unit_price' => 25000],
                    ['name' => 'Xprinter – XP – 237B', 'unit_price' => 15000],
                    ['name' => 'Desktop Barcode Scanner', 'unit_price' => 8000],
                ], JSON_UNESCAPED_UNICODE),
                'sort_order' => 1,
            ],
            [
                'name' => 'Software Only',
                'key' => 'software_only',
                'icon' => 'fa-file-alt',
                'subtitle' => 'POS Software license only',
                'template_type' => 'software_only',
                'project_overview' => 'JAAN POS Software License for existing hardware',
                'software_features' => json_encode([
                    ['kind' => 'heading', 'text' => 'Complete POS System with Advanced Features:'],
                    ['kind' => 'item', 'text' => 'Sales Management - Complete billing and invoicing system'],
                    ['kind' => 'item', 'text' => 'Inventory Management - Real-time stock tracking'],
                ], JSON_UNESCAPED_UNICODE),
                'additional_benefits' => json_encode([
                    ['kind' => 'item', 'text' => 'Full Software Demo'],
                    ['kind' => 'item', 'text' => 'Lifetime License'],
                ], JSON_UNESCAPED_UNICODE),
                'hardware_items' => json_encode([], JSON_UNESCAPED_UNICODE),
                'sort_order' => 2,
            ],
        ];

        foreach ($templates as $template) {
            QuoteTemplate::firstOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
