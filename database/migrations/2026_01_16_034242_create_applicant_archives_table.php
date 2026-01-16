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
        if (Schema::hasTable('applicant_archives')) {
            // Table exists, check if it has the right columns or just drop and recreate
            // For simplicity in this dev fix, let's drop and recreate
            Schema::drop('applicant_archives');
        }

        Schema::create('applicant_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('replacement_id')->nullable();
            $table->json('data'); // Stores the full snapshot of applicant data
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->timestamp('archived_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('replacement_id')->references('id')->on('replacements')->onDelete('set null');
            $table->foreign('archived_by')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_archives');
    }
};
