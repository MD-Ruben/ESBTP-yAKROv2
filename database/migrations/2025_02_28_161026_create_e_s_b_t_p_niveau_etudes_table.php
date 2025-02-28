<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPNiveauEtudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_niveau_etudes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du niveau d'études (ex: Première année BTS)
            $table->string('code')->unique(); // Code unique du niveau (ex: BTS1)
            $table->string('type'); // Type de diplôme (ex: BTS)
            $table->integer('year'); // Année dans le cycle (ex: 1 pour première année)
            $table->text('description')->nullable(); // Description du niveau
            $table->boolean('is_active')->default(true); // Statut actif/inactif
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
        Schema::dropIfExists('esbtp_niveau_etudes');
    }
}
