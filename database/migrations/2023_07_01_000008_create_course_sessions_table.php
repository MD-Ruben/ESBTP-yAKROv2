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
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('element_constitutif_id')->comment('EC concerné')
                  ->constrained('element_constitutifs')->cascadeOnDelete();
                  
            $table->foreignId('teacher_id')->nullable()->comment('Enseignant qui donne le cours')
                  ->constrained('teachers')->nullOnDelete();
                  
            $table->foreignId('classroom_id')->nullable()->comment('Salle de classe')
                  ->constrained('classrooms')->nullOnDelete();
                  
            $table->date('date')->comment('Date de la séance');
            $table->time('start_time')->comment('Heure de début');
            $table->time('end_time')->comment('Heure de fin');
            $table->string('type')->comment('Type de séance (CM, TD, TP)');
            $table->string('title')->nullable()->comment('Titre de la séance');
            $table->text('description')->nullable()->comment('Description du contenu');
            $table->string('status')->default('planned')->comment('Statut (planifié, en cours, terminé, annulé)');
            $table->text('cancellation_reason')->nullable()->comment('Raison de l\'annulation si applicable');
            
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé la séance')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour la séance')
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
        Schema::dropIfExists('course_sessions');
    }
}; 