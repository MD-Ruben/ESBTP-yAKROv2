<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpTeachingElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_teaching_elements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de l'ECUE
            $table->string('code')->unique(); // Code unique de l'ECUE
            $table->foreignId('teaching_unit_id')->constrained('esbtp_teaching_units'); // UE associée
            $table->text('description')->nullable(); // Description de l'ECUE
            $table->integer('hours_cm')->default(0); // Heures de cours magistraux
            $table->integer('hours_td')->default(0); // Heures de travaux dirigés
            $table->integer('hours_tp')->default(0); // Heures de travaux pratiques
            $table->integer('credits')->default(0); // Nombre de crédits de l'ECUE
            $table->integer('coefficient')->default(1); // Coefficient de l'ECUE
            $table->string('teacher_name')->nullable(); // Nom de l'enseignant responsable
            $table->string('teacher_email')->nullable(); // Email de l'enseignant
            $table->boolean('has_exam')->default(true); // Si l'ECUE a un examen final
            $table->boolean('has_continuous_assessment')->default(true); // Si l'ECUE a un contrôle continu
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->softDeletes(); // Pour la suppression logique
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_teaching_elements');
    }
}
