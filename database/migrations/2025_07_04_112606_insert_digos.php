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
            ['barangay' => 'Aplaya', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Balabag', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Binaton', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Cogon', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dawis', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dulangan', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Goma', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kapatan', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kiagot', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lapu-Lapu', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Magsaysay', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Mahayahay', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Matti', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Rizal', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Ruparan', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Agustin', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Jose', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Miguel', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sinawilan', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Soong', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tiguman', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tres de Mayo', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Zone I (Pob.)', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Zone II (Pob.)', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Zone III (Pob.)', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Zone IV (Pob.)', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Zone V (Pob.)', 'municipality' => 'Digos', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Digos')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Aplaya',
                'Balabag',
                'Binaton',
                'Cogon',
                'Dawis',
                'Dulangan',
                'Goma',
                'Kapatan',
                'Kiagot',
                'Lapu-Lapu',
                'Magsaysay',
                'Mahayahay',
                'Matti',
                'Rizal',
                'Ruparan',
                'San Agustin',
                'San Jose',
                'San Miguel',
                'Sinawilan',
                'Soong',
                'Tiguman',
                'Tres de Mayo',
                'Zone I (Pob.)',
                'Zone II (Pob.)',
                'Zone III (Pob.)',
                'Zone IV (Pob.)',
                'Zone V (Pob.)',
            ])->delete();
    }
};
