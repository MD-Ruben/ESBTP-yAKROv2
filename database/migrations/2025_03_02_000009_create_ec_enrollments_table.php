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
        Schema::create('ec_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('element_constitutif_id')->constrained('element_constitutifs')->onDelete('cascade');
            $table->foreignId('ue_enrollment_id')->nullable()->constrained('ue_enrollments')->onDelete('cascade');
            $table->string('academic_year'); // Année académique (ex: 2023-2024)
            $table->string('semester')->nullable(); // Semestre (S1, S2, etc.)
            $table->enum('status', ['inscrit', 'validé', 'ajourné', 'défaillant'])->default('inscrit');
            $table->decimal('final_grade', 5, 2)->nullable(); // Note finale sur 20
            $table->boolean('is_retaking')->default(false); // Si c'est une réinscription
            $table->text('comments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['student_id', 'element_constitutif_id', 'academic_year', 'semester'], 'student_ec_unique');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_enrollments');
    }
}; 