<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Titre du document');
            $table->text('description')->nullable()->comment('Description du document');
            $table->string('file_path')->comment('Chemin du fichier');
            $table->string('file_name')->comment('Nom original du fichier');
            $table->bigInteger('file_size')->default(0)->comment('Taille du fichier en octets');
            $table->string('file_type')->comment('Type MIME du fichier');
            
            $table->foreignId('element_constitutif_id')->nullable()->comment('EC associé (optionnel)')
                  ->constrained('element_constitutifs')->nullOnDelete();
                  
            $table->foreignId('course_session_id')->nullable()->comment('Séance de cours associée (optionnel)')
                  ->constrained('course_sessions')->nullOnDelete();
                  
            $table->foreignId('evaluation_id')->nullable()->comment('Évaluation associée (optionnel)')
                  ->constrained('evaluations')->nullOnDelete();
                  
            $table->string('visibility')->default('public')->comment('Visibilité (public, étudiants, enseignants)');
            $table->integer('download_count')->default(0)->comment('Nombre de téléchargements');
            
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé le document')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour le document')
                  ->constrained('users')->nullOnDelete();
                  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
}; 