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
        Schema::table('school_pref', function (Blueprint $table) {
            $table->text('address2')->nullable();
            $table->text('degree2')->nullable();
            $table->string('school_type2')->nullable();
            $table->integer('num_years2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_pref', function (Blueprint $table) {
            $table->dropColumn(['address2', 'degree2', 'school_type2', 'num_years2']);
        });
    }
}; 