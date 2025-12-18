<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            // Some environments have this migration recorded as "ran" but the columns are missing.
            // Add them safely if they don't exist.

            if (!Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                $col = $table->boolean('grant_1st_sem')->default(false);
                if (Schema::hasColumn('basic_info', 'gpa')) {
                    $col->after('gpa');
                }
            }

            if (!Schema::hasColumn('basic_info', 'grant_2nd_sem')) {
                $col = $table->boolean('grant_2nd_sem')->default(false);
                if (Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                    $col->after('grant_1st_sem');
                } elseif (Schema::hasColumn('basic_info', 'gpa')) {
                    $col->after('gpa');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('basic_info', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('basic_info', 'grant_1st_sem')) $drops[] = 'grant_1st_sem';
            if (Schema::hasColumn('basic_info', 'grant_2nd_sem')) $drops[] = 'grant_2nd_sem';
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};


