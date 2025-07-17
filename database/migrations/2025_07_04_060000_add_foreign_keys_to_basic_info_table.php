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

            $table->foreign('education_id')->references('id')->on('education')->onDelete('set null');
            $table->foreign('family_id')->references('id')->on('family')->onDelete('set null');
        });

        // Add foreign key for basic_info_id in education table
        Schema::table('education', function (Blueprint $table) {
            $table->foreign('basic_info_id')->references('id')->on('basic_info')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropForeign(['education_id']);
            $table->dropForeign(['family_id']);
            $table->dropColumn(['education_id', 'family_id']);
        });
    }
}; 