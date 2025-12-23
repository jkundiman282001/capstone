<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the table exists before trying to alter it
        if (!Schema::hasTable('basic_info')) {
            return;
        }

        Schema::table('basic_info', function (Blueprint $table) {
            // Only add column if it doesn't already exist
            if (!Schema::hasColumn('basic_info', 'rssc_score')) {
                $col = $table->decimal('rssc_score', 5, 2)->nullable();
                if (Schema::hasColumn('basic_info', 'gpa')) {
                    $col->after('gpa');
                }
            }
        });
    }

    public function down(): void
    {
        // Check if the table exists before trying to alter it
        if (!Schema::hasTable('basic_info')) {
            return;
        }

        Schema::table('basic_info', function (Blueprint $table) {
            if (Schema::hasColumn('basic_info', 'rssc_score')) {
                $table->dropColumn('rssc_score');
            }
        });
    }
};

