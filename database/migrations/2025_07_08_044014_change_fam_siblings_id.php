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
        // 1. Drop the foreign key constraint
        Schema::table('family', function (Blueprint $table) {
            $table->dropForeign(['fam_siblings_id']);
        });

        // 2. Make the column nullable
        Schema::table('family', function (Blueprint $table) {
            $table->unsignedBigInteger('fam_siblings_id')->nullable()->change();
        });

        // 3. Re-add the foreign key constraint
        Schema::table('family', function (Blueprint $table) {
            $table->foreign('fam_siblings_id')->references('id')->on('fam_siblings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes if needed
        Schema::table('family', function (Blueprint $table) {
            $table->dropForeign(['fam_siblings_id']);
            $table->unsignedBigInteger('fam_siblings_id')->nullable(false)->change();
            $table->foreign('fam_siblings_id')->references('id')->on('fam_siblings');
        });
    }
};
