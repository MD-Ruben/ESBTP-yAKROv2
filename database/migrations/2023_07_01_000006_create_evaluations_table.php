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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Titre de l\'évaluation');
            $table->text('description')->nullable()->comment('Description de l\'évaluation');
            
            $table->foreignId('element_constitutif_id')->comment('EC évalué')
                  ->constrained('element_constitutifs')->cascadeOnDelete();
                  
            $table->string('type')->comment('Type d\'évaluation (examen, contrôle continu, projet, etc.)');
            $table->date('date')->comment('Date de l\'évaluation');
            $table->time('start_time')->comment('Heure de début');
            $table->time('end_time')->comment('Heure de fin');
            $table->string('location')->nullable()->comment('Lieu de l\'évaluation');
            $table->float('coefficient', 4, 2)->default(1.0)->comment('Coefficient dans la note finale de l\'EC');
            $table->float('max_score', 8, 2)->default(20.0)->comment('Score maximum possible');
            $table->boolean('is_published')->default(false)->comment('Si les résultats sont publiés');
            
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé l\'évaluation')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour l\'évaluation')
                  ->constrained('users')->nullOnDelete();
                  
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation many-to-many entre surveillants et évaluations
        Schema::create('evaluation_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['evaluation_id', 'user_id']);
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_supervisor');
        Schema::dropIfExists('evaluations');
    }
}; 