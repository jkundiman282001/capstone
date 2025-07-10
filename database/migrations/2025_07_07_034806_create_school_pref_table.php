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
        Schema::create('school_pref', function (Blueprint $table) {
            $table->increments('id');
            $table->text('address');
            $table->text('degree');
            $table->string('school_type');
            $table->integer('num_years');
            $table->text('ques_answer1');
            $table->text('ques_answer2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_pref');
    }
};
