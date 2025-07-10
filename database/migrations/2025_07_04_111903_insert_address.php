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
            ['barangay' => 'Alegre', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Altavista', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Anislag', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bitaug', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Buenavista', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Camanchiles', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Darapuay', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dolo', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Eman', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kinuskusan', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Libertad', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Linawan', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Magsaysay', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Managa', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Marber', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'New Clarin', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Nueva Vida', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion I', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion II', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion III', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion IV', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion V', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Rizal', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sibayan', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tinongtongan', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Union', 'municipality' => 'Bansalan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Bansalan')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Alegre',
                'Altavista',
                'Anislag',
                'Bitaug',
                'Buenavista',
                'Camanchiles',
                'Darapuay',
                'Dolo',
                'Eman',
                'Kinuskusan',
                'Libertad',
                'Linawan',
                'Magsaysay',
                'Managa',
                'Marber',
                'New Clarin',
                'Nueva Vida',
                'Poblacion I',
                'Poblacion II',
                'Poblacion III',
                'Poblacion IV',
                'Poblacion V',
                'Rizal',
                'Sibayan',
                'Tinongtongan',
                'Union',
            ])->delete();
    }
};
