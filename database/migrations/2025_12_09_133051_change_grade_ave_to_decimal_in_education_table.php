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
        // Use raw SQL to change column type from integer to decimal
        // Using DECIMAL(5,2) to accommodate any existing large values (0.00 to 999.99)
        // This is safe for GPA values (typically 1.0-5.0) and won't lose existing data
        DB::statement('ALTER TABLE `education` MODIFY `grade_ave` DECIMAL(5,2) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to integer (note: this will lose decimal precision)
        DB::statement('ALTER TABLE `education` MODIFY `grade_ave` INT NULL');
    }
};
