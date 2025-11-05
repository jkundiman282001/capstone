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
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('priority_rank')->nullable()->after('status');
            $table->decimal('priority_score', 10, 2)->default(0)->after('priority_rank');
            $table->timestamp('submitted_at')->nullable()->after('priority_score');
            
            $table->index(['priority_rank', 'status']);
            $table->index(['priority_score', 'status']);
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['priority_rank', 'status']);
            $table->dropIndex(['priority_score', 'status']);
            $table->dropIndex(['submitted_at']);
            $table->dropColumn(['priority_rank', 'priority_score', 'submitted_at']);
        });
    }
};
