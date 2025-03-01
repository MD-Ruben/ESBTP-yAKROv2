<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPUnitesEnseignementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_unites_enseignement', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Crédit ECTS de l'unité d'enseignement
            $table->integer('credit')->default(3);
            
            // Relations avec les filières et niveaux
            $table->foreignId('filiere_id')->nullable()->constrained('esbtp_filieres');
            $table->foreignId('niveau_id')->nullable()->constrained('esbtp_niveau_etudes');
            
            $table->boolean('is_active')->default(true);
            
            // Traçabilité
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour améliorer les performances
            $table->index(['filiere_id', 'niveau_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_unites_enseignement');
    }
} 