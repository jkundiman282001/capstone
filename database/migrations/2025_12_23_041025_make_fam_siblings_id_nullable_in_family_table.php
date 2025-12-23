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
        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Get the foreign key constraint name if it exists
            $constraints = \DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'family'
                AND COLUMN_NAME = 'fam_siblings_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            // Drop foreign key if it exists
            foreach ($constraints as $constraint) {
                try {
                    \DB::statement("ALTER TABLE `family` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Ignore if already dropped
                }
            }
            
            // Make column nullable
            if (Schema::hasColumn('family', 'fam_siblings_id')) {
                \DB::statement('ALTER TABLE `family` MODIFY `fam_siblings_id` INT UNSIGNED NULL');
            }
        } finally {
            // Re-enable foreign key checks
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family', function (Blueprint $table) {
            if (Schema::hasColumn('family', 'fam_siblings_id')) {
                // Make column NOT NULL again
                \DB::statement('ALTER TABLE `family` MODIFY `fam_siblings_id` INT UNSIGNED NOT NULL');
            }
        });
    }
};
