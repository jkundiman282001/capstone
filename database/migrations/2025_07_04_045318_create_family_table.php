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
        Schema::create('family', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ethno_id')->nullable(false);
            $table->integer('fam_siblings_id')->nullable(false);
            $table->string('fam_type');
            $table->string('name');
            $table->text('address');
            $table->text('occupation');
            $table->text('office_address');
            $table->string('educational_attain');
            $table->integer('income');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family');
    }
};
