<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('origin', function (Blueprint $table) {
            $table->text('house_num')->nullable()->after('address_id');
        });
    }

    public function down(): void
    {
        Schema::table('origin', function (Blueprint $table) {
            $table->dropColumn('house_num');
        });
    }
}; 