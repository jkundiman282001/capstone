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
        Schema::table('education', function (Blueprint $table) {
            if (!Schema::hasColumn('education', 'basic_info_id')) {
                $table->unsignedBigInteger('basic_info_id')->nullable()->after('id');
                $table->foreign('basic_info_id')->references('id')->on('basic_info')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            if (Schema::hasColumn('education', 'basic_info_id')) {
                try {
                    $table->dropForeign(['basic_info_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('basic_info_id');
            }
        });
    }
};
