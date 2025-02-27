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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_session_id')->constrained('course_sessions')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'excused', 'late'])->default('absent');
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->text('excuse_reason')->nullable();
            $table->boolean('has_supporting_document')->default(false);
            $table->string('supporting_document_path')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['student_id', 'course_session_id'], 'student_session_unique');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
}; 