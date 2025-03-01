<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPBulletinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        $tableExists = Schema::hasTable('esbtp_bulletins');
        
        // Si la table n'existe pas, la créer
        if (!$tableExists) {
            Schema::create('esbtp_bulletins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
                $table->foreignId('classe_id')->constrained('esbtp_classes')->onDelete('cascade');
                $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires')->onDelete('cascade');
                $table->string('periode'); // 'semestre1', 'semestre2', 'annuel', etc.
                $table->timestamp('date_generation')->nullable();
                $table->float('moyenne_generale', 5, 2)->default(0);
                $table->integer('rang')->nullable();
                $table->integer('effectif_classe')->nullable();
                $table->text('appreciation')->nullable();
                $table->string('decision_conseil')->nullable();
                $table->boolean('is_published')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['etudiant_id', 'classe_id', 'annee_universitaire_id', 'periode']);
            });
            
            \Log::info('Table esbtp_bulletins créée avec succès.');
        } else {
            \Log::info('La table esbtp_bulletins existe déjà.');
        }
        
        // Vérifier si la table des résultats par matière existe déjà
        $tableResultatsExists = Schema::hasTable('esbtp_resultat_matieres');
        
        // Si la table n'existe pas, la créer
        if (!$tableResultatsExists) {
            Schema::create('esbtp_resultat_matieres', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bulletin_id')->constrained('esbtp_bulletins')->onDelete('cascade');
                $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
                $table->float('moyenne', 5, 2)->default(0);
                $table->float('coefficient', 5, 2)->default(1);
                $table->integer('rang')->nullable();
                $table->text('appreciation')->nullable();
                $table->integer('total_notes')->default(0);
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['bulletin_id', 'matiere_id']);
            });
            
            \Log::info('Table esbtp_resultat_matieres créée avec succès.');
        } else {
            \Log::info('La table esbtp_resultat_matieres existe déjà.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_resultat_matieres');
        Schema::dropIfExists('esbtp_bulletins');
    }
} 