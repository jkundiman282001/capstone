<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('family', function (Blueprint $table) {
            if (Schema::hasColumn('family', 'educational_attain') && ! Schema::hasColumn('family', 'educational_attainment')) {
                \DB::statement('ALTER TABLE `family` CHANGE `educational_attain` `educational_attainment` VARCHAR(255)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family', function (Blueprint $table) {
            if (Schema::hasColumn('family', 'educational_attainment') && ! Schema::hasColumn('family', 'educational_attain')) {
                \DB::statement('ALTER TABLE `family` CHANGE `educational_attainment` `educational_attain` VARCHAR(255)');
            }
        });
    }
};
