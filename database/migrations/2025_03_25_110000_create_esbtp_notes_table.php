<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        $tableExists = Schema::hasTable('esbtp_notes');
        
        // Si la table n'existe pas, la créer
        if (!$tableExists) {
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
            
            \Log::info('Table esbtp_notes créée avec succès.');
        } else {
            \Log::info('La table esbtp_notes existe déjà.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_notes');
    }
} 