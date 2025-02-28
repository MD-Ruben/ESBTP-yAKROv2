<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPAnneeUniversitairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_annee_universitaires', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de l'année universitaire (ex: 2024-2025)
            $table->date('start_date'); // Date de début
            $table->date('end_date'); // Date de fin
            $table->boolean('is_current')->default(false); // Indique si c'est l'année universitaire en cours
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->text('description')->nullable(); // Description
            $table->timestamps();
            $table->softDeletes(); // Pour la suppression logique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_annee_universitaires');
    }
}
