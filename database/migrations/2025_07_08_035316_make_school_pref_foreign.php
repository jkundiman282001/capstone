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
            if (Schema::hasColumn('basic_info', 'school_pref_id')) {
                $table->unsignedInteger('school_pref_id')->change();
            }

            if (Schema::hasColumn('basic_info', 'fam_siblings_id')) {
                $table->foreign('fam_siblings_id')->references('id')->on('fam_siblings')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropForeign(['school_pref_id']);
        });
    }
};
