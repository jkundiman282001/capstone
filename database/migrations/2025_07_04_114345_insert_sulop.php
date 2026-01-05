<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('address')->insert([
            ['barangay' => 'Bagumbayan', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Balasinon', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Carre', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Clib', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'La Fortuna', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Labon', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Laperas', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Luna', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'New Cebu', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Palili', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Paraiso', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poloyagan', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Talas', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Talus', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tocok', 'municipality' => 'Sulop', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Sulop')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Bagumbayan',
                'Balasinon',
                'Carre',
                'Clib',
                'La Fortuna',
                'Labon',
                'Laperas',
                'Luna',
                'New Cebu',
                'Palili',
                'Paraiso',
                'Poblacion',
                'Poloyagan',
                'Talas',
                'Talus',
                'Tocok',
            ])->delete();
    }
};
