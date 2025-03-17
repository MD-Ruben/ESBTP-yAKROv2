<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        if (Schema::hasTable('esbtp_matiere_niveau')) {
            // Table already exists, no need to create it again
        } else {
            Schema::create('esbtp_matiere_niveau', function (Blueprint $table) {
                $table->id();
                $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
                $table->foreignId('niveau_etude_id')->constrained('esbtp_niveau_etudes')->onDelete('cascade');
                $table->double('coefficient', 8, 2)->default(1);
                $table->integer('heures_cours')->nullable();
                $table->boolean('is_active')->default(true);
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
