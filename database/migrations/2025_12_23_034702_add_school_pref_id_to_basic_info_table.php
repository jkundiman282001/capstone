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
            if (!Schema::hasColumn('basic_info', 'school_pref_id')) {
                $table->unsignedInteger('school_pref_id')->nullable()->after('full_address_id');
                $table->foreign('school_pref_id')->references('id')->on('school_pref')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            if (Schema::hasColumn('basic_info', 'school_pref_id')) {
                try {
                    $table->dropForeign(['school_pref_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('school_pref_id');
            }
        });
    }
};
