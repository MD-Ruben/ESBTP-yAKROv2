<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateEsbtpMatiereNiveauTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, check if the table exists and drop it
        if (Schema::hasTable('esbtp_matiere_niveau')) {
            Schema::dropIfExists('esbtp_matiere_niveau');
        }

        // Create the table with all required fields
        Schema::create('esbtp_matiere_niveau', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->foreignId('niveau_etude_id')->constrained('esbtp_niveau_etudes')->onDelete('cascade');
            $table->integer('coefficient')->default(1);
            $table->integer('heures_cours')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Add unique constraint
            $table->unique(['matiere_id', 'niveau_etude_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_matiere_niveau');
    }
}
