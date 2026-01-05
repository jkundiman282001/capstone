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
        if (! Schema::hasTable('mailing_address')) {
            Schema::create('mailing_address', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('address_id');
                $table->timestamps();

                $table->foreign('address_id')->references('id')->on('address')->onDelete('cascade');
            });
        } else {
            // Table exists, try to add foreign key if it doesn't exist
            try {
                Schema::table('mailing_address', function (Blueprint $table) {
                    if (Schema::hasColumn('mailing_address', 'address_id')) {
                        $table->foreign('address_id')->references('id')->on('address')->onDelete('cascade');
                    }
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailing_address');
    }
};
