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
        Schema::create('teacher_element_constitutif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('element_constitutif_id')->constrained('element_constitutifs')->onDelete('cascade');
            $table->string('role')->default('intervenant'); // responsable, intervenant
            $table->integer('hours')->default(0); // Nombre d'heures attribuées
            $table->string('type')->nullable(); // CM, TD, TP
            $table->string('academic_year')->nullable(); // Année académique (ex: 2023-2024)
            $table->string('semester')->nullable(); // Semestre (S1, S2, etc.)
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['teacher_id', 'element_constitutif_id', 'type', 'academic_year', 'semester'], 'teacher_ec_unique');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_element_constitutif');
    }
}; 