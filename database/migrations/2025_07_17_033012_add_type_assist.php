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
            if (! Schema::hasColumn('basic_info', 'type_assist')) {
                if (Schema::hasColumn('basic_info', 'school_pref_id')) {
                    $table->string('type_assist')->nullable()->after('school_pref_id');
                } else {
                    $table->string('type_assist')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropColumn('type_assist');
        });
    }
};
