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
            ['barangay' => 'Almendras (Poblacion)', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Aurora', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Carmen', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Central', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Limonso', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'N.C. (Poblacion)', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Palili', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Piape', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Quirino District (Poblacion)', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Isidro', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'San Jose', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
            ['barangay' => 'Upper Limonso', 'municipality' => 'Padada', 'province' => 'Davao del Sur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('address')->where('municipality', 'Padada')
            ->where('province', 'Davao del Sur')
            ->whereIn('barangay', [
                'Almendras (Poblacion)',
                'Aurora',
                'Carmen',
                'Central',
                'Limonso',
                'N.C. (Poblacion)',
                'Palili',
                'Piape',
                'Quirino District (Poblacion)',
                'San Isidro',
                'San Jose',
                'Upper Limonso',
            ])->delete();
    }
};
