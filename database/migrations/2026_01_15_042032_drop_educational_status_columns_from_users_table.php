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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['educational_status', 'college_year', 'grade_scale', 'gpa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('educational_status')->nullable()->after('course');
            $table->string('college_year')->nullable()->after('educational_status');
            $table->string('grade_scale')->nullable()->after('college_year');
            $table->decimal('gpa', 5, 2)->nullable()->after('grade_scale');
        });
    }
};
