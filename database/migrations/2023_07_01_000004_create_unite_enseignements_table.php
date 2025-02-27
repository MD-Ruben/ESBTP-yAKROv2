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
        Schema::create('unite_enseignements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Code unique de l\'UE');
            $table->string('name')->comment('Nom de l\'UE');
            $table->text('description')->nullable()->comment('Description de l\'UE');
            $table->integer('credits')->default(3)->comment('Nombre de crédits ECTS');
            $table->integer('hours')->default(0)->comment('Nombre d\'heures total');
            $table->integer('cm_hours')->default(0)->comment('Heures de cours magistraux');
            $table->integer('td_hours')->default(0)->comment('Heures de travaux dirigés');
            $table->integer('tp_hours')->default(0)->comment('Heures de travaux pratiques');
            
            $table->foreignId('department_id')->nullable()->comment('Département responsable')
                  ->constrained('departments')->nullOnDelete();
                  
            $table->foreignId('responsable_id')->nullable()->comment('Enseignant responsable')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé l\'UE')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour l\'UE')
                  ->constrained('users')->nullOnDelete();
                  
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation many-to-many entre parcours et UE
        Schema::create('parcours_unite_enseignement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcours_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unite_enseignement_id')->constrained('unite_enseignements')->cascadeOnDelete();
            $table->string('semester', 10)->comment('Semestre dans lequel l\'UE est enseignée');
            $table->boolean('is_optional')->default(false)->comment('Si l\'UE est optionnelle');
            $table->timestamps();
            
            $table->unique(['parcours_id', 'unite_enseignement_id', 'semester']);
        });

        // Table pivot pour la relation many-to-many entre enseignants et UE
        Schema::create('teacher_unite_enseignement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unite_enseignement_id')->constrained('unite_enseignements')->cascadeOnDelete();
            $table->string('role')->nullable()->comment('Rôle de l\'enseignant dans l\'UE');
            $table->integer('hours')->default(0)->comment('Nombre d\'heures assurées');
            $table->timestamps();
            
            $table->unique(['teacher_id', 'unite_enseignement_id']);
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_unite_enseignement');
        Schema::dropIfExists('parcours_unite_enseignement');
        Schema::dropIfExists('unite_enseignements');
    }
}; 