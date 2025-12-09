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
        Schema::table('basic_info', function (Blueprint $table) {
            // Add GPA column to store the GPA entered by admin
            // DECIMAL(5,2) supports values from 0.00 to 999.99 (perfect for GPA 1.0-5.0 scale)
            $table->decimal('gpa', 5, 2)->nullable()->after('current_year_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropColumn('gpa');
        });
    }
};
