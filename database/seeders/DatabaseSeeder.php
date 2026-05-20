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
                'is_admin' => true,
            ]
        );

        // Create demo users
        User::firstOrCreate(
            ['email' => 'user@jaan.lk'],
            [
                'name' => 'JAAN User',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        // Call all seeders
        $this->call(SettingsSeeder::class);
   
        
    }
}
