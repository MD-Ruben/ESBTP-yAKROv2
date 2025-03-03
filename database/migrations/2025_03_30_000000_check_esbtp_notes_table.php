<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CheckESBTPNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('esbtp_notes')) {
            \Log::info('La table esbtp_notes existe.');
            
            // Vérifier les colonnes
            $columns = Schema::getColumnListing('esbtp_notes');
            \Log::info('Colonnes de la table esbtp_notes: ' . implode(', ', $columns));
        } else {
            \Log::info('La table esbtp_notes n\'existe pas.');
            
            // Créer la table si elle n'existe pas
            Schema::create('esbtp_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluation_id')->constrained('esbtp_evaluations')->onDelete('cascade');
                $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
                $table->float('note', 8, 2)->nullable();
                $table->text('commentaire')->nullable();
                $table->boolean('is_absent')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
                
                // Contrainte d'unicité pour éviter les doublons
                $table->unique(['evaluation_id', 'etudiant_id']);
            });
            \Log::info('Table esbtp_notes créée.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Ne rien faire pour éviter de supprimer la table existante
    }
} 