<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPInscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students'); // Référence à l'étudiant
            $table->foreignId('filiere_id')->constrained('esbtp_filieres'); // Référence à la filière
            $table->foreignId('niveau_etude_id')->constrained('esbtp_niveau_etudes'); // Référence au niveau d'études
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires'); // Référence à l'année universitaire
            $table->date('inscription_date'); // Date d'inscription
            $table->string('status')->default('active'); // Statut de l'inscription (active, terminée, abandonnée, etc.)
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->timestamps();
            $table->softDeletes(); // Pour la suppression logique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_inscriptions');
    }
}
