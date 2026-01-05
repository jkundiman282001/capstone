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
            ['barangay' => 'Bacungan', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Balnate', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bangkal', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Barayong', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Blocon', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dalawinon', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dalumay', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Glamang', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kasuga', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Lower Bala', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Malawanit', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'New Ilocos', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'New Opon', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Poblacion', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Isidro', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tacul', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Tagaytay', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Upper Bala', 'municipality' => 'Magsaysay', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Magsaysay')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Bacungan',
                'Balnate',
                'Bangkal',
                'Barayong',
                'Blocon',
                'Dalawinon',
                'Dalumay',
                'Glamang',
                'Kasuga',
                'Lower Bala',
                'Malawanit',
                'New Ilocos',
                'New Opon',
                'Poblacion',
                'San Isidro',
                'Tacul',
                'Tagaytay',
                'Upper Bala',
            ])->delete();
    }
};
