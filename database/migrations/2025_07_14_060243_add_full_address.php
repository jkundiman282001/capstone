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
            $table->unsignedBigInteger('full_address_id')->nullable()->after('user_id');
            $table->foreign('full_address_id')->references('id')->on('full_address')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropForeign(['full_address_id']);
            $table->dropColumn('full_address_id');
        });
    }
};
