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
                'name'     => 'MariaPOS PC Full Set',
                'key'      => 'full_set',
                'icon'     => 'fa-desktop',
                'subtitle' => 'Hardware + Software',
                'sort_order' => 1,
                'software_features' => [],
                'additional_benefits' => [
                    ['kind' => 'heading', 'text' => '1. Software Warranty (Lifetime):'],
                    ['kind' => 'item',    'text' => 'Lifetime free software updates & bug fixes'],
                    ['kind' => 'item',    'text' => 'Lifetime remote or on-site technical support'],
                    ['kind' => 'space',   'text' => ''],
                    ['kind' => 'heading', 'text' => '2. Hardware Warranty (1 Year):'],
                    ['kind' => 'item',    'text' => '1 Year warranty on all hardware components'],
                    ['kind' => 'item',    'text' => 'Free repair or replacement for defective parts within warranty'],
                    ['kind' => 'space',   'text' => ''],
                    ['kind' => 'heading', 'text' => '3. Installation & Training:'],
                    ['kind' => 'item',    'text' => 'Free on-site installation and system configuration'],
                    ['kind' => 'item',    'text' => 'Staff training on system usage included'],
                ],
                'terms_conditions' => "SOFTWARE WARRANTY (Lifetime Warranty for POS System)\nThe software provided with the POS system includes a lifetime warranty.\n\nCoverage:\n• Covers any bugs, defects, or malfunctions in the software\n• Includes lifetime updates and technical support\n\nExclusions:\n• Issues caused by unauthorized modifications\n• Problems arising from third-party software integrations\n• Misuse or improper handling of the system\n\nHARDWARE WARRANTY (1 Year)\nAll hardware components of the POS system are covered under a 1-year warranty from the date of purchase.\n\nThis includes:\n• PC-Full Set\n• Cash Drawer\n• Xprinter – XP – 237B\n• Desktop Barcode Scanner\n\nLIMITATIONS OF HARDWARE WARRANTY\nThe hardware warranty does not cover:\n• Physical damage caused by accidents, misuse, or neglect.\n• Damage due to unauthorized repairs, modifications, or tampering.\n• Consumable items such as batteries, printer ribbons, and thermal paper.\n• Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).\n\nWARRANTY CLAIMS\n• Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.\n• Defective hardware must be returned to an authorized service center for inspection.\n• Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.\n\nSERVICE TERMS\n• Lifetime software support will be provided either remotely or on-site, depending on the situation.\n• Hardware repair or replacement is free within the 1-year warranty period.\n\nAfter the 1-year warranty period:\n• Repair services will be chargeable\n• Replacement parts will be provided at current market prices\n\nEXCLUSIONS AND CONDITIONS\n• Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty.\n• Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.",
            ],
            [
                'name'     => 'Software Only',
                'key'      => 'software_only',
                'icon'     => 'fa-code',
                'subtitle' => 'Software features only',
                'sort_order' => 2,
                'software_features' => [],
                'additional_benefits' => [
                    ['kind' => 'heading', 'text' => '1. Software Warranty (Lifetime):'],
                    ['kind' => 'item',    'text' => 'Lifetime free software updates & bug fixes'],
                    ['kind' => 'item',    'text' => 'Lifetime remote or on-site technical support'],
                    ['kind' => 'item',    'text' => 'Cloud backup support (optional add-on)'],
                    ['kind' => 'space',   'text' => ''],
                    ['kind' => 'heading', 'text' => '2. Training & Onboarding:'],
                    ['kind' => 'item',    'text' => 'Online/remote training session included'],
                    ['kind' => 'item',    'text' => 'User manual and documentation provided'],
                ],
                'terms_conditions' => "SOFTWARE WARRANTY (Lifetime Warranty for POS System)\nThe software provided with the POS system includes a lifetime warranty.\n\nCoverage:\n• Covers any bugs, defects, or malfunctions in the software\n• Includes lifetime updates and technical support\n\nExclusions:\n• Issues caused by unauthorized modifications\n• Problems arising from third-party software integrations\n• Misuse or improper handling of the system\n\nSERVICE TERMS\n• Lifetime software support will be provided either remotely or on-site, depending on the situation.\n\nEXCLUSIONS AND CONDITIONS\n• Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty.\n• Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.",
            ],
            [
                'name'     => 'Hardware Only',
                'key'      => 'hardware_only',
                'icon'     => 'fa-microchip',
                'subtitle' => 'Hardware package only',
                'sort_order' => 3,
                'software_features' => [],
                'additional_benefits' => [
                    ['kind' => 'heading', 'text' => '1. Hardware Warranty (1 Year):'],
                    ['kind' => 'item',    'text' => '1 Year warranty on all hardware components'],
                    ['kind' => 'item',    'text' => 'Free repair or replacement for defective parts within warranty'],
                    ['kind' => 'space',   'text' => ''],
                    ['kind' => 'heading', 'text' => '2. Delivery & Setup:'],
                    ['kind' => 'item',    'text' => 'Free delivery within city limits'],
                    ['kind' => 'item',    'text' => 'Hardware setup and basic configuration included'],
                ],
                'terms_conditions' => "HARDWARE WARRANTY (1 Year)\nAll hardware components are covered under a 1-year warranty from the date of purchase.\n\nThis includes:\n• PC-Full Set\n• Cash Drawer\n• Xprinter – XP – 237B\n• Desktop Barcode Scanner\n\nLIMITATIONS OF HARDWARE WARRANTY\nThe hardware warranty does not cover:\n• Physical damage caused by accidents, misuse, or neglect.\n• Damage due to unauthorized repairs, modifications, or tampering.\n• Consumable items such as batteries, printer ribbons, and thermal paper.\n• Damage caused by power surges, improper electrical connections, or environmental conditions (e.g., moisture, extreme temperatures).\n\nWARRANTY CLAIMS\n• Customers must provide proof of purchase (invoice or receipt) when making a warranty claim.\n• Defective hardware must be returned to an authorized service center for inspection.\n• Hardware will be repaired or replaced at no additional cost if the issue falls within warranty coverage.\n\nSERVICE TERMS\n• Hardware repair or replacement is free within the 1-year warranty period.\n\nAfter the 1-year warranty period:\n• Repair services will be chargeable\n• Replacement parts will be provided at current market prices\n\nEXCLUSIONS AND CONDITIONS\n• Any damage or malfunction caused by misuse, mishandling, or unauthorized modifications will void the warranty.\n• Warranty services are only applicable if the product is used under normal operating conditions and according to the provided instructions.",
            ],
        ];

        foreach ($templates as $data) {
            QuoteTemplate::firstOrCreate(['key' => $data['key']], $data);
        }
    }
}
