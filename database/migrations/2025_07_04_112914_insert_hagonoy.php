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
            ['barangay' => 'Aplaya', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Balutakay', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Clib', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dulangan', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Hagonoy Crossing', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kauswagan', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lanuro', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lapulabao', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Leling', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Liberty', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Mahayahay', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Malabang', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Malingao', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Manga', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Marber', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Paligue', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sacub', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Guillermo', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Isidro', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Miguel', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sinayawan', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tacul', 'municipality' => 'Hagonoy', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Hagonoy')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Aplaya',
                'Balutakay',
                'Clib',
                'Dulangan',
                'Hagonoy Crossing',
                'Kauswagan',
                'Lanuro',
                'Lapulabao',
                'Leling',
                'Liberty',
                'Mahayahay',
                'Malabang',
                'Malingao',
                'Manga',
                'Marber',
                'Paligue',
                'Poblacion',
                'Sacub',
                'San Guillermo',
                'San Isidro',
                'San Miguel',
                'Sinayawan',
                'Tacul',
            ])->delete();
    }
};
