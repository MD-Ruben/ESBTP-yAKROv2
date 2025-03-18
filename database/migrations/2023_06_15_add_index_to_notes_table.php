<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_notes', function (Blueprint $table) {
            // Ajouter un index composite pour accélérer les requêtes de filtrage
            $table->index(['etudiant_id', 'classe_id', 'semestre'], 'notes_student_class_semester_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esbtp_notes', function (Blueprint $table) {
            $table->dropIndex('notes_student_class_semester_index');
        });
    }
}
