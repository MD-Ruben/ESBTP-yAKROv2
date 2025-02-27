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
        Schema::create('element_constitutifs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Code unique de l\'EC');
            $table->string('name')->comment('Nom de l\'EC');
            $table->text('description')->nullable()->comment('Description de l\'EC');
            
            $table->foreignId('unite_enseignement_id')->comment('UE à laquelle appartient cet EC')
                  ->constrained('unite_enseignements')->cascadeOnDelete();
                  
            $table->integer('credits')->default(1)->comment('Nombre de crédits ECTS');
            $table->float('coefficient', 4, 2)->default(1.0)->comment('Coefficient dans l\'UE');
            $table->integer('hours')->default(0)->comment('Nombre d\'heures total');
            $table->integer('cm_hours')->default(0)->comment('Heures de cours magistraux');
            $table->integer('td_hours')->default(0)->comment('Heures de travaux dirigés');
            $table->integer('tp_hours')->default(0)->comment('Heures de travaux pratiques');
            
            $table->foreignId('responsable_id')->nullable()->comment('Enseignant responsable')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé l\'EC')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour l\'EC')
                  ->constrained('users')->nullOnDelete();
                  
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation many-to-many entre enseignants et EC
        Schema::create('teacher_element_constitutif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('element_constitutif_id')->constrained('element_constitutifs')->cascadeOnDelete();
            $table->string('role')->nullable()->comment('Rôle de l\'enseignant dans l\'EC');
            $table->integer('hours')->default(0)->comment('Nombre d\'heures assurées');
            $table->string('type')->nullable()->comment('Type d\'enseignement (CM, TD, TP)');
            $table->timestamps();
            
            $table->unique(['teacher_id', 'element_constitutif_id', 'type'], 'teacher_ec_type_unique');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_element_constitutif');
        Schema::dropIfExists('element_constitutifs');
    }
}; 