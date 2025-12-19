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
        Schema::table('basic_info', function (Blueprint $table) {
            $table->boolean('disqualification_not_ip')->default(false)->after('application_rejection_reason');
            $table->boolean('disqualification_exceeded_income')->default(false)->after('disqualification_not_ip');
            $table->boolean('disqualification_incomplete_docs')->default(false)->after('disqualification_exceeded_income');
            $table->text('disqualification_remarks')->nullable()->after('disqualification_incomplete_docs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $table->dropColumn([
                'disqualification_not_ip',
                'disqualification_exceeded_income',
                'disqualification_incomplete_docs',
                'disqualification_remarks'
            ]);
        });
    }
};
