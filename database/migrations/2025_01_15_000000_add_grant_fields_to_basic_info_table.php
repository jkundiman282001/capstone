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
            $table->boolean('grant_1st_sem')->default(false)->after('gpa');
            $table->boolean('grant_2nd_sem')->default(false)->after('grant_1st_sem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropColumn(['grant_1st_sem', 'grant_2nd_sem']);
        });
    }
};

