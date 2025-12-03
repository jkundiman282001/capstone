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
            $table->string('school_name')->nullable()->after('address');
            $table->string('school_name2')->nullable()->after('address2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_pref', function (Blueprint $table) {
            $table->dropColumn([
                'school_name',
                'school_name2',
            ]);
        });
    }
};
