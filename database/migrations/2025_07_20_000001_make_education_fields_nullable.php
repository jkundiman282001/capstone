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
            $table->integer('year_grad')->nullable()->change();
            $table->integer('grade_ave')->nullable()->change();
            $table->text('rank')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            $table->integer('year_grad')->nullable(false)->change();
            $table->integer('grade_ave')->nullable(false)->change();
            $table->text('rank')->nullable(false)->change();
        });
    }
};
