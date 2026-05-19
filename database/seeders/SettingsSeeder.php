<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $companyDefaults = [
            ['key' => 'company_name',    'value' => 'JAAN Network (Pvt) Ltd',        'group' => 'company'],
            ['key' => 'company_logo',    'value' => '',                              'group' => 'company'],
            ['key' => 'company_address', 'value' => 'No 46, Hudson Rd, Colombo 03',  'group' => 'company'],
            ['key' => 'company_phone',   'value' => '+94 76 59 33 255',               'group' => 'company'],
            ['key' => 'company_email',   'value' => 'info@jaan.lk',                  'group' => 'company'],
            ['key' => 'company_website', 'value' => 'jaan.lk',                       'group' => 'company'],
            ['key' => 'bank_name',           'value' => 'DFCC Bank',                 'group' => 'payment'],
            ['key' => 'bank_branch',         'value' => 'Gampaha',                   'group' => 'payment'],
            ['key' => 'bank_account_name',   'value' => 'JAAN Network (Pvt) Ltd',    'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '102003031923',              'group' => 'payment'],
            ['key' => 'quotation_prefix', 'value' => 'QT-',  'group' => 'document'],
            ['key' => 'invoice_prefix',   'value' => 'INV-', 'group' => 'document'],
            ['key' => 'default_tax_rate', 'value' => '0',    'group' => 'document'],
        ];

        foreach ($companyDefaults as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // Document defaults — always upsert so the canonical content stays fresh
        $documentDefaults = [
            [
                'key'   => 'default_terms',
                'group' => 'document',
                'value' => <<<'TERMS'
Software Warranty (Lifetime Warranty for POS System)
The software provided with the POS system includes a lifetime warranty.

Coverage:
● Covers any bugs, defects, or malfunctions in the software
● Includes lifetime updates and technical support

Exclusions:
● Issues caused by unauthorized modifications
● Problems arising from third-party software integrations
● Misuse or improper handling of the system

Hardware Warranty (1 Year)
All hardware components of the POS system are covered under a 1-year warranty from the date of purchase.

This includes:
● PC-Full Set
● Cash Drawer
● Xprinter – XP – 237B
● Desktop Barcode Scanner

Limitations of Hardware Warranty
The hardware warranty does not cover:
● Physical damage caused by accidents, misuse, or neglect.
● Damage due to unauthorized repairs, modifications, or tampering.
● Consumable items such as batteries, printer ribbons, and thermal paper.
● Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).

Warranty Claims
● Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.
● Defective hardware must be returned to an authorized service center for inspection.
● Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.

Service Terms
● Lifetime software support will be provided either remotely or on-site, depending on the situation.
● Hardware repair or replacement is free within the 1-year warranty period.

After the 1-year warranty period:
● Repair services will be chargeable
● Replacement parts will be provided at current market prices

Exclusions and Conditions
● Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty
● Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.
TERMS,
            ],
            [
                'key'   => 'default_software_features',
                'group' => 'document',
                'value' => json_encode([
                    ['kind' => 'heading', 'text' => 'Complete POS System with Advanced Features:'],
                    ['kind' => 'item', 'text' => 'Sales Management - Complete billing and invoicing system'],
                    ['kind' => 'item', 'text' => 'Inventory Management - Real-time stock tracking with low stock alerts'],
                    ['kind' => 'item', 'text' => 'Customer Credit Management - Track credit payments and customer balances'],
                    ['kind' => 'item', 'text' => 'Return/Exchange Processing - Handle product returns with full tracking'],
                    ['kind' => 'item', 'text' => 'Split Payment Methods - Accept multiple payment types in single transaction (Cash + Card)'],
                    ['kind' => 'item', 'text' => 'Discount Management - Item-level and custom discounts with authorization'],
                    ['kind' => 'item', 'text' => 'Supplier Management - Track supplier checks and payment notifications'],
                    ['kind' => 'item', 'text' => 'Barcode with Expiry Date Tracking - Scan barcodes to track product expiry dates'],
                    ['kind' => 'item', 'text' => 'Comprehensive Reports - Sales, inventory, customer, cashier & shift reports'],
                    ['kind' => 'item', 'text' => 'User Management - Role-based access control (Admin & Cashier roles)'],
                    ['kind' => 'item', 'text' => 'Shift & Till Management - Track employee shifts and cash drawer reconciliation'],
                    ['kind' => 'item', 'text' => 'Online System with Website Sync - Real-time synchronization with e-commerce platform'],
                    ['kind' => 'item', 'text' => 'Comprehensive User Guide - 38-page detailed manual included (PDF format)'],
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key'   => 'default_additional_benefits',
                'group' => 'document',
                'value' => json_encode([
                    ['kind' => 'item', 'text' => 'Full Software Demo'],
                    ['kind' => 'item', 'text' => 'Unlimited Software Customizations'],
                    ['kind' => 'item', 'text' => 'Lifetime License for JAAN POS Software'],
                ], JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($documentDefaults as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
