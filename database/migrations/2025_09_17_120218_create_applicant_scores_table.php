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
        Schema::create('applicant_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_score', 5, 2)->default(0.00);
            $table->decimal('financial_need_score', 5, 2)->default(0.00);
            $table->decimal('academic_performance_score', 5, 2)->default(0.00);
            $table->decimal('document_completeness_score', 5, 2)->default(0.00);
            $table->decimal('geographic_priority_score', 5, 2)->default(0.00);
            $table->decimal('indigenous_heritage_score', 5, 2)->default(0.00);
            $table->decimal('family_situation_score', 5, 2)->default(0.00);
            $table->integer('priority_rank')->nullable();
            $table->text('scoring_notes')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
            
            $table->index(['total_score', 'priority_rank']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_scores');
    }
};
