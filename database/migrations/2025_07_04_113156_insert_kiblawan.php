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
            ['barangay' => 'Abnate', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bagong Negros', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bala', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Banarao', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Batang', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bituag', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Bulakan', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Cogon Bacaca', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Colonsabac', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Dapok', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kiblawan', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Kimlawis', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'La Union', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Latian', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Manual', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Maraga-a', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'New Sibonga', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Paitan', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Pasig', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sibulan', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Sibulan', 'municipality' => 'Kiblawan', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Kiblawan')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Abnate',
                'Bagong Negros',
                'Bala',
                'Banarao',
                'Batang',
                'Bituag',
                'Bulakan',
                'Cogon Bacaca',
                'Colonsabac',
                'Dapok',
                'Kiblawan',
                'Kimlawis',
                'La Union',
                'Latian',
                'Manual',
                'Maraga-a',
                'New Sibonga',
                'Paitan',
                'Pasig',
                'Sibulan',
            ])->delete();
    }
};
