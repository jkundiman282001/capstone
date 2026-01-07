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
            $table->string('name')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->text('occupation')->nullable()->change();
            $table->text('office_address')->nullable()->change();
            $table->string('educational_attainment')->nullable()->change();
            $table->integer('income')->nullable()->change();
            $table->unsignedBigInteger('ethno_id')->nullable()->change();
        });

        Schema::table('fam_siblings', function (Blueprint $table) {
            $table->integer('age')->nullable()->change();
            $table->text('scholarship')->nullable()->change();
            $table->text('course_year')->nullable()->change();
            $table->string('present_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->text('occupation')->nullable(false)->change();
            $table->text('office_address')->nullable(false)->change();
            $table->string('educational_attainment')->nullable(false)->change();
            $table->integer('income')->nullable(false)->change();
            $table->unsignedBigInteger('ethno_id')->nullable(false)->change();
        });

        Schema::table('fam_siblings', function (Blueprint $table) {
            $table->integer('age')->nullable(false)->change();
            $table->text('scholarship')->nullable(false)->change();
            $table->text('course_year')->nullable(false)->change();
            $table->string('present_status')->nullable(false)->change();
        });
    }
};
