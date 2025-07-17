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
            $table->dropForeign(['ethno_id']);
            $table->dropForeign(['family_id']);
        });
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
