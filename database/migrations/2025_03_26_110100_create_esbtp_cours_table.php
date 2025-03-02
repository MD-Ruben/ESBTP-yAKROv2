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
        if (!Schema::hasTable('esbtp_cours')) {
            Schema::create('esbtp_cours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('matiere_id')->constrained('esbtp_matieres');
                $table->foreignId('enseignant_id')->constrained('users');
                $table->foreignId('classe_id')->constrained('esbtp_classes');
                $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
                $table->integer('jour_semaine'); // 0 = Lundi, 1 = Mardi, etc.
                $table->time('heure_debut');
                $table->time('heure_fin');
                $table->string('salle')->nullable();
                $table->enum('type_cours', ['CM', 'TD', 'TP'])->default('CM'); // CM (Cours Magistral), TD (Travaux Dirigés), TP (Travaux Pratiques)
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('esbtp_cours');
    }
}; 