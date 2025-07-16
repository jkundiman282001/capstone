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
        Schema::create('full_address', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mailing_address_id');
            $table->unsignedBigInteger('permanent_address_id');
            $table->unsignedBigInteger('origin_id');
            $table->timestamps();

            $table->foreign('mailing_address_id')->references('id')->on('mailing_address')->onDelete('cascade');
            $table->foreign('permanent_address_id')->references('id')->on('permanent_address')->onDelete('cascade');
            $table->foreign('origin_id')->references('id')->on('origin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('full_address');
    }
};
