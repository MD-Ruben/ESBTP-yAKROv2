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
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->decimal('grade', 5, 2)->nullable(); // Note sur 20
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_excused')->default(false);
            $table->text('excuse_reason')->nullable();
            $table->boolean('has_supporting_document')->default(false);
            $table->string('supporting_document_path')->nullable();
            $table->text('feedback')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['student_id', 'evaluation_id'], 'student_evaluation_unique');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_results');
    }
}; 