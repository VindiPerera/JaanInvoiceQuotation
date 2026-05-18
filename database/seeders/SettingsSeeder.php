<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'company_name', 'value' => 'JAAN Network (Pvt) Ltd', 'group' => 'company'],
            ['key' => 'company_address', 'value' => 'No 46, Hudson Rd, Colombo 03', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '+94 76 59 33 255', 'group' => 'company'],
            ['key' => 'company_email', 'value' => 'info@jaan.lk', 'group' => 'company'],
            ['key' => 'company_website', 'value' => 'jaan.lk', 'group' => 'company'],
            ['key' => 'bank_name', 'value' => 'DFCC Bank', 'group' => 'payment'],
            ['key' => 'bank_branch', 'value' => 'Gampaha', 'group' => 'payment'],
            ['key' => 'bank_account_name', 'value' => 'JAAN Network (Pvt) Ltd', 'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '102003031923', 'group' => 'payment'],
            ['key' => 'quotation_prefix', 'value' => 'QT-', 'group' => 'document'],
            ['key' => 'invoice_prefix', 'value' => 'INV-', 'group' => 'document'],
            ['key' => 'default_tax_rate', 'value' => '0', 'group' => 'document'],
            ['key' => 'default_terms', 'value' => "1. Software Warranty (Lifetime Warranty):\n   - Covers defects, malfunctions, and lifetime support.\n   - Unauthorized modifications void the warranty.\n\n2. Service Terms:\n   - Lifetime software support.\n   - Free Hardware repair or replacement within 1 year.\n   - Post-warranty repairs are Chargeable", 'group' => 'document'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
