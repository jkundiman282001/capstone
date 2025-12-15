<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->decimal('rssc_score', 5, 2)->nullable()->after('grant_2nd_sem');
        });
    }

    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropColumn('rssc_score');
        });
    }
};

