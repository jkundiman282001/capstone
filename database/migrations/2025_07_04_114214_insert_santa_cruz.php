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
            ['barangay' => 'Astorga', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Baliw', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bato', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Coronon', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dalisdis', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Darong', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Inawayan', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Jose Rizal', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Josefina', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lapu-Lapu', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Libertad', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Matutungan', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Melilia', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion Zone I', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion Zone II', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion Zone III', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion Zone IV', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Saliducon', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Agustin', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Antonio', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Isidro', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Jose', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sibulan', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sinoron', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tagabuli', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tagaytay', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Zone V', 'municipality' => 'Santa Cruz', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Santa Cruz')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Astorga',
                'Baliw',
                'Bato',
                'Coronon',
                'Dalisdis',
                'Darong',
                'Inawayan',
                'Jose Rizal',
                'Josefina',
                'Lapu-Lapu',
                'Libertad',
                'Matutungan',
                'Melilia',
                'Poblacion Zone I',
                'Poblacion Zone II',
                'Poblacion Zone III',
                'Poblacion Zone IV',
                'Saliducon',
                'San Agustin',
                'San Antonio',
                'San Isidro',
                'San Jose',
                'Sibulan',
                'Sinoron',
                'Tagabuli',
                'Tagaytay',
                'Zone V',
            ])->delete();
    }
};
