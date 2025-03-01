<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPFormationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        $tableExists = Schema::hasTable('esbtp_formations');
        
        // Si la table n'existe pas, la créer
        if (!$tableExists) {
            Schema::create('esbtp_formations', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nom de la formation (Formation générale, Formation technologique et professionnelle)
                $table->string('code')->unique(); // Code unique de la formation
                $table->text('description')->nullable(); // Description de la formation
                $table->boolean('is_active')->default(true); // Statut actif/inactif
                
                // Traçabilité
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                
                $table->timestamps();
                $table->softDeletes(); // Pour la suppression logique
            });
            
            // Journalisation de la création de la table
            \Log::info('Table esbtp_formations créée avec succès.');
        } else {
            // Journalisation de l'existence de la table
            \Log::info('La table esbtp_formations existe déjà.');
        }
        
        // Création de la table pivot pour la relation entre formations et filières
        if (!Schema::hasTable('esbtp_filiere_formation')) {
            Schema::create('esbtp_filiere_formation', function (Blueprint $table) {
                $table->id();
                $table->foreignId('filiere_id')->constrained('esbtp_filieres')->onDelete('cascade');
                $table->foreignId('formation_id')->constrained('esbtp_formations')->onDelete('cascade');
                $table->timestamps();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['filiere_id', 'formation_id']);
            });
        }
        
        // Création de la table pivot pour la relation entre formations et niveaux d'études
        if (!Schema::hasTable('esbtp_formation_niveau')) {
            Schema::create('esbtp_formation_niveau', function (Blueprint $table) {
                $table->id();
                $table->foreignId('formation_id')->constrained('esbtp_formations')->onDelete('cascade');
                $table->foreignId('niveau_id')->constrained('esbtp_niveau_etudes')->onDelete('cascade');
                $table->timestamps();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['formation_id', 'niveau_id']);
            });
        }
        
        // Création de la table pivot pour la relation entre formations et matières
        if (!Schema::hasTable('esbtp_formation_matiere')) {
            Schema::create('esbtp_formation_matiere', function (Blueprint $table) {
                $table->id();
                $table->foreignId('formation_id')->constrained('esbtp_formations')->onDelete('cascade');
                $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
                $table->timestamps();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['formation_id', 'matiere_id']);
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
        Schema::dropIfExists('esbtp_formation_matiere');
        Schema::dropIfExists('esbtp_formation_niveau');
        Schema::dropIfExists('esbtp_filiere_formation');
        Schema::dropIfExists('esbtp_formations');
    }
} 