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
            ['barangay' => 'Bagumbayan', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Baybay', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bulacan', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Caputian', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Culaman', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Ibo', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lapu-Lapu', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lower Bala', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lower Limonso', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Magatos', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Mahayag', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Malalag Cogon', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Pitu', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Isidro', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Miguel', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sto. Niño', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tagansule', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Upper Limonso', 'municipality' => 'Malalag', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Malalag')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Bagumbayan',
                'Baybay',
                'Bulacan',
                'Caputian',
                'Culaman',
                'Ibo',
                'Lapu-Lapu',
                'Lower Bala',
                'Lower Limonso',
                'Magatos',
                'Mahayag',
                'Malalag Cogon',
                'Pitu',
                'Poblacion',
                'San Isidro',
                'San Miguel',
                'Sto. Niño',
                'Tagansule',
                'Upper Limonso',
            ])->delete();
    }
};
