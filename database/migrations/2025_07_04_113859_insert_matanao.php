<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('address')->insert([
            ['barangay' => 'Asbang', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bagumbayan', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Banga', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Barayong', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bual', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Camanchiles', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Colonsabac', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dongan Pekong', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kiblawan', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kisulad', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lower Bala', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Luna', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'New Visayas', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Rizal', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Saboy', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Jose', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Vicente', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Savoy', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sinawilan', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tagaytay', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Upper Bala', 'municipality' => 'Matanao', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Matanao')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Asbang',
                'Bagumbayan',
                'Banga',
                'Barayong',
                'Bual',
                'Camanchiles',
                'Colonsabac',
                'Dongan Pekong',
                'Kiblawan',
                'Kisulad',
                'Lower Bala',
                'Luna',
                'New Visayas',
                'Poblacion',
                'Rizal',
                'Saboy',
                'San Jose',
                'San Vicente',
                'Savoy',
                'Sinawilan',
                'Tagaytay',
                'Upper Bala',
            ])->delete();
    }
};
