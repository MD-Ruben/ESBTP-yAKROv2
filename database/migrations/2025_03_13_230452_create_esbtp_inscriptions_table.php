<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Using anonymous class to avoid duplicate class name issues
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This migration is redundant as the table is already created in another migration
        // We'll just check if the table exists and add any missing columns if needed
        if (Schema::hasTable('esbtp_inscriptions')) {
            // Table already exists, no need to create it again
        } else {
            Schema::create('esbtp_inscriptions', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't want to drop the table here as it might be used by other migrations
    }
};
