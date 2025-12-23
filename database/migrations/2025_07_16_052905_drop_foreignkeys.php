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
        // Drop ethno_id foreign key if it exists
        try {
            Schema::table('basic_info', function (Blueprint $table) {
                if (Schema::hasColumn('basic_info', 'ethno_id')) {
                    $table->dropForeign(['ethno_id']);
                }
            });
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore
        }
        
        // Drop family_id foreign key if it exists
        try {
            Schema::table('basic_info', function (Blueprint $table) {
                if (Schema::hasColumn('basic_info', 'family_id')) {
                    $table->dropForeign(['family_id']);
                }
            });
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->foreign('ethno_id')->references('id')->on('ethno')->onDelete('set null');
            $table->foreign('family_id')->references('id')->on('family')->onDelete('set null');
        });
    }
};
