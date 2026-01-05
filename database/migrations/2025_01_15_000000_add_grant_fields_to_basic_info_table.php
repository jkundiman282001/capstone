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
        // Check if the table exists before trying to alter it
        if (! Schema::hasTable('basic_info')) {
            return;
        }

        Schema::table('basic_info', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (! Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                $col = $table->boolean('grant_1st_sem')->default(false);
                if (Schema::hasColumn('basic_info', 'gpa')) {
                    $col->after('gpa');
                }
            }

            if (! Schema::hasColumn('basic_info', 'grant_2nd_sem')) {
                $col = $table->boolean('grant_2nd_sem')->default(false);
                if (Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                    $col->after('grant_1st_sem');
                } elseif (Schema::hasColumn('basic_info', 'gpa')) {
                    $col->after('gpa');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the table exists before trying to alter it
        if (! Schema::hasTable('basic_info')) {
            return;
        }

        Schema::table('basic_info', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                $drops[] = 'grant_1st_sem';
            }
            if (Schema::hasColumn('basic_info', 'grant_2nd_sem')) {
                $drops[] = 'grant_2nd_sem';
            }
            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
