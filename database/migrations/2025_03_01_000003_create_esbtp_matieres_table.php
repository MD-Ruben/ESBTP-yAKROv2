<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPMatieresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_matieres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Relation avec l'unité d'enseignement (UE)
            $table->foreignId('unite_enseignement_id')->nullable()->constrained('esbtp_unites_enseignement');
            
            // Valeurs par défaut pour le coefficient et le total d'heures
            $table->float('coefficient_default')->default(1.0);
            $table->integer('total_heures_default')->default(30);
            
            $table->boolean('is_active')->default(true);
            
            // Traçabilité
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation entre matières et classes (avec coefficients spécifiques)
        Schema::create('esbtp_classe_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('esbtp_classes')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            
            // Coefficients spécifiques pour cette matière dans cette classe
            $table->float('coefficient')->default(1.0);
            $table->integer('total_heures')->default(30);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['classe_id', 'matiere_id']);
        });

        // Table pivot pour la relation entre matières et filières
        Schema::create('esbtp_filiere_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiere_id')->constrained('esbtp_filieres')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['filiere_id', 'matiere_id']);
        });

        // Table pivot pour la relation entre matières et niveaux d'études
        Schema::create('esbtp_niveau_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->constrained('esbtp_niveau_etudes')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['niveau_id', 'matiere_id']);
        });

        // Table pivot pour la relation entre enseignants et matières
        Schema::create('esbtp_enseignant_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['enseignant_id', 'matiere_id', 'annee_universitaire_id'], 'enseignant_matiere_annee_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_enseignant_matiere');
        Schema::dropIfExists('esbtp_niveau_matiere');
        Schema::dropIfExists('esbtp_filiere_matiere');
        Schema::dropIfExists('esbtp_classe_matiere');
        Schema::dropIfExists('esbtp_matieres');
    }
} 