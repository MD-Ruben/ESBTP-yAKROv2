<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('esbtp_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants');
            $table->foreignId('cours_id')->nullable()->constrained('esbtp_cours');
            $table->date('date');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->boolean('justifie')->default(false);
            $table->string('motif')->nullable();
            $table->string('document_justificatif')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('esbtp_absences');
    }
}; 