<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@jaan.lk'],
            [
                'name' => 'JAAN Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Create demo users
        User::firstOrCreate(
            ['email' => 'user@jaan.lk'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        // Call all seeders
        $this->call(SettingsSeeder::class);
        $this->call(QuoteTemplateSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(HardwareCatalogSeeder::class);
    }
}
