<?php

namespace Database\Seeders;

use App\Models\HardwareCatalog;
use Illuminate\Database\Seeder;

class HardwareCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'PC-Full Set',
                'description' => 'Complete Computer System with Monitor, Keyboard, Mouse',
                'category' => 'Hardware',
                'unit_price' => 150000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
            [
                'name' => 'Cash Drawer',
                'description' => 'Electronic Cash Drawer with Integration',
                'category' => 'Hardware',
                'unit_price' => 25000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
            [
                'name' => 'Xprinter – XP – 237B',
                'description' => 'Thermal Receipt Printer',
                'category' => 'Hardware',
                'unit_price' => 15000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
            [
                'name' => 'Desktop Barcode Scanner',
                'description' => '2D Barcode Scanner for Point of Sale',
                'category' => 'Hardware',
                'unit_price' => 8000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
            [
                'name' => 'POS Display Monitor',
                'description' => '15.6 inch Customer Facing Display',
                'category' => 'Hardware',
                'unit_price' => 35000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
            [
                'name' => 'Network Router',
                'description' => 'WiFi Router for Multi-terminal Setup',
                'category' => 'Networking',
                'unit_price' => 12000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
            [
                'name' => 'Receipt Paper Roll',
                'description' => 'Thermal Receipt Paper - Pack of 50 Rolls',
                'category' => 'Consumables',
                'unit_price' => 5000.00,
                'warranty' => 'N/A',
                'is_active' => true,
            ],
            [
                'name' => 'Barcode Labels',
                'description' => 'Self-adhesive Barcode Labels - 1000 Pieces',
                'category' => 'Consumables',
                'unit_price' => 3500.00,
                'warranty' => 'N/A',
                'is_active' => true,
            ],
            [
                'name' => 'UPS (Uninterruptible Power Supply)',
                'description' => '1000VA UPS with 45-60 minute backup',
                'category' => 'Hardware',
                'unit_price' => 42000.00,
                'warranty' => '2 Years',
                'is_active' => true,
            ],
            [
                'name' => 'Installation & Configuration',
                'description' => 'Complete System Installation and Configuration Service',
                'category' => 'Service',
                'unit_price' => 50000.00,
                'warranty' => 'Service',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            HardwareCatalog::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
