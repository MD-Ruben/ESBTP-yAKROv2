<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPBulletinDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        $tableExists = Schema::hasTable('esbtp_bulletin_details');
        
        // Si la table n'existe pas, la créer
        if (!$tableExists) {
            Schema::create('esbtp_bulletin_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bulletin_id')->constrained('esbtp_bulletins')->onDelete('cascade');
                $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
                $table->float('note_cc', 5, 2)->default(0)->comment('Note du contrôle continu');
                $table->float('note_examen', 5, 2)->default(0)->comment('Note de l\'examen final');
                $table->float('moyenne', 5, 2)->default(0)->comment('Moyenne générale de la matière');
                $table->float('moyenne_classe', 5, 2)->default(0)->comment('Moyenne de la classe pour cette matière');
                $table->float('coefficient', 5, 2)->default(1)->comment('Coefficient de la matière');
                $table->integer('credits')->default(0)->comment('Nombre de crédits de la matière');
                $table->integer('credits_valides')->default(0)->comment('Nombre de crédits validés');
                $table->integer('rang')->nullable()->comment('Rang de l\'étudiant dans cette matière');
                $table->integer('effectif')->nullable()->comment('Effectif de la classe pour cette matière');
                $table->string('appreciation')->nullable()->comment('Appréciation de l\'enseignant');
                $table->text('observations')->nullable()->comment('Observations supplémentaires');
                $table->timestamps();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['bulletin_id', 'matiere_id'], 'unique_bulletin_matiere');
            });
            
            \Log::info('Table esbtp_bulletin_details créée avec succès.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_bulletin_details');
        
        \Log::info('Table esbtp_bulletin_details supprimée avec succès.');
    }
} 