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
                'name' => 'Learning Management System Mobile Application Development ( 3 Phase )',
                'description' => 'Learning Management System Mobile Application Development ( 3 Phase )',
                'category' => 'Software Development',
                'unit_price' => 0.00,
                'warranty' => '3 Years',
                'is_active' => true,
            ],
            [
                'name' => 'CPU + Monitor',
                'description' => "CPU + Monitor\n• i5 4th gen 8gb ram\n• 19in monitor",
                'category' => 'Hardware',
                'unit_price' => 125000.00,
                'warranty' => '1 Year',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            HardwareCatalog::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
