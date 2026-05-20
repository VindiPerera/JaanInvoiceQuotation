<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Colombo Retail Store',
                'address' => 'No. 123, Galle Road, Colombo 03',
                'contact' => '+94 76 123 4567',
                'email' => 'info@colomboretail.lk',
                'notes' => 'Regular customer - Monthly orders',
            ],
            [
                'name' => 'Kandy Fashion Hub',
                'address' => 'No. 456, Peradeniya Road, Kandy',
                'contact' => '+94 76 234 5678',
                'email' => 'contact@kandyfashion.lk',
                'notes' => 'New customer - Initial setup',
            ],
            [
                'name' => 'Galle Grocery Chain',
                'address' => 'No. 789, Church Street, Galle',
                'contact' => '+94 76 345 6789',
                'email' => 'support@gallegrocery.lk',
                'notes' => 'Expansion project - Multiple locations',
            ],
            [
                'name' => 'Jaffna Electronics',
                'address' => 'No. 234, Main Road, Jaffna',
                'contact' => '+94 76 456 7890',
                'email' => 'sales@jaffnaelectronics.lk',
                'notes' => 'Tech savvy customer',
            ],
            [
                'name' => 'Matara Supermarket',
                'address' => 'No. 567, Coastal Road, Matara',
                'contact' => '+94 76 567 8901',
                'email' => 'manager@matarasupermarket.lk',
                'notes' => 'Enterprise customer',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }
    }
}
