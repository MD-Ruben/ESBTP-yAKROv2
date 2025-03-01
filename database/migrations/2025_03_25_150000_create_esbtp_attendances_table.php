<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seance_cours_id')->constrained('esbtp_seance_cours')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
            $table->date('date');
            $table->enum('statut', ['present', 'absent', 'retard', 'excuse'])->default('absent');
            $table->text('commentaire')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Assurer qu'un étudiant n'a qu'une seule entrée de présence par séance et par date
            $table->unique(['seance_cours_id', 'etudiant_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_attendances');
    }
} 