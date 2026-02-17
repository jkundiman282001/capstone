<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the admin user already exists
        if (! Staff::where('email', 'admin@ncip.gov.ph')->exists()) {
            Staff::create([
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@ncip.gov.ph',
                'password' => Hash::make('password'),
            ]);
            $this->command->info('Admin account created successfully.');
            $this->command->info('Email: admin@ncip.gov.ph');
            $this->command->info('Password: password');
        } else {
            $this->command->info('Admin account already exists.');
        }
    }
}
