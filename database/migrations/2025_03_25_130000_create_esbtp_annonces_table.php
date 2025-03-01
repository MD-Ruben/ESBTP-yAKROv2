<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPAnnoncesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        $tableExists = Schema::hasTable('esbtp_annonces');
        
        // Si la table n'existe pas, la créer
        if (!$tableExists) {
            Schema::create('esbtp_annonces', function (Blueprint $table) {
                $table->id();
                $table->string('titre');
                $table->text('contenu');
                $table->string('type')->default('globale'); // 'globale', 'classe', 'etudiant'
                $table->timestamp('date_publication')->nullable();
                $table->timestamp('date_expiration')->nullable();
                $table->integer('priorite')->default(0); // 0 = normale, 1 = importante, 2 = urgente
                $table->boolean('is_published')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
            
            \Log::info('Table esbtp_annonces créée avec succès.');
        } else {
            \Log::info('La table esbtp_annonces existe déjà.');
        }
        
        // Vérifier si la table pivot pour les annonces destinées aux classes existe déjà
        $tableAnnonceClasseExists = Schema::hasTable('esbtp_annonce_classe');
        
        // Si la table n'existe pas, la créer
        if (!$tableAnnonceClasseExists) {
            Schema::create('esbtp_annonce_classe', function (Blueprint $table) {
                $table->id();
                $table->foreignId('annonce_id')->constrained('esbtp_annonces')->onDelete('cascade');
                $table->foreignId('classe_id')->constrained('esbtp_classes')->onDelete('cascade');
                $table->timestamps();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['annonce_id', 'classe_id']);
            });
            
            \Log::info('Table esbtp_annonce_classe créée avec succès.');
        } else {
            \Log::info('La table esbtp_annonce_classe existe déjà.');
        }
        
        // Vérifier si la table pivot pour les annonces destinées aux étudiants existe déjà
        $tableAnnonceEtudiantExists = Schema::hasTable('esbtp_annonce_etudiant');
        
        // Si la table n'existe pas, la créer
        if (!$tableAnnonceEtudiantExists) {
            Schema::create('esbtp_annonce_etudiant', function (Blueprint $table) {
                $table->id();
                $table->foreignId('annonce_id')->constrained('esbtp_annonces')->onDelete('cascade');
                $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['annonce_id', 'etudiant_id']);
            });
            
            \Log::info('Table esbtp_annonce_etudiant créée avec succès.');
        } else {
            \Log::info('La table esbtp_annonce_etudiant existe déjà.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_annonce_etudiant');
        Schema::dropIfExists('esbtp_annonce_classe');
        Schema::dropIfExists('esbtp_annonces');
    }
} 