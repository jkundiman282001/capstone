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
        Schema::table('fam_siblings', function (Blueprint $table) {
            $table->unsignedBigInteger('basic_info_id')->nullable()->after('id');
            $table->foreign('basic_info_id')->references('id')->on('basic_info')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fam_siblings', function (Blueprint $table) {
            $table->dropForeign(['basic_info_id']);
            $table->dropColumn('basic_info_id');
        });
    }
};
