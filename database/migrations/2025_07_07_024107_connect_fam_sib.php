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
        Schema::table('family', function (Blueprint $table) {
            $table->unsignedBigInteger('ethno_id')->change();
            $table->unsignedInteger('fam_siblings_id')->change();

            $table->foreign('ethno_id')->references('id')->on('ethno')->onDelete('cascade');
            $table->foreign('fam_siblings_id')->references('id')->on('fam_siblings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family', function (Blueprint $table) {
            $table->dropForeign(['ethno_id']);
            $table->dropForeign(['fam_siblings_id']);
        });
    }
};
