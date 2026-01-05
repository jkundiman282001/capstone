<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacements', function (Blueprint $table) {
            $table->id();

            // The applicant who becomes the replacement awardee
            $table->unsignedBigInteger('replacement_user_id');

            // The grantee/awardee being replaced (optional link, or store name if unknown)
            $table->unsignedBigInteger('replaced_user_id')->nullable();
            $table->string('replaced_name')->nullable();

            // Reason/s of replacement
            $table->text('replacement_reason')->nullable();

            // Optional label (e.g., "SY 2022-2023")
            $table->string('school_year')->nullable();

            // Staff who encoded this replacement (optional)
            $table->unsignedBigInteger('created_by_staff_id')->nullable();

            $table->timestamps();

            $table->foreign('replacement_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('replaced_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by_staff_id')->references('id')->on('staff')->nullOnDelete();

            $table->index(['replacement_user_id']);
            $table->index(['replaced_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacements');
    }
};
