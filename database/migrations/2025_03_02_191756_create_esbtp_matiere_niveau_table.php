<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpMatiereNiveauTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_matiere_niveau', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->foreignId('niveau_etude_id')->constrained('esbtp_niveau_etudes')->onDelete('cascade');
            $table->float('coefficient')->default(1.0);
            $table->integer('heures_cours')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unique(['matiere_id', 'niveau_etude_id']);
            $table->timestamps();
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
