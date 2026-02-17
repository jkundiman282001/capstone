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
                'access_code' => 'NCIP-ADMIN-2024',
                'password' => Hash::make('password'), // Keeping password as fallback
            ]);
            $this->command->info('Admin account created successfully.');
            $this->command->info('Email: admin@ncip.gov.ph');
            $this->command->info('Access Code: NCIP-ADMIN-2024');
        } else {
            // Update existing admin with access code if missing
            $admin = Staff::where('email', 'admin@ncip.gov.ph')->first();
            if (!$admin->access_code) {
                $admin->update(['access_code' => 'NCIP-ADMIN-2024']);
                $this->command->info('Updated existing admin with access code: NCIP-ADMIN-2024');
            }
            $this->command->info('Admin account already exists.');
        }
    }
}
