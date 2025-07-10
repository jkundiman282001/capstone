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
        DB::table('ethno')->insert([
            ['ethnicity' => 'The Subanen', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Manobo', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => "B'laan", 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => "T'boli", 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Mandaya', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Mansaka', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Tiruray', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Higaonon', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Bagobo', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Bukidnon', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Tagakaolo', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Banwaon', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Dibabawon', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Talaandig', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Mamanua', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Manguangan', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Maranao', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Maguindanaon', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Tausug', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Kalagan', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Sangil', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Ilanun/Iranun', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Palibugan', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Yakan', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Sama', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Badjao', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Jumamapun', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Palawanon', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Molbog', 'created_at' => now(), 'updated_at' => now()],
            ['ethnicity' => 'Other', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('ethno')->whereIn('ethnicity', [
            'The Subanen', 'Manobo', "B'laan", "T'boli", 'Mandaya', 'Mansaka', 'Tiruray', 'Higaonon', 'Bagobo', 'Bukidnon', 'Tagakaolo', 'Banwaon', 'Dibabawon', 'Talaandig', 'Mamanua', 'Manguangan', 'Maranao', 'Maguindanaon', 'Tausug', 'Kalagan', 'Sangil', 'Ilanun/Iranun', 'Palibugan', 'Yakan', 'Sama', 'Badjao', 'Jumamapun', 'Palawanon', 'Molbog', 'Other'
        ])->delete();
    }
};
