<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompositeIndexOnNotes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('esbtp_notes', function (Blueprint $table) {
            // Add composite index for faster queries when filtering notes
            $table->index(['etudiant_id', 'semestre', 'classe_id'], 'notes_etudiant_semestre_classe_index');

            // Add index on evaluation_id for faster joins
            $table->index('evaluation_id', 'notes_evaluation_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esbtp_notes', function (Blueprint $table) {
            $table->dropIndex('notes_etudiant_semestre_classe_index');
            $table->dropIndex('notes_evaluation_index');
        });
    }
}
