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
            $table->text('alt_degree')->nullable()->after('degree');
            $table->text('alt_degree2')->nullable()->after('degree2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_pref', function (Blueprint $table) {
            $table->dropColumn(['alt_degree', 'alt_degree2']);
        });
    }
};
